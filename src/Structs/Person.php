<?php

namespace Idez\Bankly\Structs;

use Psr\Http\Message\ResponseInterface;

class Person extends Struct
{
    public string $document;
    public string $name;
    public Address $address;

    public ?string $tradeName;

    public function __construct(array|ResponseInterface $data = [])
    {
        $data['address'] = new Address($data['address'] ?? []);
        parent::__construct($data);
    }
}
