<?php

namespace Idez\Bankly\Database\Factories;

use Idez\Bankly\Enums\AccountType;
use Idez\Bankly\Data\Account;
use Idez\Bankly\Data\Holder;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * @phpstan-ignore-next-line
     */
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
