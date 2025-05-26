<?php

namespace App\Http\Controllers\Tst;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Payment;
use Carbon\Carbon;
use DB;

class TestController extends Controller
{
    
    public function getMetrics(Request $request)
    {
        $organizerId = auth()->user()->id;

       $totalRegistrations = Ticket::whereHas('event', fn($q) => $q->where('organizer_id', $organizerId))->count();

        $previousRevenue = Payment::whereHas('ticket.event', fn($q) => $q->where('organizer_id', $organizerId))
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('amount');

        $currentRevenue = Payment::whereHas('ticket.event', fn($q) => $q->where('organizer_id', $organizerId))
            ->whereMonth('created_at', now()->month)
            ->sum('amount');



        $revenueGrowth = $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;

        $totalEvents = Event::where('organizer_id', $organizerId)->count();
        

        $activeEvents = Event::where('organizer_id', $organizerId)
            ->whereDate('date', '>=', Carbon::today())
            ->count();


        // $avgSatisfaction = Event::where('organizer_id', $organizerId)->avg('rating') ?? 0;

        return response()->json([
            'totalRegistrations' => $totalRegistrations,
            'registrationGrowth' => 12.5, // Replace with actual logic if needed
            'totalRevenue' => $currentRevenue,
            'revenueGrowth' => round($revenueGrowth, 2),
            // 'avgSatisfaction' => round($avgSatisfaction, 1),
            'satisfactionChange' => 0.3, // Replace with actual logic if needed
            'totalEvents' => $totalEvents,
            'activeEvents' => $activeEvents,
        ]);
    }





    public function getCharts(Request $request)
    {
        $organizerId = $request->user()->id;

        $data = Payment::selectRaw('MONTH(created_at) as month, COUNT(*) as registrations, SUM(amount) as revenue')
            ->whereHas('ticket.event', fn($q) => $q->where('organizer_id', $organizerId))
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $chartData = [];

        foreach ($data as $item) {
            $chartData[] = [
                'month' => $months[$item->month - 1],
                'registrations' => $item->registrations,
                'revenue' => $item->revenue,
            ];
        }

        return response()->json(['registrationData' => $chartData]);
    }






    public function getTopEvents(Request $request)
    {
        $organizerId = $request->user()->id;

       return $events = Event::where('organizer_id', $organizerId)
            ->withCount('tickets')
            ->withAvg('payments', 'amount')
            ->with('payments')
            ->get()
            ->map(function ($event) {
                return [
                    'name' => $event->name,
                    'attendees' => $event->tickets_count,
                    'rating' => round($event->rating ?? 0, 1),
                    'revenue' => $event->tickets->sum(fn($ticket) => optional($ticket->payment)->amount),
                    'status' => ucfirst($event->status),
                ];
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values()
            ;

        return response()->json($events);
    }







    // public function getSatisfaction(Request $request)
    // {
    //     $organizerId = $request->user()->id;

    //     return $counts = Event::where('organizer_id', $organizerId)->select('rating')->get()->map(function ($event) {
    //         if ($event->rating >= 4.5) return 'Excellent';
    //         if ($event->rating >= 3.5) return 'Good';
    //         if ($event->rating >= 2.5) return 'Average';
    //         return 'Poor';
    //     })->countBy();

    //     $total = $counts->sum();

    //     $data = [];
    //     foreach (['Excellent', 'Good', 'Average', 'Poor'] as $category) {
    //         $count = $counts[$category] ?? 0;
    //         $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
    //         $data[] = ['rating' => $category, 'count' => $count, 'percentage' => $percentage];
    //     }

    //     return response()->json([
    //         'data' => $data,
    //         'colors' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
    //     ]);
    // }





    public function getCategoryPerformance(Request $request)
    {
        $organizerId = $request->user()->id;

        $categories = Event::where('organizer_id', $organizerId)
            ->select('category', DB::raw('COUNT(*) as events'))
            ->groupBy('category')
            ->get()
            ->map(function ($row) use ($organizerId) {
                $events = Event::where('organizer_id', $organizerId)->where('category', $row->category)->pluck('id');
                $tickets = Ticket::whereIn('event_id', $events)->get();

                $avgAttendees = $tickets->count() > 0 ? round($tickets->count() / $row->events) : 0;
                $revenue = Payment::whereIn('related_id', $tickets->pluck('id'))->sum('amount');
                $avgRevenue = $row->events > 0 ? round($revenue / $row->events) : 0;

                return [
                    'category' => $row->category,
                    'events' => $row->events,
                    'avgAttendees' => $avgAttendees,
                    'avgRevenue' => $avgRevenue,
                ];
            })
            ;

        return response()->json($categories);
    }







    // public function exportReport(Request $request)
    // {
    //     // Placeholder logic. Replace with actual Excel or PDF export.
    //     return response()->json(['message' => 'Report exported successfully.']);
    // }
}


