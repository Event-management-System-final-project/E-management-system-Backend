<?php

// app/Http/Controllers/Api/TeamAssignmentController.php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Member;

class TeamAssignmentController extends Controller
{
    // GET /api/admin/events/upcoming
    public function upcomingEvents()
    {
        $events = Event::where('event_status', 'upcoming')
            ->orderBy('date')
            ->get(['id','title','date','location'])
            ->map(fn($e) => [
                'id'    => $e->id,
                'title' => $e->title,
                'date'  => $e->date->format('M d, Y'),
                'location' => $e->location,
            ]);

        return response()->json(['upcomingEvents' => $events]);
    }

    // GET /api/admin/team
    public function teamMembers(Request $request)
    {
        // assuming admin user sees all members; if you need to filter by organizer:
        // $orgId = $request->user()->id;
        // $members = Member::with('user')->where('organizer_id',$orgId)->get();

        $members = Member::with('user')->get();

        $list = $members->map(fn($m) => [
            'id'        => $m->id,
            'firstName' => $m->user->firstName,
            'lastName'  => $m->user->lastName,
            'role'      => $m->user->role,
            'phone'     => $m->phone,
        ]);

        return response()->json(['teamMembers' => $list]);
    }

    // POST /api/admin/events/{event}/assign-teams
    public function assignTeams(Request $request, Event $event)
    {
        $request->validate([
            'team_member_ids'   => 'required|array',
            'team_member_ids.*' => 'exists:members,id',
        ]);

        // sync will attach and detach as needed
        $event->members()->sync($request->team_member_ids);

        return response()->json([
            'message' => 'Teams assigned successfully.',
            'assigned' => $event->members()->pluck('members.id'),
        ]);
    }


    
}

