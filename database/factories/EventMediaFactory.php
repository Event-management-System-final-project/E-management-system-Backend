<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventMedia>
 */
class EventMediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eventid = Event::pluck('id')->toArray();
        return [
            'event_id' => $this->faker->randomElement($eventid),
            'media_type' => $this->faker->randomElement(['image', 'video']),
            'media_url' => $this->faker->imageUrl(),
        ];
    }
}
