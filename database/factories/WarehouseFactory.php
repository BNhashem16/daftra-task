<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->company.' Warehouse',
            'location' => $this->faker->city.', '.$this->faker->stateAbbr,
        ];
    }
}
