<?php

namespace Idez\Bankly\Data;

class DictHolder extends Resource
{
    public string $name;
    public string $type;
    public ValueType $document;
    public ?string $tradingName;

    public function __construct($data = [])
    {
        $data['name'] ??= $data['tradingName'];
        $data['document'] = new ValueType($data['document']);

        parent::__construct($data);
    }
}
