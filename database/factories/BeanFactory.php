<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BeanFactory extends Factory
{
    public function definition(): array
    {
        $roastLevels = ['light', 'medium', 'dark'];

        return [
            'name' => $this->faker->words(2, true) . ' Blend',
            'origin' => $this->faker->randomElement(['Ethiopia', 'Colombia', 'Brazil', 'Guatemala', 'Kenya']),
            'roastery' => $this->faker->company() . ' Roasters',
            'roast_level' => $this->faker->randomElement($roastLevels),
            'roast_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'notes' => $this->faker->sentence(),
        ];
    }
}
