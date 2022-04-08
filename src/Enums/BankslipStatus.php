<?php

namespace Idez\Bankly;

enum BankslipStatus: string
{
    case Settled = 'Settled';
    case Registered = 'Registered';
}
