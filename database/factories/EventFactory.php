<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Organizer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'organizer_id' => Organizer::factory(),
            'title' => fake()->word(),
            'description' => fake()->paragraph(),
            'date' => fake()->date(),
            'time' => fake()->time(),
            'category' => fake()->word(),
            'attendees' => fake()->numberBetween(10, 500),
            'location' => fake()->address(),
            'price' => fake()->randomFloat(2, 0, 1000),
            'featured'=> fake()->boolean(),
            'status' => fake()->randomElement(['upcoming', 'ongoing', 'past']),
        ];
    }

    // EVENT CREATED BY THE USER
    public function byUser()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => User::factory(),
                'organizer_id' => null,
            ];
        });
    }

    // EVENT CREATED BY THE ORGANIZER
    public function byOrganizer()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => null,
                'organizer_id' => Organizer::factory(),
            ];
        });
    }
}
