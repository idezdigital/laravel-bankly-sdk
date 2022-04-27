<?php

namespace Idez\Bankly\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class P2P extends Resource
{
    use HasFactory;

    public string $authenticationCode;
}
