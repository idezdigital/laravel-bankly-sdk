<?php

namespace Idez\Bankly\Contracts;

interface NovaSelectEnumInterface
{
    public function label(): string;

    public static function nova(): array;
}
