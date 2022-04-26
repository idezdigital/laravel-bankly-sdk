<?php

namespace Idez\Bankly\Clients;

use Idez\Bankly\Exceptions\BanklyAuthenticationException;
use Idez\Bankly\Resources\Token;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * @deprecated
 */
abstract class BanklyClient
{
    protected const BASE_URL_PROD = 'bankly.com.br';
    protected const BASE_URL_SANDBOX = 'sandbox.bankly.com.br';

    protected const ENV_STAGING = 'staging';
    protected const EVN_PRODUCTION = 'production';

    protected const TOKEN_KEY = 'bankly-token';
    public const API_VERSION = '1.0';

    protected const RETRY_COUNT = 3;
    protected const RETRY_INTERVAL = 500;

    protected string $baseURL;
    protected string $token;

    protected null|string $env;

    /**
     * @throws BanklyAuthenticationException
     */
    public function __construct(
        protected null|string $client = null,
        protected null|string $secret = null,
        protected array|Collection|null|string $scopes = null,
        bool $authenticate = true
    ) {
        $this->client ??= config('bankly.client');
        $this->secret ??= config('bankly.secret');
        $this->scopes ??= config('bankly.default_scopes');
        $this->env = config('bankly.env');

        if (is_null($this->client) || is_null($this->secret)) {
            throw new BanklyAuthenticationException('Client or secret not set');
        }

        if ($authenticate) {
            $this->authenticate();
        }
    }

    /**
     * @return mixed
     */
    protected function client(): PendingRequest
    {
        return Http::baseUrl('https://api.' . $this->getEnvUrl())
            ->withToken($this->token)
            ->retry(self::RETRY_COUNT, self::RETRY_INTERVAL)
            ->withHeaders(['api-version' => self::API_VERSION]);
    }

    /**
     * @return Token
     * @throws RequestException
     */
    public function authenticate(): Token
    {
        $cachedToken = $this->getCachedToken();

        if (blank($cachedToken)) {
            $formRequest = [
                'grant_type' => 'client_credentials',
                'client_id' => $this->client,
                'client_secret' => $this->secret,
                'scope' => $this->scopes ?? config('bankly.default_scopes'),
            ];

            /** @var Response $auth */
            $auth = Http::baseUrl('https://login.' . $this->getEnvUrl())
                ->asForm()
                ->retry(self::RETRY_COUNT, self::RETRY_INTERVAL)
                ->post('/connect/token', $formRequest)
                ->throw();

            $authObject = new Token($auth->json());
            $cachedToken = $authObject->access_token;

            Cache::put(self::TOKEN_KEY, $cachedToken, $authObject->expires_in * 0.8);
        }

        $this->token = $cachedToken;

        return $authObject ?? new Token(['access_token' => $cachedToken]);
    }

    public function getEnvUrl(): string
    {
        return $this->baseURL = $this->env === self::EVN_PRODUCTION ? self::BASE_URL_PROD : self::BASE_URL_SANDBOX;
    }

    public function getCachedToken(): ?string
    {
        return Cache::get(self::TOKEN_KEY);
    }
}
