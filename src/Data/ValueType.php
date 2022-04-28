<?php

namespace Idez\Bankly\Data;

use Psr\Http\Message\ResponseInterface;

class ValueType extends Data
{
    public string $type;
    public ?string $value;

    public function __construct(array|ResponseInterface $data = [])
    {
        parent::__construct($data);
    }
}
