<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class MetricsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Assume you have at least one organizer user with ID 1
        $organizerId = 2;
        $buyerId     = 3; // assign tickets to this user

        // 1) Create 5 events for this organizer
        $events = [];
        for ($i = 1; $i <= 5; $i++) {
            $date = Carbon::today()->subMonths(rand(0, 2))->addDays(rand(-10, 30));
            $time = $faker->time('H:i:s');

            $events[] = DB::table('events')->insertGetId([
                'organizer_id'    => $organizerId,
                'title'           => $faker->sentence(4),
                'description'     => $faker->paragraph(),
                'category'        => $faker->randomElement(['Technology', 'Business', 'Education', 'Healthcare', 'Marketing']),
                'date'            => $date->toDateString(),
                'time'            => $time,
                'location'        => $faker->city . ', ' . $faker->state,
                'attendees'       => $faker->numberBetween(50, 500),
                'budget'          => $faker->numberBetween(1000, 20000),
                'price'           => $faker->numberBetween(20, 200),
                'request_type'    => $faker->randomElement(['Standard', 'Premium', 'VIP']),
                'requirements'    => json_encode($faker->words(5)),
                'approval_status' => $faker->randomElement(['draft', 'pending', 'approved', 'rejected']),
                'event_status'    => $faker->randomElement(['upcoming', 'live', 'completed', 'canceled']),
                'featured'        => $faker->boolean(30),
                'created_at'      => $date->copy()->subDays(rand(10, 30)),
                'updated_at'      => now(),
            ]);
        }

        // 2) For each event, create tickets & payments
        foreach ($events as $eventId) {
            $tickets = [];
            $count   = rand(50, 200);

            for ($j = 0; $j < $count; $j++) {
                $monthOffset = $j < $count/2 ? 1 : 0;
                $createdAt = Carbon::now()
                    ->subMonth($monthOffset)
                    ->subDays(rand(0, 28))
                    ->addHours(rand(0,23))
                    ->addMinutes(rand(0,59));

                $tickets[] = DB::table('tickets')->insertGetId([
                    'event_id'   => $eventId,
                    'user_id'    => $buyerId,
                    'created_at' => $createdAt,
                    'updated_at' => now(),
                ]);
            }

        foreach ($events as $eventId) {
            $paymentDate = DB::table('tickets')->where('event_id', $eventId)->value('created_at');
            DB::table('payments')->insert([
                'user_id'    => $buyerId,
                'trx_ref'    => Str::upper(Str::random(10)),
                'amount'     => rand(20, 200),
                'purpose'    => 'ticket',
                'currency'   => 'USD',
                'related_id' => $eventId,
                'status'     => $faker->randomElement(['pending','paid','failed']),
                'meta'       => json_encode([]),
                'created_at' => $paymentDate,
                'updated_at' => now(),
            ]);
        }
    }
}}

