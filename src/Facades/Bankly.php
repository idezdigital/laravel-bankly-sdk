<?php

namespace Idez\Bankly\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Idez\Bankly\Bankly
 * @codeCoverageIgnore
 */
class Bankly extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bankly';
    }
}
