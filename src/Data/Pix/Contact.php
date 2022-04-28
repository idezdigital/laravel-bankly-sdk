<?php

namespace Idez\Bankly\Data\Pix;

use Idez\Bankly\Data\Account;
use Idez\Bankly\Data\Bank;
use Idez\Bankly\Data\Resource;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Resource
{
    use HasFactory;
    public ?string $name;
    public string $documentNumber;
    public string $documentType;
    public Account $account;
    public Bank $bank;

    public function __construct($data = [])
    {
        $data['account'] = $data['account'] instanceof Account ? $data['account'] : new Account($data['account']);
        $data['bank'] = new Bank($data['bank']);

        parent::__construct($data);
    }
}
