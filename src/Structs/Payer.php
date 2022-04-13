<?php

namespace Idez\Bankly\Structs;

class Payer extends Struct
{
    public string $document;
    public string $name;
    public Address $address;

    public ?string $tradeName;
}
