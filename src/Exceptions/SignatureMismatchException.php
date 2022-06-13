<?php

namespace Idez\Bankly\Exceptions;

class SignatureMismatchException extends BanklyException
{
    protected $code = 401;
}
