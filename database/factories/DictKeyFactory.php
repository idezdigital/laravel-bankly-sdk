<?php

namespace Idez\Bankly\Database\Factories;

use Idez\Bankly\Enums\DictKeyType;
use Idez\Bankly\Data\Pix\DictKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class DictKeyFactory extends Factory
{
    /**
     * @phpstan-ignore-next-line
     */
    protected $model = DictKey::class;

    public function definition()
    {
        return [
            'endToEndId' => $this->faker->uuid,
            'addressingKey' => [
                'type' => DictKeyType::EVP->value,
                'value' => $this->faker->uuid,
            ],
            'holder' => [
                'document' => [
                    'type' => 'CNPJ',
                    'value' => "***465740001**"
                ],
                'name' => $this->faker->name,
                'tradingName' => $this->faker->company,
                'type' => 'BUSINESS'
            ]
        ];
    }
}
