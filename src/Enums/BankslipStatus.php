<?php

namespace Idez\Bankly\Enums;

enum BankslipStatus: string
{
    case Settled = 'Settled';
    case Registered = 'Registered';
}
