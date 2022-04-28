<?php

namespace App\Models\Bankly\Webhook;

use App\Models\Bankly\Account;
use App\Models\Bankly\ValueType;
use App\Models\Struct;

/**
 * Todo: merge to App\Models\Account
 */
class Recipient extends Struct
{
    public ValueType $document;
    public string $type;
    public string $name;
    public Account $account;

    public function __construct($data = [])
    {
        $data['account'] = new Account($data['account']);
        $data['document'] = new ValueType($data['document']);

        parent::__construct($data);
    }
}
