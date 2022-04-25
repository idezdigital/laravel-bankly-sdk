<?php

namespace Idez\Bankly\Resources;

use Idez\Bankly\Enums\AccountType;

class Account extends Resource
{
    public string $branch = '0001';
    public string $number;
    public ?AccountType $type = AccountType::Checking;
    public ?string $document;
    public ?Bank $bank;
    public ?Holder $holder;

    public function __construct(mixed $data = [], string $branch = '0001')
    {
        $data['bank'] = isset($data['bank']) ? new Bank($data['bank']) : null;
        $data['holder'] = isset($data['holder']) ? new Holder($data['holder']) : null;
        $data['type'] = isset($data['type']) ? AccountType::tryFrom($data['type']) : null;

        if (isset($data['account'])) {
            $data['branch'] = $branch ?? $data['account']['branch'];
            $data['number'] = $data['account']['number'];
        }

        parent::__construct($data);
    }
}
