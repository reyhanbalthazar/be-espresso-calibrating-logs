<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CalibrationSession;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CalibrationSession>
 */
class CalibrationSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'session_date' => fake()->date(),
            'notes' => fake()->sentence(),
        ];
    }
}