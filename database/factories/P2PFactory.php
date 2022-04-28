<?php

namespace Idez\Bankly\Database\Factories;

use Idez\Bankly\Data\P2P;
use Illuminate\Database\Eloquent\Factories\Factory;

class P2PFactory extends Factory
{
    protected $model = P2P::class;

    public function definition()
    {
        return [
            'authenticationCode' => $this->faker->uuid,
        ];
    }
}
