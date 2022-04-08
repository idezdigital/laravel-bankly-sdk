<?php

namespace App\Models\Bankly\Webhook;

use App\Models\Bankly\ValueType;
use App\Models\Struct;

class CashInData extends Struct
{
    public ?ValueType $addressingKey;
    public string $authenticationCode;
    public Amount $amount;
    public Recipient $recipient;
    public Channel $channel;

    public function __construct($data = [])
    {
        if (isset($data['addressingKey'])) {
            $data['addressingKey'] = new ValueType($data['addressingKey']);
        }

        $data['amount'] = new Amount($data['amount']);
        $data['recipient'] = new Recipient($data['recipient']);
        $data['channel'] = new Channel($data['channel']);

        parent::__construct($data);
    }
}
