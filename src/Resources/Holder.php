<?php

namespace Idez\Bankly\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Holder extends Resource
{
    use HasFactory;

    public string $type;
    public string $documentNumber;
    public ?string $name;
}
