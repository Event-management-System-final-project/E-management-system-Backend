<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Organizer;
use Illuminate\Support\Arr;
use Carbon\Carbon;

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


        $approvalStatus = Arr::random(['draft', 'pending', 'approved', 'rejected']);
        $eventStatus = null;

        if ($approvalStatus === 'approved') {
            $eventStatus = Arr::random(['upcoming', 'live', 'completed', 'canceled']);
        }

        $startTime = $this->faker->dateTimeBetween('+1 days', '+10 days');
        $endTime = (clone $startTime)->modify('+2 hours');

        // Adjust dates if live or completed
        if ($eventStatus === 'live') {
            $startTime = Carbon::now()->subMinutes(30);
            $endTime = Carbon::now()->addMinutes(90);
        } elseif ($eventStatus === 'completed') {
            $startTime = Carbon::now()->subDays(2);
            $endTime = Carbon::now()->subDays(1);
        }


        $organizerid = User::where('role', 'organizer')->pluck('id')->toArray();
        return [
            'organizer_id' => $this->faker->numberBetween(1,2),
            'title' => fake()->word(),
            'description' => fake()->paragraph(),
            'request_type' => fake()->randomElement(['organizer', 'user']),
            'date' => fake()->date(),
            'time' => fake()->time(),
            'category' => fake()->word(),
            'attendees' => fake()->numberBetween(10, 500),
            'location' => fake()->address(),
            'price' => fake()->randomFloat(2, 0, 1000),
            'featured'=> fake()->boolean(),
            'approval_status' => $approvalStatus,
            'event_status' => $eventStatus,
            'budget' => fake()->randomFloat(2, 0, 1000),
        ];

       
    }


       
    }

    

