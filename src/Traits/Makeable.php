<?php

namespace Idez\Bankly\Traits;

trait Makeable
{
    /**
     * Create a new element.
     *
     * @param mixed ...$arguments
     * @return static
     */
    public static function make(...$arguments): static
    {
        return new static(...$arguments);
    }
}
