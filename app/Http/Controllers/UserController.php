<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // FUNTION TO REGISTER A USER
    public function register(Request $request){
       
        $formData = $request->validate([
            'lastName' => "required",
            'firstName' => "required",
            'email' => "required|email",
            'password' => "required|confirmed",
            'role' => "required"

        ]);

        if(User::where('email', $formData['email'])->exists()){
            return "user already registered";
        }

        $user = User::create($formData);

        $token = $user->createToken($user->firstName);

        return [
            'message' => "Registered succesfully",
            'token' => $token->plainTextToken,
            'user' => $user
        ];

    }

    // FUNCTION TO LOGIN A USER
    public function login(Request $request){
        $request->validate([
            'email' => "required|email",
            'password' => "required"

        ]);

        $user = User::where('email', $request->email)->first();

        
    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ])->status(401);
    }

    return response()->json(
        [
            'token' =>  $user->createToken($user->firstName)->plainTextToken,
            'user' => $user->role
        ], 200
    );



    }

    

// FUNCTION TO LOGOUT A USER
    public function logout(Request $request){
        $request->user()->tokens()->delete();

        return "logged out succesfully";

    }
}
