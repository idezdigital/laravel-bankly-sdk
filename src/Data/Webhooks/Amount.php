<?php

namespace Idez\Bankly\Data\Webhooks;

use Idez\Bankly\Data\Resource;

class Amount extends Resource
{
    public string $value;
    public string $currency;
}
