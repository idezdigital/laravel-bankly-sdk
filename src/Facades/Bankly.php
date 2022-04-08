<?php

namespace Idez\Bankly\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Idez\Bankly\Bankly
 */
class Bankly extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bankly';
    }
}
