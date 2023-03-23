<?php

namespace Idez\Bankly;

use Exception;
use Idez\Bankly\Clients\AccountClient;
use Idez\Bankly\Clients\BankSlipClient;
use Idez\Bankly\Clients\PixClient;
use Idez\Bankly\Clients\TransferClient;
use Idez\Bankly\Clients\TedClient;
use Idez\Bankly\Data\Token;
use Idez\Bankly\Rules\FileExistsRule;
use Idez\Bankly\Traits\HasScopes;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Bankly
{
    use HasScopes;

    public const ACESSO_ISPB = '13140088';
    public const ACESSO_COMPE = '332';
    public const ACESSO_NAME = 'Acesso Soluções de Pagamentos S.A';

    protected const BASE_URL_PROD = 'bankly.com.br';
    protected const BASE_URL_SANDBOX = 'sandbox.bankly.com.br';

    protected const ENV_STAGING = 'staging';
    protected const ENV_PRODUCTION = 'production';

    public const API_VERSION = '1.0';
    protected const TOKEN_KEY = 'bankly-token';

    public function __construct(
        private string|null                  $certificatePath = null,
        private string|null                  $privatePath = null,
        private string|null                  $passphrase = null,
        private array|string|Collection|null $scopes = null,
        private array|Collection             $middlewares = [],
    ) {
        $this->certificatePath ??= config('bankly.mTls.certificate_path');
        $this->privatePath ??= config('bankly.mTls.private_key_path');
        $this->passphrase ??= config('bankly.mTls.passphrase');
        $this->scopes = $this->normalizeScopes($scopes ?? config('bankly.default_scopes'));
        $this->middlewares = collect($middlewares);

        Validator::make(
            [
                'certificate' => $this->certificatePath,
                'private' => $this->privatePath,
                'passphrase' => $this->passphrase,
            ],
            [
                'certificate' => ['required', new FileExistsRule()],
                'private' => ['required', new FileExistsRule()],
                'passphrase' => [Password::min(64)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                ],
            ]
        )->validate();
    }

    public function account(): AccountClient
    {
        return new AccountClient($this->middlewares);
    }

    public function bankSlip(): BankSlipClient
    {
        return new BankSlipClient($this->middlewares);
    }

    public function pix(): PixClient
    {
        return new PixClient($this->middlewares);
    }

    public function transfer(): TransferClient
    {
        return new TransferClient($this->middlewares);
    }

    public function ted(): TedClient
    {
        return new TedClient($this->middlewares);
    }

    public function getEnvUrl(): string
    {
        return config('bankly.env') === self::ENV_PRODUCTION ? self::BASE_URL_PROD : self::BASE_URL_SANDBOX;
    }

    /**
     * @return Token access token
     * @throws RequestException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function authenticate(): Token
    {
        $cachedToken = cache()->get(self::TOKEN_KEY);

        $baseUrl = "https://auth.{$this->getEnvUrl()}";

        if (blank($cachedToken)) {
            $request = Http::baseUrl($baseUrl)
                ->withHeaders(['api-version' => self::API_VERSION])
                ->withOptions($this->getCerts());

            // @codeCoverageIgnoreStart
            foreach ($this->middlewares as $middleware) {
                $request->withMiddleware($middleware);
            }
            // @codeCoverageIgnoreEnd

            $auth = $request
                ->asForm()
                ->post('/oauth2/token', [
                    'client_id' => config('bankly.oauth2.client_id'),
                    'grant_type' => 'client_credentials',
                    'scope' => $this->scopes,
                ])
                ->throw();

            $authObject = new Token($auth->json());
            $cachedToken = $authObject->access_token;

            cache()->put(self::TOKEN_KEY, $cachedToken, $this->calculateExpiresIn($authObject->expires_in));
        }

        return $authObject ?? new Token(['access_token' => $cachedToken]);
    }

    /**
     * @param int $time
     * @return int
     */
    private function calculateExpiresIn(int $time): int
    {
        return intval(floor($time ?? 0 * 0.8));
    }

    /**
     * @return array[]
     */
    private function getCerts(): array
    {
        return [
            'cert' => [$this->certificatePath, $this->passphrase],
            'ssl_key' => [$this->privatePath, $this->passphrase],
        ];
    }
}
