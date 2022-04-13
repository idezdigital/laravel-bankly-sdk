<?php

namespace Idez\Bankly\Structs;

class Holder extends Struct
{
    public string $type;
    public string $documentNumber;
    public ?string $name;
}
