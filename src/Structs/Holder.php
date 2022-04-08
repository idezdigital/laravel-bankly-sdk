<?php

namespace Idez\Bankly\Structs;

use Idez\Bankly\Struct;

class Holder extends Struct
{
    public string $type;
    public string $documentNumber;
    public ?string $name;
}
