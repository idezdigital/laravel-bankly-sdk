<?php

namespace Idez\Bankly\Enums\Clients;

use Idez\Bankly\Enums\AccountType;
use Idez\Bankly\Enums\Bankly;
use Idez\Bankly\Structs\Account;
use Idez\Bankly\Structs\P2P;

class TransferClient extends BanklyClient
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
        ])->throw()->object();

        return new P2P($response);
    }
}
