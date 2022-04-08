<?php

namespace Idez\Bankly\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class EnumCast implements Cast
{

    public function __construct(
        protected ?string $type = null
    ) {
    }

    public function cast(DataProperty $property, mixed $value): mixed
    {
        return $property::tryFrom($value);
    }
}
