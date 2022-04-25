<?php

namespace Idez\Bankly\Resources;

class Rate extends Resource
{
    public \DateTime $startDate;
    public string $type;
    public float $value;
}
