<?php

namespace Idez\Bankly\Structs;

class Rate extends Struct
{
    public \DateTime $startDate;
    public string $type;
    public float $value;
}
