<?php

namespace Idez\Bankly\Clients;

use Carbon\Carbon;
use Idez\Bankly\Data\Account;
use Idez\Bankly\Data\AccountInfo;
use Idez\Bankly\Data\Event;
use Illuminate\Http\Client\RequestException;

class AccountClient extends BaseClient
{
    /**
     * @param Account $account
     * @param Carbon|null $from
     * @param Carbon|null $to
     * @param int $page
     * @param int $pageSize
     * @param bool $includeDetails
     * @return Event[]
     */
    public function getEvents(Account $account, ?Carbon $from = null, ?Carbon $to = null, int $page = 1, int $pageSize = 100, bool $includeDetails = true): array
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

        $events = $this->client()->get('/events', $data)->throw()->json();

        return array_map(fn ($event) => new Event($event), $events);
    }

    public function getAccountData(string $accountNumber, $includeBalance = true): AccountInfo
    {
        $responseObj = $this->client()->get("/accounts/{$accountNumber}", [
            'includeBalance' => $includeBalance,
        ])->throw()->json();

        return new AccountInfo($responseObj);
    }

    /**
     * @throws RequestException
     */
    public function getAccountBalance(string $accountNumber): float
    {
        return $this->getAccountData($accountNumber, true)->available->amount;
    }
}
