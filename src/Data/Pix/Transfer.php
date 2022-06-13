<?php

namespace Idez\Bankly\Data\Pix;

use Idez\Bankly\Data\Data;

class Transfer extends Data
{
    public float $amount;
    public float $withdrawalAmount = 0.00;
    public float $chargeAmount = 0.00;
    public string $description;
    public Contact $sender;
    public Contact $recipient;
    public string $authenticationCode;

    public function __construct($data = [])
    {
        $data['sender'] = $data['sender'] instanceof Contact ? $data['sender'] : new Contact($data['sender']);
        $data['recipient'] = $data['recipient'] instanceof Contact ? $data['recipient'] : new Contact($data['recipient']);

        parent::__construct($data);
    }
}
