<?php

it('should returns all events', function () {
    Http::fake([
        'https://api.sandbox.bankly.com.br/events*' => Http::response([
            [
                'aggregateId' => 'TRANSACTION_ID_1ee9751e-3584-41be-9c68-1f45299308ca',
                'type' => 'TRANSACTION',
                'category' => 'EletronicTransfers',
                'description' => 'Removendo dinheiro da conta Main',
                'documentNumber' => '32846574000171',
                'bankBranch' => '0001',
                'bankAccount' => '197564',
                'amount' => -124.32,
                'index' => 'cash-out',
                'name' => 'CASH_OUT_ACCOUNT',
                'timestamp' => '2022-04-28T00:33:32.442544Z',
                'status' => 'ACTIVE',
            ],
            [
                'aggregateId' => 'PIX_TRANSACTION_ID_a010c49f-ef21-4100-a921-0e7fd843ca4c',
                'type' => 'TRANSACTION',
                'category' => 'EletronicTransfers',
                'documentNumber' => '32846574000171',
                'bankBranch' => '0001',
                'bankAccount' => '197564',
                'amount' => -2.5,
                'index' => 'cash-out',
                'name' => 'PIX_CASH_OUT_REFUND_ACCOUNT',
                'timestamp' => '2022-04-27T10:32:21.56877Z',
                'data' => [
                    'AuthenticationCodeOriginal' => '435b8119-9831-4f85-a6f3-910e7dafd4f5',
                    'EndToEndIdOriginal' => 'E1314008820220427103158152614063',
                    'RefundCode' => 'MD06',
                    'RefundReason' => '',
                    'TotalRefundedAmount' => 2.5,
                    'AuthenticationCode' => 'a010c49f-ef21-4100-a921-0e7fd843ca4c',
                    'EndToEndId' => null,
                    'TransactionDate' => '2022-04-27T10:32:21.56877Z',
                    'InitializationType' => 'Manual',
                    'AddressKey' => 'd8b3c33f-9bc9-4444-8716-d7a6d243e55e',
                    'AddressKeyType' => 'EVP',
                    'Description' => null,
                    'Channel' => 'INTERNAL',
                    'Amount' => -2.5,
                    'Recipient' => [
                        'Document' => '71246654423',
                        'DocumentType' => 'CPF',
                        'Name' => 'Quatro',
                        'BankBranch' => '0001',
                        'BankAccount' => '210161',
                        'BankAccountType' => 'CHECKING',
                        'BankIspb' => '13140088',
                        'BankName' => 'Acesso Soluções De Pagamento S.A.',
                        'BankCompe' => '332',
                    ],
                    'ScheduledPaymentDate' => null,
                    'AmountDetails' => null,
                    'WithdrawalProviderIspb' => null,
                    'WithdrawalAgentType' => null,
                ],
                'status' => 'ACTIVE',
            ],
        ]),
    ]);

    $accountClient = new \Idez\Bankly\Clients\AccountClient(authenticate: false);
    $events = $accountClient->getEvents(\Idez\Bankly\Data\Account::factory()->make());
    expect($events)
        ->toBeArray()
        ->each
        ->toBeInstanceOf(\Idez\Bankly\Data\Event::class);
});
