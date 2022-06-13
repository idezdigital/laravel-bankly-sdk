<?php

namespace Idez\Bankly\Data;

/**
 * Class CashInWebhook
 * @package App\Models\Bankly
 * @property string $Ispb
 * @property string $Name
 */
class AccountBalance extends Data
{
    public float $amount;
    public string $currency;
}
