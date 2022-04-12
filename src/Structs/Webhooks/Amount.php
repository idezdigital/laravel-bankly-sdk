<?php

namespace Idez\Bankly\Structs\Webhooks;

use Idez\Bankly\Structs\Struct;

class Amount extends Struct
{
    public string $value;
    public string $currency;
}
