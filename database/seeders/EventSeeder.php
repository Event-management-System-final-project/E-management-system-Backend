<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Organizer;
use App\Models\EventMedia;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     



    $event = Event::create([
        'organizer_id' => 2,
        'title' => 'Tech Fest 2025',
        'description' => 'A big tech event.',
        'category' => 'Tech',
        'date' => '2025-08-01',
        'time' => '12:00:00',
        'location' => 'Addis Ababa',
        'attendees' => 100,
        'budget' => 50000,
        'price' => 100,
        'request_type' => 'organizer',
      
        'approval_status' => 'approved',
        'event_status' => 'upcoming',
        'featured' => true,
    ]);

    // Attach media to the event
    $event->eventMedia()->createMany([
        [
            'media_type' => 'image',
            'media_url' => 'EventPoser/photo1.jpg', // You can customize the path
        ],
       
    ]);


       

       
    }
}
