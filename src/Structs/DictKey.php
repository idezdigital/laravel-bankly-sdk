<?php

namespace Idez\Bankly\Enums\Structs;


use Illuminate\Database\Eloquent\Factories\HasFactory;

class DictKey extends Struct
{
    use HasFactory;
    public string $endToEndId;
    public ValueType $addressingKey;
    public DictHolder $holder;

    public function __construct($data = [])
    {
        $data['addressingKey'] = new ValueType($data['addressingKey']);
        $data['holder'] = new DictHolder($data['holder']);

        parent::__construct($data);
    }
}
