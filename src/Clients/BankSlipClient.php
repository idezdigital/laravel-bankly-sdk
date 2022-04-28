<?php

namespace Idez\Bankly\Clients;

use Carbon\Carbon;
use Exception;
use Idez\Bankly\Enums\BankslipType;
use Idez\Bankly\Data\Account;
use Idez\Bankly\Data\Bankslip;
use Illuminate\Http\Client\RequestException;

class BankSlipClient extends BaseClient
{
    /**
     * @param float $amount
     * @param Carbon $dueDate
     * @param Account $account
     * @param BankslipType $type
     * @param string|null $document
     * @return Bankslip
     * @throws RequestException
     */
    public function createBankslip(float $amount, Carbon $dueDate, Account $account, BankslipType $type = BankslipType::Invoice, string|null $document = null): Bankslip
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
        ])->throw()->json();
    }

    /**
     * @param string $accountBranch
     * @param string $accountNumber
     * @param string $authorizationCode
     * @return object
     * @throws RequestException
     * @throws Exception
     */
    public function getBankslip(string $accountBranch, string $accountNumber, string $authorizationCode): object
    {
        $response = $this->client()->get("/bankslip/branch/{$accountBranch}/number/{$accountNumber}/{$authorizationCode}")
            ->throw()->json();

        return new Bankslip($response);
    }

    public function printBankslip(string $authorizationCode): \Illuminate\Http\Client\Response
    {
        return $this->client()->get("/bankslip/{$authorizationCode}/pdf");
    }
}
