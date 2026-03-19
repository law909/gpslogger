<?php

namespace Database\Factories;

use App\Models\FollowedPerson;
use App\Models\LocationUpdate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LocationUpdate>
 */
class LocationUpdateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'followed_person_id' => FollowedPerson::factory(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'accuracy' => $this->faker->randomFloat(2, 1, 100),
            'battery_level' => $this->faker->numberBetween(0, 100),
            'recorded_at' => $this->faker->dateTimeBetween('-1 day', 'now'),
        ];
    }
}
