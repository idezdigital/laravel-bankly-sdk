<?php

use Idez\Bankly\Clients\BaseClient;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;

it('should throws if certificate path is null', function () {
    config(['bankly.mTls.certificate_path' => null]);
    new class () extends BaseClient {
    };
})->throws(Illuminate\Validation\ValidationException::class,);

it('should throws if private key path is null', function () {
    config(['bankly.mTls.private_key_path' => null]);
    new class () extends BaseClient {
    };
})->throws(Illuminate\Validation\ValidationException::class);

it('should throws if passphrase is null', function () {
    config(['bankly.mTls.passphrase' => null]);
    new class () extends BaseClient {
    };
})->throws(Illuminate\Validation\ValidationException::class,);


it('should throws if certificate file not exists', function () {
    config(['bankly.mTls.certificate_path' => 'batatinha.cert']);
    new class () extends BaseClient {
    };
})->throws(Illuminate\Validation\ValidationException::class);


it('should throws if private key file not exists', function () {
    config(['bankly.mTls.private_key_path' => 'batatinha.pem']);
    new class () extends BaseClient {
    };
})->throws(Illuminate\Validation\ValidationException::class);


it('should passes if passphrase is valid', function () {
    config(['bankly.mTls.passphrase' => 'cx@123aacx@123Aacx@123!a#%@123a*a()x@123aacx@123aacx@123aacx@123aa']);
    new class () extends BaseClient {
    };

    $this->expectNotToPerformAssertions();
});


it('should throws if passphrase is invalid', function (string $passphrase) {
    config(['bankly.mTls.passphrase' => $passphrase]);
    new class () extends BaseClient {
    };
})->throws(Illuminate\Validation\ValidationException::class)
    ->with([
        'a@C', // Below the minimum characters
        'abc@123aacx@123aacx@123aacx@123aacx@123aacx@123aacx@123aacx@123aa', // All lowercase
        'abcc123aacxc123aacxc123aacxc123aAcxc123aacxc123aacxc123aacxc123aa', // No symbols
        'AAAAAAAAAAAAAA@AAAAAAAAAAAAAAAAAAAAAAAAA@AAAAAAAAAAAAAAAAAAAAAAAA', // All uppercase
    ]);

it('should can authenticate with bankly and saved token in cache', function () {
    Http::fake(
        ['https://auth-mtls.sandbox.bankly.com.br/oauth2/token' => Http::response(
            [
                'token_type' => 'Bearer',
                'access_token' => 'token',
                'scope' => 'test',
                'claims' => 'company_key',
                'expires_in' => 3600,
            ],
            200
        )]
    );

    $client = new class () extends BaseClient {
    };
    $client->authenticate();
    $token = $client->getCachedToken();
    expect($token)->toBe('token');
});


it('should throw exception on try authenticate with bankly request not successfully', function () {
    Http::fake(
        ['https://auth-mtls.sandbox.bankly.com.br/oauth2/token' => Http::response(
            [
                'token_type' => 'Bearer',
                'access_token' => 'token',
                'scope' => 'test',
                'claims' => 'company_key',
                'expires_in' => 3600,
            ],
            403
        )]
    );

    $client = new class () extends BaseClient {
    };
    $client->authenticate();
})->throws(RequestException::class);

it(/**
 * @throws \Psr\SimpleCache\InvalidArgumentException
 */ /**
 * @throws \Psr\SimpleCache\InvalidArgumentException
 */ 'should returns token from cache', function () {
    $client = new class () extends BaseClient {
    };
    Cache::set('bankly-token', 'teste');

    $token = $client->getCachedToken();
    expect($token)->toBe('teste');
});

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

it('should send the correct information in the authentication request', function () {
    Http::fake(
        ['https://auth-mtls.sandbox.bankly.com.br/oauth2/token' => Http::response(
            [
                'token_type' => 'Bearer',
                'access_token' => 'token',
                'scope' => 'test',
                'claims' => 'company_key',
                'expires_in' => 200,
            ],
            200
        )]
    );

    config(['bankly.oauth2.client_id' => 'client_id']);
    config(['bankly.default_scopes' => 'scope1 scope2']);
    $client = new class () extends BaseClient {
    };
    $client->authenticate();
    Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
        return $request->hasHeader('Content-Type')
            && $request->header('Content-Type')[0] === 'application/x-www-form-urlencoded'
            && $request->header('api-version')[0] === '1.0'
            && $request->method() === 'POST'
            && $request->url() === 'https://auth-mtls.sandbox.bankly.com.br/oauth2/token'
            && blank(array_diff([
                'client_id' => 'client_id',
                'grant_type' => 'client_credentials',
                'scope' => 'scope1 scope2',
            ], $request->data()));
    });
});

it('should returns token object on Authentication', function () {
    Http::fake(
        ['https://auth-mtls.sandbox.bankly.com.br/oauth2/token' => Http::response(
            [
                'token_type' => 'Bearer',
                'access_token' => 'token',
                'scope' => 'test',
                'claims' => 'company_key',
                'expires_in' => 200,
            ],
            200
        )]
    );

    $client = new class () extends BaseClient {
    };
    Cache::set('bankly-token', null);
    $url = $client->authenticate();
    expect($url)->toBeInstanceOf(\Idez\Bankly\Data\Token::class)->access_token->toBe('token');
});

it('should returns token object on Authentication if cache filled', function () {
    Http::fake(
        ['https://auth-mtls.sandbox.bankly.com.br/oauth2/token' => Http::response(
            [
                'token_type' => 'Bearer',
                'access_token' => 'token',
                'scope' => 'test',
                'claims' => 'company_key',
                'expires_in' => 200,
            ],
            200
        )]
    );

    $client = new class () extends BaseClient {
    };
    Cache::set('bankly-token', 'teste');
    $url = $client->authenticate();
    expect($url)->toBeInstanceOf(\Idez\Bankly\Data\Token::class)
        ->access_token
        ->toBe('teste')
        ->expires_in
        ->toBeNull();
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

it('should normalize scopes', function ($scopes) {
    $client = new class () extends BaseClient {
    };
    $normalizedScopes = $client->normalizeScopes($scopes);
    expect($normalizedScopes)->toBe('events.read pix.cashout.create');
})->with([
    'array' => [['events.read', 'pix.cashout.create']],
    'string' => 'events.read pix.cashout.create',
    'collection' => collect(['events.read', 'pix.cashout.create']),
]);


it('should return true if contains scopes', function ($scopes) {
    $client = new class () extends BaseClient {
    };
    $client->setScopes($scopes);
    $contains = $client->containsScope('events.read');
    expect($contains)->toBe(true);
})->with([
    'array' => [['events.read', 'pix.cashout.create']],
    'string' => 'events.read pix.cashout.create',
    'collection' => collect(['events.read', 'pix.cashout.create']),
]);

it('should return list of scopes', function ($scopes) {
    $client = new class () extends BaseClient {
    };
    $client->setScopes($scopes);
    $returnedScopes = $client->getScopes();

    expect($returnedScopes)->toBe('events.read pix.cashout.create');
})->with([
    'array' => [['events.read', 'pix.cashout.create']],
    'string' => 'events.read pix.cashout.create',
    'collection' => collect(['events.read', 'pix.cashout.create']),
]);

it('should throw exception if there are more than 10 scopes ', function () {
    $client = new class () extends BaseClient {
    };
    $scopes = array_fill(0, 11, 'teste');
    $client->setScopes($scopes);
})->throws(InvalidArgumentException::class, 'Scopes must be less than 10');

it('should throw exception if scopes is empty ', function () {
    $client = new class () extends BaseClient {
    };
    $client->setScopes([]);
})
    ->with([[], '', collect([])])
    ->throws(InvalidArgumentException::class, 'Scopes must be a non-empty string or collection');
