<?php

namespace Idez\Bankly\Data\Pix;

use Idez\Bankly\Data\DictHolder;
use Idez\Bankly\Data\Data;
use Idez\Bankly\Data\ValueType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DictKey extends Data
{
    use HasFactory;

    public ?string $endToEndId;
    public ValueType $addressingKey;
    public DictHolder $holder;

    public function __construct($data = [])
    {
        $data['addressingKey'] = new ValueType($data['addressingKey']);
        $data['holder'] = new DictHolder($data['holder']);

        parent::__construct($data);
    }
}
