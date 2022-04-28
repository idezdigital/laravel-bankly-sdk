<?php

namespace Idez\Bankly\Data\Webhooks;

use Idez\Bankly\Data\Data;
use Idez\Bankly\Data\ValueType;

class CashInData extends Data
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
