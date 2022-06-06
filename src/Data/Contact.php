<?php

namespace Idez\Bankly\Data;

class Contact extends Data
{
    public ?string $document;

    public ?string $documentType;
    public ?string $documentNumber;

    public string $name;
    public Account $account;

    public function __construct($data = [])
    {
        $data['account'] = new Account($data['account']);
        parent::__construct($data);
    }
}
