<?php

namespace Idez\Bankly\Clients;

use Idez\Bankly\Bankly;
use Idez\Bankly\Data\Account;
use Idez\Bankly\Data\Transfer;
use Idez\Bankly\Enums\AccountType;

class TransferClient extends BaseClient
{
    public function p2p(Account $from, Account $to, float $amount, ?string $description = null, AccountType $type = AccountType::Checking): Transfer
    {
        $response = $this->client()->throw()->post('/fund-transfers', [
            'amount' => round($amount * 100),
            'description' => $description,
            'sender' => [
                'name' => $from->holder?->name ?? $from->number,
                'branch' => $from->branch,
                'account' => $from->number,
                'document' => $from->document,
            ],
            'recipient' => [
                'bankCode' => $to->bank?->compe ?? Bankly::ACESSO_COMPE,
                'name' => $to?->holder?->name ?? $to->number,
                'branch' => $to->branch ?? '0001',
                'account' => $to->number,
                'document' => $to->document,
                'type' => $type->value,
            ],
        ])->json();

        return new Transfer($response);
    }
}
