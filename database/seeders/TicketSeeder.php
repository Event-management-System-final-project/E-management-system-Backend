<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\Event;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all event IDs
        $eventIds = Event::pluck('id')->toArray();

        // Seed the tickets table
        Ticket::factory(30)->create(function () use ($eventIds) {
            return [
                'event_id' => fake()->randomElement($eventIds),
            ];
        });
    }
}
