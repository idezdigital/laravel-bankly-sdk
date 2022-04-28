<?php

namespace Idez\Bankly\Clients;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Idez\Bankly\Data\Account;
use Idez\Bankly\Data\Bankslip;
use Idez\Bankly\Enums\BankslipType;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\File;

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
     * @throws Exception
     */
    public function createBankslip(float $amount, Carbon $dueDate, Account $account, BankslipType $type = BankslipType::Invoice, string|null $document = null): Bankslip
    {
        $response = $this->client()->post('/bankslip', [
            'account' => [
                'number' => $account->number,
                'branch' => $account->branch,
            ],
            'documentNumber' => $document ?? $account->document,
            'amount' => $amount,
            'dueDate' => $dueDate->format('d/m/Y'),
            'type' => $type,
        ])->throw()->json();

        return new Bankslip($response);
    }

    /**
     * @param Account $account
     * @param string $authorizationCode
     * @return object
     * @throws Exception
     */
    public function getBankslip(Account $account, string $authorizationCode): object
    {
        $response = $this->client()->get("/bankslip/branch/{$account->branch}/number/{$account->number}/{$authorizationCode}")
            ->json();

        return new Bankslip($response);
    }

    /**
     * @param string $authorizationCode
     * @param string|null $temporaryPath
     * @return PromiseInterface|Response
     */
    public function printBankslip(string $authorizationCode, ?string $temporaryPath = null): PromiseInterface|Response
    {
        $response = $this->client();
        if(filled($temporaryPath)) {
           $response->sink($temporaryPath);
        }


        return $response->get("/bankslip/{$authorizationCode}/pdf");
    }
}
