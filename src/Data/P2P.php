<?php

namespace Idez\Bankly\Data;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class P2P extends Data
{
    use HasFactory;

    public string $authenticationCode;
}
