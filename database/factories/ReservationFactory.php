<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'name' => $this->faker->name(),
        'email' => $this->faker->unique()->safeEmail(),
        'reservation_time' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
        'guests' => $this->faker->NumberBetween(1,10),
        'note'=>$this->faker->sentence()
    ];
    }
}
