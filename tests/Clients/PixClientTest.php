<?php

use Idez\Bankly\Clients\BanklyMTLSClient;
use Illuminate\Support\Str;

test('should PixClient be a children of BanklyMTLSClient', function () {
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

    $client = new \Idez\Bankly\Clients\PixClient();
    $this->assertInstanceOf(BanklyMTLSClient::class, $client);
});

test('should create qrcode', function () {
    $encoded = base64_encode(Str::random(10));

    Http::fake(
        ['https://login.sandbox.bankly.com.br/connect/token' => Http::response(
            [
                'access_token' => 'token',
                'expires_in' => 3600,
                'token_type' => 'Bearer',
                'scope' => 'test',
            ],
            403
        ),
        'https://sandbox.bankly.com.br/pix/qrcodes' => Http::response(['encodedValue'=> $encoded],200),
    ]);

     $pix = new \Idez\Bankly\Clients\PixClient();
     $qr = $pix->createStaticQrCode(
         keyType: 'evp',
         keyValue: Str::uuid()->toString(),
         amount: 100.00,
         conciliationId: Str::random(16),
         recipientName: \Pest\Faker\faker()->name,
         locationCity: \Pest\Faker\faker('pt_BR')->city,
         locationZip: \Pest\Faker\faker('pt_BR')->postcode(),
         singlePayment: false
     );

     $this->assertEquals('https://api.pix.fr/v1/qrcode/test', $qr);
 });
