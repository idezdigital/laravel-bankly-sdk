<?php


namespace Idez\Bankly\Structs;

use Idez\Bankly\AccountType;
use Idez\Bankly\Struct;

class Account extends Struct
{

    public string $branch;
    public string $number;
    public ?AccountType $type = AccountType::Checking;
    public ?string $document;
    public ?Bank $bank;
    public ?Holder $holder;

    public function __construct(mixed $data = [])
    {
        $data['bank'] = isset($data['bank'])  ? new Bank($data['bank']) : null;
        $data['holder'] = isset($data['holder']) ? new Holder($data['holder']) : null;
        $data['type'] = isset($data['type']) ? AccountType::tryFrom($data['type']) : null;

        if(isset($data['account'])){
            $data['branch'] = $data['account']['branch'];
            $data['number'] = $data['account']['number'];
        }

        parent::__construct($data);
    }
}
