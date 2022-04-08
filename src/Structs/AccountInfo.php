<?php


namespace Idez\Bankly\Structs;

use Idez\Bankly\Struct;

class AccountInfo extends Struct
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
