<?php

namespace Idez\Bankly;

enum BankslipType: string
{
    case Deposit = 'deposit';
    case Invoice = 'levy';
}