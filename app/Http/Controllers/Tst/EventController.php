<?php

namespace App\Http\Controllers\Tst;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Payment;

class EventController extends Controller
{


 public function summary(Request $request)
    {
        $organizerId = auth()->user()->id;

        if (!$organizerId) {
            return response()->json(['error' => 'Organizer ID is required'], 400);
        }

        // Get all events of this organizer
        $events = Event::with(['tickets', 'payments'])->where('organizer_id', $organizerId)->take(2)->get();

        $totalEvents = $events->count();

      $totalTicketsSold = $events->sum(function ($event) {
        return $event->payments->where('status', 'paid')->where('purpose', 'ticket')->sum('amount');
    });

        $totalRevenue = $events->sum(function ($event) {
            return $event->payments->sum('amount');
        });

        $totalAttendees = $events->sum('attendees'); // Assuming 'attendees' is a column on events table

        // Prepare event data for frontend (you might want to customize)
        $eventData = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'name' => $event->name,
                'date' => $event->date,
                'time' => $event->time,
                'location' => $event->location,
                'status' => $event->status,
                // Number of ticket records (sold)
                'ticketsSold' => $event->sum('id'),
                'totalTickets' => $event->sum('attendees'), // assuming 'attendees' is a column on events table
                'revenue' => $event->payments->sum('amount'),
                'attendees' => $event->attendees,
                
                // Add ticket types and recent purchases if needed
                'ticketTypes' => $event->tickets->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'name' => $ticket->type_name,
                        'description' => $ticket->description,
                        'price' => $ticket->price,
                        'sold' => $ticket->quantity,
                        'total' => $ticket->total_quantity,
                        'available' => $ticket->total_quantity - $ticket->quantity,
                    ];
                }),
                'recentPurchases' => $event->payments->sortByDesc('created_at')->take(5)->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'buyerName' => $payment->buyer_name,
                        'ticketType' => $payment->ticket_type,
                        'quantity' => $payment->quantity,
                        'amount' => $payment->amount,
                        'purchaseDate' => $payment->created_at? $payment->created_at->toIso8601String(): null,
                        
                    ];
                }),
            ];
        });

        return response()->json([
            'totalEvents' => $totalEvents,
            'totalTicketsSold' => $totalTicketsSold,
            'totalRevenue' => $totalRevenue,
            'totalAttendees' => $totalAttendees,
            'events' => $eventData,
        ]);
    }
}





