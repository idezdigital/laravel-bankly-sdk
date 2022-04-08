<?php

namespace Idez\Bankly\Structs;

use Idez\Bankly\Bankly;
use Idez\Bankly\Struct;

/**
 * Class CashInWebhook
 * @package App\Models\Bankly
 * @property string $Ispb
 * @property string $Name
 */
class Bank extends Struct
{
    public string $ispb = Bankly::ACESSO_ISPB;
    public string $name = Bankly::ACESSO_NAME;
    public ?string $compe = Bankly::ACESSO_COMPE;
}
