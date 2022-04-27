<?php

namespace Idez\Bankly\Database\Factories;

use Idez\Bankly\Enums\AccountType;
use Idez\Bankly\Resources\Account;
use Idez\Bankly\Resources\Holder;
use Illuminate\Database\Eloquent\Factories\Factory;

class HolderFactory extends Factory
{
    protected $model = Holder::class;

    public function definition()
    {
        return [
            'documentNumber' => $this->faker->numerify('############'),
            'type' => $this->faker->text,
            'name' => $this->faker->name,
        ];
    }
}
