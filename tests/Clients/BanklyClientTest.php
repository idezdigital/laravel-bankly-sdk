<?php

use Idez\Bankly\Clients\BanklyClient;
use Idez\Bankly\Exceptions\BanklyAuthenticationException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;

it('should throws if client_id is null', function () {
    config(['bankly.client' => null, 'bankly.secret' => 'test']);
    new class Extends BanklyClient{};
})->throws(BanklyAuthenticationException::class, 'Client or secret not set');

it('should throws if secret_id is null', function () {
    config(['bankly.client' => 'test', 'bankly.secret' => null]);
    new class Extends BanklyClient{};
})->throws(BanklyAuthenticationException::class, 'Client or secret not set');

it('should throws if client_id AND secret_id null', function () {
    config(['bankly.client' => null, 'bankly.secret' => null]);
    new class Extends BanklyClient{};
})->throws(BanklyAuthenticationException::class, 'Client or secret not set');

it('should can authenticate with bankly and saved token in cache', function () {
    config(['bankly.client' => 'test', 'bankly.secret' => 'test']);

    Http::fake(
        ['https://login.sandbox.bankly.com.br/connect/token' => Http::response([
            'access_token' => 'token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'test',
        ],200
        )]);

    $client = new class Extends BanklyClient{};
    $client->authentication();

    $token =  $client->getCachedToken();
    expect($token)->toBe('token');
});


it('should throw exception on try authenticate with bankly request not successfully', function () {
    config(['bankly.client' => 'test', 'bankly.secret' => 'test']);

    Http::fake(
        ['https://login.sandbox.bankly.com.br/connect/token' => Http::response([
            'access_token' => 'token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'test',
        ],403
        )]);

    $client = new class Extends BanklyClient{};
    $client->authentication();
})->throws(RequestException::class);

it(/**
 * @throws \Psr\SimpleCache\InvalidArgumentException
 */ 'should returns token from cache', function () {
    config(['bankly.client' => 'test', 'bankly.secret' => 'test']);
    Http::fake(
        ['https://login.sandbox.bankly.com.br/connect/token' => Http::response([
            'access_token' => 'token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'test',
        ],200
    )]);

    $client = new class Extends BanklyClient{};
    Cache::set('bankly-token', 'teste');

    $token =  $client->getCachedToken();
    expect($token)->toBe('teste');
});

it( 'should returns env url if sandbox', function (string $env) {
    config(['bankly.env' => $env, 'bankly.client' => 'test', 'bankly.secret' => 'test']);
    Http::fake(
        ['https://login.sandbox.bankly.com.br/connect/token' => Http::response([
            'access_token' => 'token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'test',
        ],200
        )]);

    $client = new class Extends BanklyClient{};
    $url =  $client->getEnvUrl();
    expect($url)->toBe('sandbox.bankly.com.br');
})->with(['staging', 'local', 'testing']);

it( 'should returns env url if production', function () {
    config(['bankly.env' => 'production', 'bankly.client' => 'test', 'bankly.secret' => 'test']);
    Http::fake(
        ['https://login.bankly.com.br/connect/token' => Http::response([
            'access_token' => 'token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'test',
        ],200
        )]);

    $client = new class Extends BanklyClient{};
    $url =  $client->getEnvUrl();
    expect($url)->toBe('bankly.com.br');
});
