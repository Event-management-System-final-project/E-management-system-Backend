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
        $organizer = User::where('role', 'organizer')->inRandomOrder()->first();
        $eventId = Event::pluck('id')->toArray();

        return [
            "title" => $this->faker->sentence,
            "category" => $this->faker->word,
            "description" => $this->faker->paragraph,
            "budget" => $this->faker->randomFloat(2, 0, 1000),
            "budget_spent" => $this->faker->randomFloat(2, 0, 1000),
            "status" => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'blocked', 'not_started']),
            "priority" => $this->faker->randomElement(['low', 'medium', 'high']),
            "due_date" => $this->faker->dateTimeBetween('now', '+1 month'),
            "organizer_id" => $organizer->id, 
            "event_id" => $this->faker->randomElement($eventId), 
        ];
    }
}
