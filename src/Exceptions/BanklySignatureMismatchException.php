<?php

namespace Idez\Bankly\Exceptions;

class BanklySignatureMismatchException extends BanklyException
{
    protected $code = 401;
}
