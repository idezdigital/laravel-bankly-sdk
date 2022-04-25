<?php

namespace Idez\Bankly\Clients;

use Idez\Bankly\Exceptions\BanklyAuthenticationException;
use Idez\Bankly\Exceptions\BanklyRegistrationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class BanklyMTLSClient
{
    protected const BASE_URL_PROD = 'bankly.com.br';
    protected const BASE_URL_SANDBOX = 'sandbox.bankly.com.br';

    protected const ENV_STAGING = 'staging';
    protected const ENV_PRODUCTION = 'production';

    public const API_VERSION = '1.0';
    protected const CACHE_KEY = 'bankly_mTls_client';

    protected const RETRY_COUNT = 3;
    protected const RETRY_INTERVAL = 500;

    protected string $baseUrl;
    protected Collection $middlewares;
    protected string $scopes;

    public function __construct(
        private string|null $certificatePath = null,
        private string|null $privatePath = null,
        private string|null $password = null,
        array|string|Collection|null $scopes = null,
        array|Collection $middlewares = []
    )
    {
        $this->certificatePath ??= config('bankly.mTls.certificate_path');
        $this->privatePath ??= config('bankly.mTls.private_key_path');
        $this->password ??= config('bankly.mTls.password');
        $this->scopes = $this->normalizeScopes($scopes ?? config('bankly.default_scopes'));
        $this->middlewares = collect($middlewares);
        $this->baseUrl = $this->getUrl();

        Validator::make(
            [
                'certificate' => $certificatePath,
                'private' => $privatePath,
                'password' => $password,
            ],
            [
                'certificate' => 'required|file',
                'private' => 'required|file',
                'password' => [Password::min(64)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                ],
            ]
        )->validate();
    }

    public function getUrl(): string
    {
        return config('bankly.env') === self::ENV_PRODUCTION ? self::BASE_URL_PROD : self::BASE_URL_SANDBOX;
    }

    /**
     * @return PendingRequest
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function client(): PendingRequest
    {
        $cachedToken = cache()->get(self::CACHE_KEY);

        return Http::baseUrl('https://api.' . $this->getUrl())
            ->withToken($cachedToken)
            ->retry(self::RETRY_COUNT, self::RETRY_INTERVAL)
            ->withHeaders(['api-version' => self::API_VERSION]);
    }


    /**
     * @return string access token
     * @throws BanklyAuthenticationException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function authentication(): string
    {
        $cachedToken = cache()->get(self::CACHE_KEY);

        if (blank($cachedToken)) {
            $auth = Http::baseUrl("https://auth-mtls.{$this->baseUrl}")
                ->pushHandlers($this->middlewares)
                ->withHeaders(['api-version' => self::API_VERSION])
                ->withOptions($this->getCerts())
                ->retry(self::RETRY_COUNT, self::RETRY_INTERVAL)
                ->asForm()
                ->post('/oauth2/token', [
                    'client_id' => config('bankly.oauth2.client_id'),
                    'grant_type' => 'client_credentials',
                    'scope' => $this->scopes
                ]);

            if ($auth->failed()) {
                throw new BanklyAuthenticationException("Unable to authenticate at Bankly.");
            }

            $authObject = $auth->object();
            $cachedToken = $authObject->access_token;

            cache()->put(self::CACHE_KEY, $cachedToken, $authObject->expires_in * 0.8);
        }


        return $cachedToken;
    }


    #[ArrayShape(['cert' => "array", 'ssl_key' => "array"])]
    private function getCerts(): array
    {
        return [
            'cert' => [$this->certificatePath, $this->password],
            'ssl_key' => [$this->privatePath, $this->password],
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

    public function setScopes(array|string|Collection|null $scopes): self
    {
        $this->scopes = $this->normalizeScopes($scopes);
        return $this;
    }

    public function normalizeScopes(array|string|Collection $scopes): string
    {
        if (is_string($scopes)) {
            return $scopes;
        }

        return collect($scopes)->implode(' ');
    }

    /**
     * @throws BanklyRegistrationException
     */
    public function register(): object
    {
        $register = Http::baseUrl('https://auth-mtls.' . $this->baseUrl)
            ->retry(self::RETRY_COUNT, self::RETRY_INTERVAL)
            ->withOptions($this->getCerts())
//            ->pushHandlers($this->middlewares)
            ->post('/oauth2/register', [
                "grant_types" => ["client_credentials"],
                "tls_client_auth_subject_dn" => config('bankly.oauth2.subject_dn'),
                "token_endpoint_auth_method" => "tls_client_auth",
                "response_types" => ["access_token"],
                "company_key" => config('bankly.company_key'),
                "scope" => $this->scopes,
            ]);

        if ($register->failed()) {
            throw new BanklyRegistrationException("Unable to authenticate at Bankly.");
        }

        return $register->object();
    }
}
