<?php

namespace Idez\Bankly\Clients;

use Idez\Bankly\Exceptions\BanklyAuthenticationException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

abstract class BanklyClient
{
    protected const BASE_URL_PROD = 'bankly.com.br';
    protected const BASE_URL_STAGING = 'sandbox.bankly.com.br';

    protected const ENV_STAGING = 'staging';
    protected const EVN_PRODUCTION = 'production';

    protected const TOKEN_KEY = 'bankly-token';
    public const API_VERSION = '1.0';

    protected const RETRY_COUNT = 3;
    protected const RETRY_INTERVAL = 500;

    protected string $baseURL;
    protected string $token;

    public function __construct(
        protected null|string $client = '',
        protected null|string $secret = '',
        protected null|string $scopes = null,
        protected null|string $env = self::ENV_STAGING
    ) {
        $this->authentication();
    }

    /**
     * @return void
     * @throws BanklyAuthenticationException
     * @deprecated
     */
    public function authentication(): void
    {
        $cachedToken = $this->getCachedToken();

        if (blank($cachedToken) && filled($this->client) && filled($this->secret)) {
            $formRequest = [
                'grant_type' => 'client_credentials',
                'client_id' => $this->client,
                'client_secret' => $this->secret,
                'scope' => $this->scopes ?? Config::get('bankly.default_scopes'),
            ];


            /** @var Response $auth */
            $auth = Http::baseUrl('https://login.' . $this->baseURL)
                ->asForm()
                ->retry(self::RETRY_COUNT, self::RETRY_INTERVAL)
                ->post('/connect/token', $formRequest);

            if ($auth->failed()) {
                throw new BanklyAuthenticationException("Unable to authenticate at Bankly.");
            }

            $authObject = $auth->object();
            $cachedToken = $authObject->access_token;

            Cache::put(self::TOKEN_KEY, $cachedToken, $authObject->expires_in * 0.8);
        }

        $this->token = $cachedToken;
    }

    public function getUrl(): string
    {
        return $this->baseURL = $this->env === self::ENV_STAGING ? self::BASE_URL_STAGING : self::BASE_URL_PROD;
    }

    public function getCachedToken(): ?string
    {
        return Cache::get(self::TOKEN_KEY);
    }

    /**
     * @return mixed
     */
    protected function client(): PendingRequest
    {
        return Http::baseUrl('https://api.' . $this->baseURL)
            ->withToken($this->token)
            ->retry(self::RETRY_COUNT, self::RETRY_INTERVAL)
            ->withHeaders(['api-version' => self::API_VERSION]);
    }
}
