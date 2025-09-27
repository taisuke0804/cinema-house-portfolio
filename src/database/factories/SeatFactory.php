<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Seat;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seat>
 */
class SeatFactory extends Factory
{
    protected $model = Seat::class; 

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'screening_id' => null,
            'user_id' => null, // デフォルトは未予約
            'row' => fake()->randomElement(['A', 'B']),
            'number' => fake()->numberBetween(1, 10),
            'is_reserved' => false,
        ];
    }
}
