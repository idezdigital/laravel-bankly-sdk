<?php

namespace Idez\Bankly\Structs;

use Idez\Bankly\Structs\Address;

class Payer extends Struct
{
    public string $document;
    public string $name;
    public Address $address;

    public ?string $tradeName;
}
