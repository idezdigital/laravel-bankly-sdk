<?php

namespace Idez\Bankly\Clients;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

abstract class BaseClient
{
    protected const BASE_URL_PROD = 'bankly.com.br';
    protected const BASE_URL_SANDBOX = 'sandbox.bankly.com.br';

    protected const ENV_STAGING = 'staging';
    protected const ENV_PRODUCTION = 'production';

    public const API_VERSION = '1.0';
    protected const TOKEN_KEY = 'bankly-token';

    protected const RETRY_COUNT = 3;
    protected const RETRY_INTERVAL = 500;

    protected Collection $middlewares;

    public function __construct(array|Collection $middlewares = [])
    {
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
            ->withHeaders(['api-version' => self::API_VERSION, 'x-correlation-id' => Str::uuid()->toString()]);

        // @codeCoverageIgnoreStart
        foreach ($this->middlewares as $middleware) {
            $pendingRequest->withMiddleware($middleware);
        }
        // @codeCoverageIgnoreEnd

        return $pendingRequest;
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

    public function getCachedToken(): ?string
    {
        return Cache::get(self::TOKEN_KEY);
    }
}
