<?php

namespace Idez\Bankly\Enums;

enum AccountType: string
{
    case Checking = 'CHECKING';
    case Salary = 'SALARY';
    case Savings = 'SAVINGS';
    case Payment = 'PAYMENT';
}
