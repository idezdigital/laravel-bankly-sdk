<?php

namespace Idez\Bankly\Structs;

class DictHolder extends Struct
{
    public string $name;
    public string $type;
    public ValueType $document;

    public function __construct($data = [])
    {
        $data['document'] = new ValueType($data['document']);

        parent::__construct($data);
    }
}
