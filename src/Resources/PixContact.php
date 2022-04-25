<?php

namespace Idez\Bankly\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PixContact extends Resource
{
    use HasFactory;
    public ?string $name;
    public string $documentType;
    public string $documentNumber;

    public Account $account;
    public Bank $bank;

    public function __construct($data = [])
    {
        $data['account'] = $data['account'] instanceof Account ? $data['account'] : new Account($data['account']);
        $data['bank'] = new Bank($data['bank']);

        parent::__construct($data);
    }
}
