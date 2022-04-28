<?php

namespace Idez\Bankly\Data\Webhooks;

class Channel extends Struct
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
