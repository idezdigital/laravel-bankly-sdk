<?php

namespace Idez\Bankly\Resources;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DictKey extends Resource
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
