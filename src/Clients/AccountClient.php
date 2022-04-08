<?php

namespace Idez\Bankly\Clients;

use Carbon\Carbon;
use Idez\Bankly\Structs\Account;
use Idez\Bankly\Structs\AccountInfo;

class AccountClient extends BanklyClient
{
    public function getEvents(Account $account, ?Carbon $from = null, ?Carbon $to = null, int $page = 1, int $pageSize = 100, bool $includeDetails = true): object
    {
        $data = [
            'page' => $page,
            'pageSize' => $pageSize,
            'includeDetails' => $includeDetails ?
                'true' :
                'false',
            'branch' => $account->branch,
            'account' => $account->number,
        ];

        if (filled($from)) {
            //2021-09-24T18:40:07
            $data['beginDateTime'] = $from->setTimezone('UTC')->format('Y-m-d\TH:i:s');
        }

        if (filled($to)) {
            $data['endDateTime'] = $to->setTimezone('UTC')->format('Y-m-d\TH:i:s');
        }

        return $this->client()->get('/events', $data)->object();
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getAccountData(string $accountNumber): AccountInfo
    {
        $responseObj = $this->client()->get("/accounts/{$accountNumber}", [
            'includeBalance' => 'true',
        ])->throw()->object();

        return new AccountInfo($responseObj);
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getAccountBalance(string $accountNumber): float
    {
        return $this->getAccountData($accountNumber)->available->amount;
    }
}
