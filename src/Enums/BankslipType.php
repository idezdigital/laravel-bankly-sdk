<?php

namespace Idez\Bankly\Enums;

enum BankslipType: string
{
    case Deposit = 'deposit';
    case Invoice = 'levy';
}
