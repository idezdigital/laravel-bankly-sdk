<?php

it('should can create bank slip', function () {
    $bankSlipClient = new \Idez\Bankly\Clients\BankSlipClient();

    Http::fake([
        'https://api.sandbox.bankly.com.br/bankslip' => Http::response([
            'account' => [
                'number' => '197564',
                'branch' => '0001',
            ],
            'authenticationCode' => 'cb78b121-e891-4ccf-9f4d-8a18f6edb1c9',
        ]),
    ]);

    $bankSlip = $bankSlipClient->createBankslip(
        amount: 100,
        dueDate: now()->addDays(5),
        account: \Idez\Bankly\Data\Account::factory()->make(),
        type: \Idez\Bankly\Enums\BankslipType::Deposit,
        document: '12345678901',
    );

    $this->assertInstanceOf(\Idez\Bankly\Data\BankSlip::class, $bankSlip);
});

it('should can generate bankslip', function () {
    $bankSlipClient = new \Idez\Bankly\Clients\BankSlipClient();
    $code = Str::uuid()->toString();

    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent(
        'bankslip.pdf',
        "%PDF-1.4
        1 0 obj
        <<
        /Title (��Boleto - Bankly)
        /Creator (��wkhtmltopdf 0.12.4)
        /Producer (��Qt 4.8.7)
        /CreationDate (D:20220412214427-03'00')"
    )
        ->store('bankslips');

    Http::fake([
        "https://api.sandbox.bankly.com.br/bankslip/{$code}/pdf*" => Http::response(Storage::get($file)),
    ]);

    $bankSlip = $bankSlipClient->printBankslip($code);
    $this->assertInstanceOf(\Illuminate\Http\Client\Response::class, $bankSlip);
});

it('should can generate bankslip with temporary file', function () {
    $bankSlipClient = new \Idez\Bankly\Clients\BankSlipClient();
    $code = Str::uuid()->toString();

    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent(
        'bankslip.pdf',
        "%PDF-1.4
        1 0 obj
        <<
        /Title (��Boleto - Bankly)
        /Creator (��wkhtmltopdf 0.12.4)
        /Producer (��Qt 4.8.7)
        /CreationDate (D:20220412214427-03'00')"
    )
        ->store('bankslips');

    Http::fake([
        "https://api.sandbox.bankly.com.br/bankslip/{$code}/pdf*" => Http::response(Storage::get($file)),
    ]);

    $bankSlip = $bankSlipClient->printBankslip($code, \Illuminate\Support\Facades\Storage::path('temp/boleto.pdf'));
    $this->assertInstanceOf(\Illuminate\Http\Client\Response::class, $bankSlip);
});

it('should returns bankslip', function () {
    Http::fake([
        'https://api.sandbox.bankly.com.br/bankslip/branch/*' => Http::response([
            'authenticationCode' => '7016f8c1-2dcc-4966-b514-84a05f6be900',
            'updatedAt' => '2021-10-04T17:08:32.652-03:00',
            'ourNumber' => '96395800015',
            'digitable' => '33290001151963958000015001975646387790000010000',
            'status' => 'Settled',
            'account' => [
                'number' => '197564',
            ],
            'document' => '689639580001503',
            'amount' => [
                'currency' => 'BRL',
                'value' => 100.0,
            ],
            'minimumAmount' => [
                'currency' => 'BRL',
                'value' => 0.0,
            ],
            'dueDate' => '2021-10-20T00:00:00-03:00',
            'closePayment' => '2021-10-20T00:00:00-03:00',
            'emissionDate' => '2021-10-04T17:05:58.29-03:00',
            'type' => 'Deposit',
            'payer' => [
                'document' => '32846574000171',
                'name' => 'SDB_MELIUZ Sandbox LTDA',
                'tradeName' => 'SDB_MELIUZ Sandbox LTDA',
                'address' => [
                    'addressLine' => 'Avenida Rebouças',
                    'city' => 'São Paulo',
                    'state' => 'SP',
                    'zipCode' => '05402100',
                ],
            ],
            'recipientFinal' => [
                'document' => '32846574000171',
                'name' => 'SDB_MELIUZ Sandbox LTDA',
                'tradeName' => 'SDB_MELIUZ Sandbox LTDA',
                'address' => [
                    'addressLine' => 'Avenida Rebouças',
                    'city' => 'São Paulo',
                    'state' => 'SP',
                    'zipCode' => '05402100',
                ],
            ],
            'recipientOrigin' => [
                'document' => '32846574000171',
                'name' => 'SDB_MELIUZ Sandbox LTDA',
                'tradeName' => 'SDB_MELIUZ Sandbox LTDA',
                'address' => [
                    'addressLine' => 'Avenida Rebouças',
                    'city' => 'São Paulo',
                    'state' => 'SP',
                    'zipCode' => '05402100',
                ],
            ],
            'payments' => [
                [
                    'id' => '9998ff24-535d-4eeb-bbb5-cd6268b84213',
                    'amount' => 100.0,
                    'paymentChannel' => 'InternetBanking',
                    'paidOutDate' => '2021-10-02T21:00:00-03:00',
                ],
            ],
            'interest' => [
                'startDate' => '0001-01-01T00:00:00',
                'type' => 0,
                'value' => 0.0,
            ],
            'fine' => [
                'startDate' => '0001-01-01T00:00:00',
                'type' => 'Free',
                'value' => 0.0,
            ],
            'discount' => [
                'limitDate' => '0001-01-01T00:00:00',
                'type' => 'Free',
                'value' => 0.0,
            ],
        ]),
    ]);
    $bankSlipClient = new \Idez\Bankly\Clients\BankSlipClient();
    $code = Str::uuid()->toString();
    $account = \Idez\Bankly\Data\Account::factory()->make();

    $bankSlip = $bankSlipClient->getBankslip($account, $code);

    expect($bankSlip)->toBeInstanceOf(\Idez\Bankly\Data\BankSlip::class);
});
