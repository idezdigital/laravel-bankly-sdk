<?php

namespace Idez\Bankly\Enums\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Idez\Bankly\Enums\Bankly
 */
class Bankly extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bankly';
    }
}
