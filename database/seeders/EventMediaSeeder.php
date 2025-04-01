<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\eventMedia;
use App\Models\Event;

class EventMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();

        foreach($events as $event){
            EventMedia::factory()->count(3)->create([
                "event_id" => $event->id,
            ]);
        }
    }
}
