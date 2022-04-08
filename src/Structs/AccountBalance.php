<?php


namespace Idez\Bankly\Structs;

use Idez\Bankly\Struct;

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
