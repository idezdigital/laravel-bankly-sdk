<?php

namespace Idez\Bankly\Structs;

/**
 * Class CashInWebhook
 * @package App\Models\Bankly
 * @property string $Ispb
 * @property string $Name
 */
class AccountBalance extends Struct
{
    public string $amount;
    public string $currency;
}
