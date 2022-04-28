<?php

namespace Idez\Bankly\Clients;

use Idez\Bankly\Bankly;
use Idez\Bankly\Data\Account;
use Idez\Bankly\Data\P2P;
use Idez\Bankly\Enums\AccountType;

class TransferClient extends BaseClient
{
    /**
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function p2p(Account $from, Account $to, float $amount, string $description, $correlationID, AccountType $type = AccountType::Checking): P2P
    {
        $response = $this->client()->withHeaders([
            'x-correlation-id' => $correlationID,
        ])->post('/fund-transfers', [
            'amount' => round($amount * 100),
            'description' => $description,
            'sender' => [
                'name' => $from->number,
                'branch' => $from?->branch,
                'account' => $from->number,
                'document' => $from->document,
            ],
            'recipient' => [
                'bankCode' => Bankly::ACESSO_COMPE,
                'name' => $to->number,
                'branch' => $to?->branch,
                'account' => $to->number,
                'document' => $to->document,
                'type' => $type->value,
            ],
        ])->throw()->json();

        return new P2P($response);
    }
}
