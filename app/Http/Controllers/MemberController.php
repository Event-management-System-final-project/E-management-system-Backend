<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\members;
use App\Models\Event;
use App\Models\Task;


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
            return explode("-", $item)[0]; // Extract first part
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

        // $formData['role'] = 'OT-'.request('role');
       

        if (User::where('email', $formData['email'])->exists()) {
            return response()->json(['message' => 'User already registered'], 409);
        }

        $user = User::create([
            'firstName' => $formData['firstName'],
            'lastName' => $formData['lastName'],
            'email' => $formData['email'],
            'custom_role' => $formData['role'], // Assigning the role with OT prefix
            'password' => bcrypt($formData['password']),
        ]);

        $user->assignRole('OT'); // Assign the role to the user

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










    // FUNCTION TO UPDATE A MEMBER
    public function updateMember(Request $request)
    {
        
        $formData = $request->validate([
            'user_id' => "required",
            'firstName' => "required",
            'lastName' => "required",
            'email' => "required|email",
            'phone' => "required",
            'role' => "required"
        ]);

        $formData['role'] = 'OT-'.request('role');

        // Check if the member exists
        $organizerId = auth()->user()->id;
       if(!members::where('user_id', $formData['user_id'])->where('organizer_id', $organizerId)->exists()){
            return response()->json(['message' => 'Member not found'], 404);
        }


        // Update the user record
        $userToUpdate = User::find($formData['user_id']);
        if ($userToUpdate) {
            $userToUpdate->update([
                'firstName' => $formData['firstName'],
                'lastName' => $formData['lastName'],
                'email' => $formData['email'],
                'role' => $formData['role'],
            ]);
        }

        $member = members::where('user_id', $formData['user_id'])->where('organizer_id', $organizerId)->first();
        // Update the member record
        $member->update([
            'phone' => $formData['phone'],
        ]);

        return response()->json([
            'message' => "Member updated successfully",
            'user' => $userToUpdate,
            'member' => $member
        ]);
    }









    

    // FUNCTION TO DELETE A MEMBER
    public function deleteMember($id)
    {
        $user = auth()->user();
        $member = members::where('user_id', $id)->where('organizer_id', $user->id)->first();

        if (!$member) {
            return response()->json(['message' => 'Member not found'], 404);
        }

        // Delete the member record
        $member->delete();

        // Delete the user record
        User::destroy($id);

        return response()->json(['message' => 'Member deleted successfully']);
    }



    public function assignedTasks(){

     
        $user = auth()->user();

        // Check if the user is a member (not an organizer)
        $member = members::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json(['message' => 'You are not a member.'], 403);
        }

        // Get tasks assigned to the member through the pivot table
        $tasks = $member->tasks()->with('event')->get();

        return response()->json([
            'message' => "Tasks fetched successfully",
            'tasks' => $tasks
        ]);

    }
}
