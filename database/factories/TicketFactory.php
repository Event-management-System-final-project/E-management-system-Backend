<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        "event_id" => Event::factory(),
        "ticket_type" => $this->faker->randomElement(['regular', 'vip']),
        'price' => $this->faker->randomFloat(2, 10, 100),
        'qr_code' => $this->faker->uuid(), 
        'availability_status' => $this->faker->randomElement(['available', 'sold out', 'reserved']),
        ];
    }
}
