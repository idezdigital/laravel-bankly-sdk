<?php

namespace Idez\Bankly\Resources\Pix;

use Idez\Bankly\Resources\DictHolder;
use Idez\Bankly\Resources\Resource;
use Idez\Bankly\Resources\ValueType;
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
