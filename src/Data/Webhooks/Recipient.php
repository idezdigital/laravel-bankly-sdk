<?php

namespace Idez\Bankly\Data\Webhooks;

use Idez\Bankly\Data\Account;
use Idez\Bankly\Data\Data;
use Idez\Bankly\Data\ValueType;

/**
 * Todo: merge to Idez\Bankly\Data\Account
 */
class Recipient extends Data
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
