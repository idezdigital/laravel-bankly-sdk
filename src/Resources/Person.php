<?php

namespace Idez\Bankly\Resources;

use Psr\Http\Message\ResponseInterface;

class Person extends Resource
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