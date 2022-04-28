<?php

namespace Idez\Bankly\Data;

use Idez\Bankly\Data\Pix\Contact;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Refund extends Resource
{
    use HasFactory;

    public string $authenticationCode;
    public float $amount;
    public ?string $description;
    public string $correlationId;
    public string $status;
    public Contact $sender;
    public Contact $recipient;

    public function __construct($data = [])
    {
        $data['sender'] = new Contact($data['sender']);
        $data['recipient'] = new Contact($data['recipient']);

        parent::__construct($data);
    }
}
