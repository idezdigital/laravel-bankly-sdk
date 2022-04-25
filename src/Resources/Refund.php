<?php

namespace Idez\Bankly\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Refund extends Resource
{
    use HasFactory;

    public string $authenticationCode;
    public float $amount;
    public ?string $description;
    public string $correlationId;
    public string $status;
    public PixContact $sender;
    public PixContact $recipient;

    public function __construct($data = [])
    {
        $data['sender'] = new PixContact($data['sender']);
        $data['recipient'] = new PixContact($data['recipient']);

        parent::__construct($data);
    }
}
