<?php

namespace Idez\Bankly\Structs;



class Contact extends Struct
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
