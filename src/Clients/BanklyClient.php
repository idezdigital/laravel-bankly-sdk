<?php

namespace Idez\Bankly\Clients;

use Idez\Bankly\Exceptions\AuthenticationException;
use Idez\Bankly\Resources\Token;
use Idez\Bankly\Rules\FileExistsRule;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

abstract class BanklyClient
{
    protected const BASE_URL_PROD = 'bankly.com.br';
    protected const BASE_URL_SANDBOX = 'sandbox.bankly.com.br';

    protected const ENV_STAGING = 'staging';
    protected const ENV_PRODUCTION = 'production';

    public const API_VERSION = '1.0';
    protected const TOKEN_KEY = 'bankly-token';

    protected const RETRY_COUNT = 3;
    protected const RETRY_INTERVAL = 500;

    protected string $baseUrl;
    protected Collection $middlewares;
    protected string $scopes;

    /**
     * @throws RequestException
     * @throws AuthenticationException
     */
    public function __construct(
        private string|null          $certificatePath = null,
        private string|null          $privatePath = null,
        private string|null          $passphrase = null,
        array|string|Collection|null $scopes = null,
        array|Collection             $middlewares = [],
        bool $authenticate = true
    ) {
        $this->certificatePath ??= config('bankly.mTls.certificate_path');
        $this->privatePath ??= config('bankly.mTls.private_key_path');
        $this->passphrase ??= config('bankly.mTls.passphrase');
        $this->scopes = $this->normalizeScopes($scopes ?? config('bankly.default_scopes'));
        $this->middlewares = collect($middlewares);
        $this->baseUrl = $this->getEnvUrl();

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

        if (is_null($this->certificatePath) || is_null($this->privatePath) || is_null($this->passphrase)) {
            throw new AuthenticationException('Certificate, private key and password are required.');
        }

        if ($authenticate) {
            $this->authenticate();
        }
    }

    public function getEnvUrl(): string
    {
        return config('bankly.env') === self::ENV_PRODUCTION ? self::BASE_URL_PROD : self::BASE_URL_SANDBOX;
    }

    /**
     * @return PendingRequest
     */
    protected function client(): PendingRequest
    {
        $cachedToken = $this->getCachedToken();

        $pendingRequest = Http::baseUrl('https://api.' . $this->getEnvUrl())
            ->withToken($cachedToken)
            ->retry(self::RETRY_COUNT, self::RETRY_INTERVAL)
            ->withHeaders(['api-version' => self::API_VERSION, 'x-correlation-id' => Str::uuid()->toString()])
            ->throw();

        foreach ($this->middlewares as $middleware) {
            $pendingRequest->withMiddleware($middleware);
        }

        return $pendingRequest;
    }

    /**
     * @return Token access token
     * @throws RequestException
     */
    public function authenticate(): Token
    {
        $cachedToken = $this->getCachedToken();

        if (blank($cachedToken)) {
            $request = Http::baseUrl("https://auth-mtls.{$this->baseUrl}")
                ->withHeaders(['api-version' => self::API_VERSION])
                ->withOptions($this->getCerts());

            foreach ($this->middlewares as $middleware) {
                $request->withMiddleware($middleware);
            }

            $auth = $request
                ->retry(self::RETRY_COUNT, self::RETRY_INTERVAL)
                ->asForm()
                ->post('/oauth2/token', [
                    'client_id' => config('bankly.oauth2.client_id'),
                    'grant_type' => 'client_credentials',
                    'scope' => $this->scopes,
                ])
                ->throw();

            $authObject = new Token($auth->json());
            $cachedToken = $authObject->access_token;
            cache()->put(self::TOKEN_KEY, $cachedToken, $authObject->expires_in * 0.8);
        }

        return $authObject ?? new Token(['access_token' => $cachedToken]);
    }

    #[ArrayShape(['cert' => "array", 'ssl_key' => "array"])]
    private function getCerts(): array
    {
        return [
            'cert' => [$this->certificatePath, $this->passphrase],
            'ssl_key' => [$this->privatePath, $this->passphrase],
        ];
    }

    /**
     * @param callable ...$middleware
     * @return $this
     */
    public function withMiddleware(callable ...$middleware): self
    {
        $this->middlewares->push($middleware);

        return $this;
    }

    /**
     * @param array|string|Collection|null $scopes
     * @return $this
     */
    public function setScopes(array|string|Collection|null $scopes): self
    {
        $this->scopes = $this->normalizeScopes($scopes);

        return $this;
    }

    public function getCachedToken(): ?string
    {
        return Cache::get(self::TOKEN_KEY);
    }

    public function getScopes(): string
    {
        return $this->scopes;
    }

    public function containsScope(string $scope): bool
    {
        return Str::contains($this->scopes, $scope);
    }

    /**
     * @param array|string|Collection $scopes
     * @return string
     * @throws InvalidArgumentException
     */
    public function normalizeScopes(array|string|Collection $scopes): string
    {
        if (is_string($scopes)) {
            $scopes = explode(' ', $scopes);
        }

        $scopes = collect($scopes);

        if ($scopes->isEmpty()) {
            throw new InvalidArgumentException('Scopes must be a non-empty string or collection');
        }

        if ($scopes->count() > 10) {
            throw new InvalidArgumentException('Scopes must be less than 10');
        }

        return $scopes->implode(' ');
    }
}
