<?php

it('should returns pix client', function () {
    $bankly = (new Idez\Bankly\Bankly())->pix();
    expect($bankly)->toBeInstanceOf(\Idez\Bankly\Clients\PixClient::class);
});

it('should returns transfer client', function () {
    $bankly = (new Idez\Bankly\Bankly())->transfer();
    expect($bankly)->toBeInstanceOf(\Idez\Bankly\Clients\TransferClient::class);
});

it('should returns account client', function () {
    $bankly = (new Idez\Bankly\Bankly())->account();
    expect($bankly)->toBeInstanceOf(\Idez\Bankly\Clients\AccountClient::class);
});

it('should returns bankslip client', function () {
    $bankly = (new Idez\Bankly\Bankly())->bankSlip();
    expect($bankly)->toBeInstanceOf(\Idez\Bankly\Clients\BankSlipClient::class);
});

it('should normalize scopes', function ($scopes) {
    $client = new Idez\Bankly\Bankly();
    $normalizedScopes = $client->normalizeScopes($scopes);
    expect($normalizedScopes)->toBe('events.read pix.cashout.create');
})->with([
    'array' => [['events.read', 'pix.cashout.create']],
    'string' => 'events.read pix.cashout.create',
    'collection' => collect(['events.read', 'pix.cashout.create']),
]);


it('should return true if contains scopes', function ($scopes) {
    $client = new Idez\Bankly\Bankly();
    $client->setScopes($scopes);
    $contains = $client->containsScope('events.read');
    expect($contains)->toBe(true);
})->with([
    'array' => [['events.read', 'pix.cashout.create']],
    'string' => 'events.read pix.cashout.create',
    'collection' => collect(['events.read', 'pix.cashout.create']),
]);

it('should return list of scopes', function ($scopes) {
    $client = new Idez\Bankly\Bankly();
    $client->setScopes($scopes);
    $returnedScopes = $client->getScopes();

    expect($returnedScopes)->toBe('events.read pix.cashout.create');
})->with([
    'array' => [['events.read', 'pix.cashout.create']],
    'string' => 'events.read pix.cashout.create',
    'collection' => collect(['events.read', 'pix.cashout.create']),
]);

it('should throw exception if there are more than 10 scopes ', function () {
    $client = new Idez\Bankly\Bankly();
    $scopes = array_fill(0, 11, 'teste');
    $client->setScopes($scopes);
})->throws(InvalidArgumentException::class, 'Scopes must be less than 10');

it('should throw exception if scopes is empty ', function () {
    $client = new Idez\Bankly\Bankly();
    $client->setScopes([]);
})
    ->with([[], '', collect([])])
    ->throws(InvalidArgumentException::class, 'Scopes must be a non-empty string or collection');

it('should can authenticate with bankly and saved token in cache', function () {
    Http::fake(
        ['https://auth.sandbox.bankly.com.br/oauth2/token' => Http::response(
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

    $client = new Idez\Bankly\Bankly();
    $client->authenticate();
    $token = cache()->get('bankly-token');
    expect($token)->toBe('token');
});


it('should throw exception on try authenticate with bankly request not successfully', function () {
    Http::fake(
        ['https://auth.sandbox.bankly.com.br/oauth2/token' => Http::response(
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

    $client = new Idez\Bankly\Bankly();
    $client->authenticate();
})->throws(\Illuminate\Http\Client\RequestException::class);
