<?php

use Idez\Bankly\Clients\BaseClient;

it('should returns env url if sandbox', function (string $env) {
    config(['bankly.env' => $env]);
    $client = new class () extends BaseClient {
    };
    $url = $client->getEnvUrl();
    expect($url)->toBe('sandbox.bankly.com.br');
})->with(['staging', 'local', 'testing']);

it('should returns env url if production', function () {
    config(['bankly.env' => 'production']);
    $client = new class () extends BaseClient {
    };
    $url = $client->getEnvUrl();
    expect($url)->toBe('bankly.com.br');
});

it('can push middleware on client', function () {
    $client = new class () extends BaseClient {
    };
    $clientAfterMiddleware = $client->withMiddleware(function ($request, $next) {
        $request->headers->set('teste', 'teste');

        return $next($request);
    });

    expect($clientAfterMiddleware)->toBeInstanceOf(BaseClient::class);
});
