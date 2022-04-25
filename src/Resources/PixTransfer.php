<?php

namespace Idez\Bankly\Resources;

class PixTransfer extends Resource
{
    public float $amount;
    public string $description;
    public PixContact $sender;
    public PixContact $recipient;

    public ?string $authenticationCode;

    public function __construct($data = [])
    {
        $data['sender'] = $data['sender'] instanceof PixContact ? $data['sender'] : new PixContact($data['sender']);
        $data['recipient'] = $data['recipient'] instanceof PixContact ? $data['recipient'] : new PixContact($data['recipient']);

        parent::__construct($data);
    }
}
