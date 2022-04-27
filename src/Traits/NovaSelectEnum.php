<?php

namespace Idez\Bankly\Traits;

trait NovaSelectEnum
{
    public static function nova(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
