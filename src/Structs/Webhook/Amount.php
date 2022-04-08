<?php

namespace App\Models\Bankly\Webhook;

use App\Models\Struct;

class Amount extends Struct
{
    public string $value;
    public string $currency;
}
