<?php

namespace Idez\Bankly\Clients;

use Carbon\Carbon;
use Idez\Bankly\Data\Account;
use Idez\Bankly\Data\AccountInfo;
use Illuminate\Http\Client\RequestException;

class AccountClient extends BaseClient
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
     * @throws RequestException
     */
    public function getAccountData(string $accountNumber): AccountInfo
    {
        $responseObj = $this->client()->get("/accounts/{$accountNumber}", [
            'includeBalance' => 'true',
        ])->throw()->object();

        return new AccountInfo($responseObj);
    }

    /**
     * @throws RequestException
     */
    public function getAccountBalance(string $accountNumber): float
    {
        return $this->getAccountData($accountNumber)->available->amount;
    }
}
