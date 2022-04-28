<?php

namespace Idez\Bankly\Data;

class Rate extends Resource
{
    public \DateTime $startDate;
    public string $type;
    public float $value;
}
