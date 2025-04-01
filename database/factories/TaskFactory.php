<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Organizer;
use App\Models\Event;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $organizerId = User::where('role', 'organizer')->pluck('id')->toArray();
        $eventId = Event::where('organizer_id', )->pluck('id')->toArray();
        // $organizerId = Organizer::pluck('id')->toArray();
        return [
            // "title" => $this->faker->sentence,
            // "description" => $this->faker->paragraph,
            // "status" => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'blocked', 'not_started']),
            // "priority" => $this->faker->randomElement(['low', 'medium', 'high']),
            // "assigned_to" => $this->faker->randomElement(), 
            // "deadline" => $this->faker->dateTimeBetween('now', '+1 month'),
            // "organizer_id" => $this->faker->randomElement($organizerId), 
            // "event_id" => $this->faker->randomElement($eventId), 
        ];
    }
}
