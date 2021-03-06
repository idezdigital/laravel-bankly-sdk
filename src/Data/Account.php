<?php

namespace Idez\Bankly\Data;

use Idez\Bankly\Enums\AccountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Data
{
    use HasFactory;

    public string $branch = '0001';
    public string $number;
    public ?string $document = null;
    public ?AccountType $type = AccountType::Checking;
    public ?Bank $bank = null;
    public ?Holder $holder = null;

    public function __construct(mixed $data = [], string $branch = '0001')
    {
        if (isset($data['holder']) && ! $data['holder'] instanceof Holder) {
            $data['holder'] = new Holder($data['holder']);
        }

        if (isset($data['bank']) && ! $data['bank'] instanceof Bank) {
            $data['bank'] = new Bank($data['bank']);
        } else {
            $data['bank'] = new Bank();
        }

        if (isset($data['type']) && is_string($data['type'])) {
            $data['type'] = AccountType::tryFrom($data['type']);
        }

        if (isset($data['account'])) {
            $data['branch'] = $data['account']['branch'] ?? $branch ;
            $data['number'] = $data['account']['number'];
        }

        parent::__construct($data);
    }
}
