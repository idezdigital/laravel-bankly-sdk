<?php

namespace Idez\Bankly;

use Idez\Bankly\Clients\AccountClient;
use Idez\Bankly\Clients\BankSlipClient;
use Idez\Bankly\Clients\PixClient;
use Idez\Bankly\Clients\TransferClient;

class Bankly
{
    public const ACESSO_ISPB = '13140088';
    public const ACESSO_COMPE = '332';
    public const ACESSO_NAME = 'Acesso Soluções de Pagamentos S.A';

    public function account()
    {
        return app(AccountClient::class);
    }

    public function bankSlip()
    {
        return app(BankSlipClient::class);
    }

    public function pix()
    {
        return app(PixClient::class);
    }

    public function transfer()
    {
        return app(TransferClient::class);
    }
}
