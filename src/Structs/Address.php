<?php


namespace Idez\Bankly\Structs;


use Idez\Bankly\Struct;


class Address extends Struct
{
    public string $addressLine;
    public string $city;
    public string $state;
    public string $zipCode;
}
