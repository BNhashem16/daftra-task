<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryItemFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->words(2, true),
            'sku' => strtoupper($this->faker->unique()->bothify('???-#####')),
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'low_stock_threshold' => $this->faker->numberBetween(5, 20),
        ];
    }
}
