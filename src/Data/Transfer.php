<?php

namespace Idez\Bankly\Data;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfer extends Data
{
    use HasFactory;

    public string $authenticationCode;
}
