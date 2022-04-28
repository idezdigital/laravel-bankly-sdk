<?php

namespace Idez\Bankly\Database\Factories;

use Idez\Bankly\Data\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;

class P2PFactory extends Factory
{
    /**
     * @phpstan-ignore-next-line
     */
    protected $model = Transfer::class;

    public function definition()
    {
        return [
            'authenticationCode' => $this->faker->uuid,
        ];
    }
}
