<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Section>
 */
class SectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => $this->faker->randomElement([1, 2]),
            'building_id' => \App\Models\Building::factory(),
            'name' => 'Секция '.$this->faker->numberBetween(1, 10),
            'floors_count' => $this->faker->numberBetween(1, 10),
        ];

    }
}
