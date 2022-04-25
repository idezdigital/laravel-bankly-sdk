<?php

namespace Idez\Bankly\Resources;

/**
 * Class CashInWebhook
 * @package App\Models\Bankly
 * @property string $Ispb
 * @property string $Name
 */
class AccountBalance extends Resource
{
    public string $amount;
    public string $currency;
}
