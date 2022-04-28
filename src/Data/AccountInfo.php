<?php

namespace Idez\Bankly\Data;

class AccountInfo extends Data
{
    public string $status;
    public string $branch;
    public string $number;
    public AccountBalance $inProcess;
    public AccountBalance $available;
    public AccountBalance $blocked;

    public function __construct($data = [])
    {
        $data->inProcess = new AccountBalance($data->balance->inProcess);
        $data->blocked = new AccountBalance($data->balance->blocked);
        $data->available = new AccountBalance($data->balance->available);
        unset($data->balance);

        parent::__construct($data);
    }
}
