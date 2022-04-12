<?php

namespace Idez\Bankly\Structs;



class Address extends Struct
{
    public string $addressLine;
    public string $city;
    public string $state;
    public string $zipCode;
}
