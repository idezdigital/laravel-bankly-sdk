<?php

namespace Idez\Bankly\Data\Webhooks;

use Idez\Bankly\Data\Data;

class Channel extends Data
{
    public string $name;
    public string $end2EndId;
    public ?string $receiverReconciliationId;
    public string $pixInitializationType;
    public string $pixPaymentPriority;
    public string $pixPaymentPriorityType;
    public string $pixPaymentPurpose;

    public Recipient $sender;

    public function __construct($data = [])
    {
        $data['sender'] = new Recipient($data['sender']);

        parent::__construct($data);
    }
}
