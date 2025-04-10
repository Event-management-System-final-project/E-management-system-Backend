<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\members;
use App\Models\Event;


class MemberController extends Controller
{
    public function members()
    {
        // GETTING THE AUTHENTICATED ORGANIZER
        $user = auth()->user();
        
        // GETTING THE ORGANIZER'S MEMBERS
        $members = members::where('organizer_id', $user->id)->with('user')->get();
        // CHANGING THE ORGANIZER'S MEMBERS TO AN ARRAY
        $membersArr = $members->toArray();
       
    //    GETTING THE ROLE OF THE ORGANIZER'S MEMBERS
        $roleData = $members->pluck('user.role')->toArray();

        // Extracting the second part of the role
        $role = array_map(function ($item) {
            return explode("-", $item)[1]; // Extract first part
        }, $roleData);
      
        // assigning the appropriate role to each memeber
        for($i = 0; $i < count($membersArr); $i++){
            $membersArr[$i]['user']['role'] = $role[$i]; // Change role
        }


        return response()->json([
            'message' => "Members fetched successfully",
            'members' => collect($membersArr)
        ]);
    }








// FUNCTION TO CREATE A MEMBER
    public function addMember(Request $request)
    {
        $formData = $request->validate([
            'firstName' => "required",
            'lastName' => "required",
            'email' => "required|email",
            'password' => "required",
            'phone' => "required",
            'role' => "required"
        ]);

        $formData['role'] = 'OT-'.request('role');
       

        if (User::where('email', $formData['email'])->exists()) {
            return response()->json(['message' => 'User already registered'], 409);
        }

        $user = User::create([
            'firstName' => $formData['firstName'],
            'lastName' => $formData['lastName'],
            'email' => $formData['email'],
            'password' => bcrypt($formData['password']),
            'role' => $formData['role'],
        ]);

        // Create a new member record
        $userId = $user->id;
        $organizerId = auth()->user()->id;


        $member = members::create([
            'user_id' => $userId,
            'organizer_id' => $organizerId,
            'phone' => $formData['phone'],
            // Assuming task_id is not needed for a new member
           
        ]);
      

        return response()->json([
            'message' => "Member added successfully",
            'user' => $user,
            'member' => $member
        ]);

    }
}
