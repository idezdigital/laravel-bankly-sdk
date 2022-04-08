<?php


namespace Idez\Bankly\Structs;


use Idez\Bankly\Struct;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DictKey extends Struct
{
    public string $endToEndId;
    public ValueType $addressingKey;
    public DictHolder $holder;

    use HasFactory;

    public function __construct($data = [])
    {
        $data['addressingKey'] = new ValueType($data['addressingKey']);
        $data['holder'] = new DictHolder($data['holder']);

        parent::__construct($data);
    }
}
