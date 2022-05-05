<?php

namespace Idez\Bankly;

use Idez\Bankly\Clients\AccountClient;
use Idez\Bankly\Clients\BankSlipClient;
use Idez\Bankly\Clients\PixClient;
use Idez\Bankly\Clients\TransferClient;
use Illuminate\Support\Collection;

class Bankly
{
    public const ACESSO_ISPB = '13140088';
    public const ACESSO_COMPE = '332';
    public const ACESSO_NAME = 'Acesso Soluções de Pagamentos S.A';

    public function __construct(
        private readonly string|null $certificatePath = null,
        private readonly string|null $privatePath = null,
        private readonly string|null $passphrase = null,
        private readonly array|string|Collection|null $scopes = null,
        private readonly array|Collection $middlewares = [],
    )
    {
    }

    public function account()
    {
        return new AccountClient($this->certificatePath, $this->privatePath, $this->passphrase, $this->scopes, $this->middlewares);
    }

    public function bankSlip()
    {
        return new BankSlipClient($this->certificatePath, $this->privatePath, $this->passphrase, $this->scopes, $this->middlewares);
    }

    public function pix()
    {
        return new PixClient($this->certificatePath, $this->privatePath, $this->passphrase, $this->scopes, $this->middlewares);
    }

    public function transfer()
    {
        return new TransferClient($this->certificatePath, $this->privatePath, $this->passphrase, $this->scopes, $this->middlewares);
    }
}
