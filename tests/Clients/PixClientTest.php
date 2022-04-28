<?php

use Idez\Bankly\Clients\BaseClient;
use Idez\Bankly\Clients\PixClient;
use Idez\Bankly\Data\Pix\DictKey;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

it('should PixClient be a children of BaseClient', function () {
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

    $client = new PixClient();
    $this->assertInstanceOf(BaseClient::class, $client);
});

it('should create qrcode and returns base64', function () {
    $encoded = base64_encode(Str::random(10));

    Http::fake([
        'https://api.sandbox.bankly.com.br/pix/qrcodes' => Http::response(['encodedValue' => $encoded], 200),
    ]);

    $pix = new PixClient(authenticate: false);
    $keyValue = Str::uuid()->toString();
    $conciliationId = Str::random();
    $name = \Pest\Faker\faker()->company;
    $city = 'São Paulo';
    $zip = \Pest\Faker\faker('pt_BR')->postcode;

    $staticQrCode = $pix->createStaticQrCode(
        keyType: 'evp',
        keyValue: $keyValue,
        amount: 100.00,
        conciliationId: $conciliationId,
        recipientName: $name,
        locationCity: $city,
        locationZip: $zip,
        singlePayment: false
    );

    expect($staticQrCode)
         ->toBeInstanceOf(\Idez\Bankly\Data\Pix\StaticQrCode::class)
         ->encodedValue
         ->toBeBase64()
         ->toBe($encoded);


    $data = [
        'addressingKey' => [
            'type' => 'evp',
            'value' => $keyValue,
        ],
        'conciliationId' => $conciliationId,
        'amount' => 100.00,
        'recipientName' => Str::of($name)->ascii()->replace(['.', "'", "-", "_"], "")->__toString(),
        'singlePayment' => false,
        'location' => [
            'city' => Str::of($city)->ascii()->replace(['.', "'", "-", "_"], "")->__toString(),
            'zipCode' => $zip,
        ],
    ];

    Http::assertSent(function (\Illuminate\Http\Client\Request $request) use ($data) {
        return $data === $request->data();
    });
});

it('should execute pix and returns pix transfer with dictkey info', function () {
    $encoded = base64_encode(Str::random(10));
    $from = \Idez\Bankly\Data\Account::factory()->make();
    $to = DictKey::factory()->make();

    Http::fake([
        'https://api.sandbox.bankly.com.br/pix/cash-out' => Http::response(
            [
                'amount' => 100.00,
                'withdrawalAmount' => 0.0,
                'changeAmount' => 0.0,
                'description' => 'test',
                'sender' => [
                    'documentType' => 'CNPJ',
                    'account' => [
                        'branch' => '0001',
                        'number' => '22750878',
                        'type' => 'CHECKING',
                    ],
                    'bank' => [
                        'ispb' => '13140088',
                        'compe' => '332',
                        'name' => 'Acesso Soluções de Pagamento S.A.',
                    ],
                    'documentNumber' => '14110585000107',
                    'name' => 'Quatro',
                ],
                'recipient' => [
                    'documentType' => 'CNPJ',
                    'account' => [
                        'branch' => '3395',
                        'number' => '745065',
                        'type' => 'CHECKING',
                    ],
                    'bank' => [
                        'ispb' => '60746948',
                        'compe' => '237',
                        'name' => 'Bco Bradesco S.A.',
                    ],
                    'documentNumber' => '00422351000603',
                    'name' => 'Loja Exemplo',
                ],
                'authenticationCode' => 'dbbfd512-ede4-4841-9fce-bfbc70bfb4ef',

            ],
            200
        ),
    ]);

    $client = new PixClient(authenticate: false);


    $transfer = $client->executePix(
        from: $from,
        to: $to,
        amount: 100.00,
        description: 'test'
    );

    expect($transfer)
        ->toBeInstanceOf(\Idez\Bankly\Data\Pix\Transfer::class)
        ->amount
        ->toBe(100.00)
        ->description
        ->toBe('test')
        ->authenticationCode
        ->toBe('dbbfd512-ede4-4841-9fce-bfbc70bfb4ef');
});

it('should execute pix and returns pix transfer', function () {
    $encoded = base64_encode(Str::random(10));
    $from = \Idez\Bankly\Data\Account::factory()->make();
    $to = \Idez\Bankly\Data\Account::factory()->make();

    Http::fake([
        'https://api.sandbox.bankly.com.br/pix/cash-out' => Http::response(
            [
                'amount' => 100.00,
                'withdrawalAmount' => 0.0,
                'changeAmount' => 0.0,
                'description' => 'test',
                'sender' => [
                    'documentType' => 'CNPJ',
                    'account' => [
                        'branch' => '0001',
                        'number' => '22750878',
                        'type' => 'CHECKING',
                    ],
                    'bank' => [
                        'ispb' => '13140088',
                        'compe' => '332',
                        'name' => 'Acesso Soluções de Pagamento S.A.',
                    ],
                    'documentNumber' => '14110585000107',
                    'name' => 'Quatro',
                ],
                'recipient' => [
                    'documentType' => 'CNPJ',
                    'account' => [
                        'branch' => '3395',
                        'number' => '745065',
                        'type' => 'CHECKING',
                    ],
                    'bank' => [
                        'ispb' => '60746948',
                        'compe' => '237',
                        'name' => 'Bco Bradesco S.A.',
                    ],
                    'documentNumber' => '00422351000603',
                    'name' => 'Loja Exemplo',
                ],
                'authenticationCode' => 'dbbfd512-ede4-4841-9fce-bfbc70bfb4ef',

        ],
            200
        ),
    ]);

    $client = new PixClient(authenticate: false);


    $transfer = $client->executePix(
        from: $from,
        to: $to,
        amount: 100.00,
        description: 'test'
    );

    expect($transfer)
        ->toBeInstanceOf(\Idez\Bankly\Data\Pix\Transfer::class)
        ->amount
        ->toBe(100.00)
        ->description
        ->toBe('test')
        ->authenticationCode
        ->toBe('dbbfd512-ede4-4841-9fce-bfbc70bfb4ef');
});

it('should refund pix and return object', function () {
    $authenticationCode = Str::uuid()->toString();
    Http::fake([
        'https://api.sandbox.bankly.com.br/baas/pix/cash-out:refund' => Http::response(
            [
                'authenticationCode' => $authenticationCode,
                'amount' => 100.00,
                'description' => 'test',
                'correlationId' => Str::random(),
                'status' => 'CREATED',
                "createdAt" => now()->toISOString(),
                "updatedAt" => now()->toISOString(),
                'sender' => [
                    'documentType' => 'CNPJ',
                    'account' => [
                        'branch' => '0001',
                        'number' => '22750878',
                        'type' => 'CHECKING',
                    ],
                    'bank' => [
                        'ispb' => '13140088',
                        'compe' => '332',
                        'name' => 'Acesso Soluções de Pagamento S.A.',
                    ],
                    'documentNumber' => '14110585000107',
                    'name' => 'Quatro',
                ],
                'recipient' => [
                    'documentType' => 'CNPJ',
                    'account' => [
                        'branch' => '3395',
                        'number' => '745065',
                        'type' => 'CHECKING',
                    ],
                    'bank' => [
                        'ispb' => '60746948',
                        'compe' => '237',
                        'name' => 'Bco Bradesco S.A.',
                    ],
                    'documentNumber' => '00422351000603',
                    'name' => 'Loja Exemplo',
                ],
            ],
            200
        ),
    ]);

    $client = new PixClient(scopes: ['events.read, pix.cashout.create'], authenticate: false);
    $refundPix = $client
        ->refundPix(
            from: \Idez\Bankly\Data\Account::make([
            'branch' => '3395',
            'number' => '745065',
            'type' => 'CHECKING',
        ]),
            authenticationCode: $authenticationCode,
            amount: 100.00,
        );

    expect($refundPix)
        ->toBeInstanceOf(\Idez\Bankly\Data\Refund::class)
        ->amount
        ->toBe(100.00)
        ->description
        ->toBe('test')
        ->authenticationCode
        ->toBe($authenticationCode);
});

it('should search dict key and return object', function (string $key) {
    $cleanKey = \Idez\Bankly\Support\Dict::cleanMask($key);

    Http::fake([
       "https://api.sandbox.bankly.com.br/baas/pix/entries/{$cleanKey}" => Http::response(json_decode('{
        "endToEndId": "8e2034e3fa0d457096fdc7ddeb2e1b76",
        "addressingKey": {
            "type": "EVP",
            "value": "6ef33d52-8b95-4209-866d-c138f814bc61"
        },
        "holder": {
            "type": "BUSINESS",
            "tradingName": "SDB_MELIUZ Sandbox LTDA",
            "document": {
                "value": "***465740001**",
                "type": "CNPJ"
            }
        },
        "status": "OWNED",
        "createdAt": "2021-06-23T15:10:12.33+00:00",
        "ownedAt": "2021-06-23T15:10:12.33+00:00"
    }', true)),
   ]);

    $client = new PixClient(scopes: ['pix.entries.read'], authenticate: false);
    $dict = $client->searchDictKey($key, 'cpf');

    expect($dict)
        ->toBeInstanceOf(DictKey::class);
})->with([
    'cpf' => '028913038650',
    'cnpj' => '20.129.010/0001-39',
    'email' => 'unit@test.com',
    'phone' => '11 99999-9999',
    'evp' => Uuid::uuid4()->toString(),
]);


it('should returns all dict keys', function () {
    $account = \Idez\Bankly\Data\Account::make([
        'branch' => '3395',
        'number' => '745065',
        'type' => 'CHECKING',
    ]);

    Http::fake([
        "https://api.sandbox.bankly.com.br/accounts/{$account->number}/addressing-keys" => Http::response([
            [
                'type' => 'EVP',
                'value' => Uuid::uuid4()->toString(),
            ],
            [
                'type' => 'CNPJ',
                'value' => $account->document,
            ],
        ]),
    ]);

    $client = new PixClient(scopes: ['pix.entries.read'], authenticate: false);
    $dictKeys = $client
        ->listDictKeys($account->number);

    expect($dictKeys)
        ->toBeArray()
        ->each
        ->toBeInstanceOf(\Idez\Bankly\Data\ValueType::class);
});
