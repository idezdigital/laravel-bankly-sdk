<?php

namespace Idez\Bankly\Resources\Webhooks;

use Idez\Bankly\Resources\Resource;

class Amount extends Resource
{
    public string $value;
    public string $currency;
}
