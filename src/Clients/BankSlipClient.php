<?php

namespace Idez\Bankly\Enums\Clients;

use Carbon\Carbon;
use Idez\Bankly\Enums\BankslipType;
use Idez\Bankly\Structs\Account;

class BankSlipClient extends BanklyClient
{
    /**
     * @param float $amount
     * @param Carbon $dueDate
     * @param \Idez\Bankly\Structs\Account $account
     * @param BankslipType $type
     * @param string|null $document
     * @return object
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function createBankslip(float $amount, Carbon $dueDate, Account $account, BankslipType $type = BankslipType::Invoice, string|null $document = null): object
    {
        return $this->client()->post('/bankslip', [
            'account' => [
                'number' => $account->number,
                'branch' => $account->branch,
            ],
            'documentNumber' => $document ?? $account->document,
            'amount' => $amount,
            'dueDate' => $dueDate->format('d/m/Y'),
            'type' => $type,
        ])->throw()->object();
    }

    /**
     * @param string $accountBranch
     * @param string $accountNumber
     * @param string $authorizationCode
     * @return object
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getBankslip(string $accountBranch, string $accountNumber, string $authorizationCode): object
    {
        return $this->client()->get("/bankslip/branch/{$accountBranch}/number/{$accountNumber}/{$authorizationCode}")
            ->throw()->object();
    }

    public function printBankslip(string $authorizationCode): \Illuminate\Http\Client\Response
    {
        return $this->client()->get("/bankslip/{$authorizationCode}/pdf");
    }
}
