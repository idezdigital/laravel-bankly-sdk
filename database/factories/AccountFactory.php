<?php

namespace Idez\Bankly\Database\Factories;

use Idez\Bankly\Enums\AccountType;
use Idez\Bankly\Resources\Account;
use Idez\Bankly\Resources\Holder;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition()
    {
        return [
            'branch' => '0001',
            'number' => '123456789',
            'document' => $this->faker->numerify('############'),
            'type' => $this->faker->randomElement(AccountType::cases()),
            'holder' => Holder::factory()->make(),
        ];
    }
}
