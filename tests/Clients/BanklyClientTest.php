<?php

use Idez\Bankly\Clients\BanklyClient;
use Idez\Bankly\Exceptions\BanklyAuthenticationException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;

it('should throws if client_id is null', function () {
    config(['bankly.client' => null, 'bankly.secret' => 'test']);
    new class () extends BanklyClient {};
})->throws(BanklyAuthenticationException::class, 'Client or secret not set');

it('should throws if secret_id is null', function () {
    config(['bankly.client' => 'test', 'bankly.secret' => null]);
    new class () extends BanklyClient {};
})->throws(BanklyAuthenticationException::class, 'Client or secret not set');

it('should throws if client_id AND secret_id null', function () {
    config(['bankly.client' => null, 'bankly.secret' => null]);
    new class () extends BanklyClient {};
})->throws(BanklyAuthenticationException::class, 'Client or secret not set');

it('should can authenticate with bankly and saved token in cache', function () {
    config(['bankly.client' => 'test', 'bankly.secret' => 'test']);

    Http::fake(
        ['https://login.sandbox.bankly.com.br/connect/token' => Http::response(
            [
            'access_token' => 'token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'test',
        ],
            200
        )]
    );

    $client = new class () extends BanklyClient {};

    $token = $client->getCachedToken();
    expect($token)->toBe('token');
});


it('should throw exception on try authenticate with bankly request not successfully', function () {
    config(['bankly.client' => 'test', 'bankly.secret' => 'test']);

    Http::fake(
        ['https://login.sandbox.bankly.com.br/connect/token' => Http::response(
            [
            'access_token' => 'token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'test',
        ],
            403
        )]
    );

    $client = new class () extends BanklyClient {};
})->throws(RequestException::class);

it(/**
 * @throws \Psr\SimpleCache\InvalidArgumentException
 */ 'should returns token from cache', function () {
    config(['bankly.client' => 'test', 'bankly.secret' => 'test']);
    Http::fake(
        ['https://login.sandbox.bankly.com.br/connect/token' => Http::response(
            [
            'access_token' => 'token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'test',
        ],
            200
        )]
    );

    $client = new class () extends BanklyClient {};
    Cache::set('bankly-token', 'teste');

    $token = $client->getCachedToken();
    expect($token)->toBe('teste');
});

it('should returns env url if sandbox', function (string $env) {
    config(['bankly.env' => $env, 'bankly.client' => 'test', 'bankly.secret' => 'test']);
    Http::fake(
        ['https://login.sandbox.bankly.com.br/connect/token' => Http::response(
            [
            'access_token' => 'token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'test',
        ],
            200
        )]
    );

    $client = new class () extends BanklyClient {};
    $url = $client->getEnvUrl();
    expect($url)->toBe('sandbox.bankly.com.br');
})->with(['staging', 'local', 'testing']);

it('should returns env url if production', function () {
    config(['bankly.env' => 'production', 'bankly.client' => 'test', 'bankly.secret' => 'test']);
    Http::fake(
        ['https://login.bankly.com.br/connect/token' => Http::response(
            [
            'access_token' => 'token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'test',
        ],
            200
        )]
    );

    $client = new class () extends BanklyClient {};
    $url = $client->getEnvUrl();
    expect($url)->toBe('bankly.com.br');
});


it('should returns token object on Authentication', function () {
    config(['bankly.env' => 'production', 'bankly.client' => 'test', 'bankly.secret' => 'test']);
    Http::fake(
        ['https://login.bankly.com.br/connect/token' => Http::response(
            [
                'access_token' => 'token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
                'scope' => 'test',
            ],
            200
        )]
    );

    $client = new class () extends BanklyClient {};
    Cache::set('bankly-token', null);
    $url = $client->authentication();
    expect($url)->toBeInstanceOf(\Idez\Bankly\Structs\Token::class)->access_token->toBe('token');
});

it('should returns token object on Authentication if cache filled', function () {
    config(['bankly.env' => 'production', 'bankly.client' => 'test', 'bankly.secret' => 'test']);
    Http::fake(
        ['https://login.bankly.com.br/connect/token' => Http::response(
            [
                'access_token' => 'token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
                'scope' => 'test',
            ],
            200
        )]
    );

    $client = new class () extends BanklyClient {};
    Cache::set('bankly-token', 'teste');
    $url = $client->authentication();
    expect($url)->toBeInstanceOf(\Idez\Bankly\Structs\Token::class)
        ->access_token
        ->toBe('teste')
        ->expires_in
        ->toBeNull();
});

it('should send the correct information in the authentication request', function () {
    config(['bankly.env' => 'production', 'bankly.client' => 'test', 'bankly.secret' => 'test', 'bankly.default_scopes' => 'scope1 scope2']);
    Http::fake(
        ['https://login.bankly.com.br/connect/token' => Http::response(
            [
                'access_token' => 'token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
                'scope' => 'test',
            ],
            200
        )]
    );
    new class () extends BanklyClient {};
    Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
        return $request->hasHeader('Content-Type')
            && $request->header('Content-Type')[0] === 'application/x-www-form-urlencoded'
            && $request->method() === 'POST'
            && $request->url() === 'https://login.bankly.com.br/connect/token'
            && blank(array_diff([
                'grant_type' => 'client_credentials',
                'client_id' => 'test',
                'client_secret' => 'test',
                'scope' => 'scope1 scope2',
            ], $request->data()));
    });
});
