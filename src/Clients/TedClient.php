<?php

namespace Idez\Bankly\Clients;

use Carbon\Carbon;
use Idez\Bankly\Data\Account;
use Idez\Bankly\Data\AccountInfo;
use Idez\Bankly\Data\Event;
use Idez\Bankly\Data\Pix\DictKey;
use Idez\Bankly\Data\Ted;
use Idez\Bankly\Enums\AccountType;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;

class TedClient extends BaseClient
{
    public function executeTed(Account $from, Account $to, int $amount, string $description = ''): Response
    {
        $ted = $this->client()->post('/fund-transfers', [
            'amount' => $amount,
            'sender' => $from->toArray(),
            'recipient' => $to->toArray(),
            'description' => $description,
        ]);

        return Ted::make($ted->json());
    }

    public function listTeds(Account $account, string $branch = '0001', int $pageSize = 50, ?string $cursor = null)
    {
        $teds = $this->client()->get('/fund-transfers', [
            'branch' => $branch,
            'account' => $account->account,
            'pageSize' => $pageSize,
            'nextPage' => $cursor,
        ]);

        return $teds->collect()->get('data')->map(fn($ted) => Ted::make($ted));
    }

    public function getTed(string $id)
    {
        $ted = $this->client()->get("/fund-transfers/{$id}");

        return Ted::make($ted->json());
    }
}
