<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $request){
       
        $formData = $request->validate([
            'name' => "required",
            'email' => "required|email",
            'password' => "required"

        ]);
        if(User::where('email', $formData["email"])->exists()){
            return "user already registered";
        }

        $user = User::create($formData);

        $token = $user->createToken($user->name);

        return [
            'message' => "Registered succesfully",
            'token' => $token->plainTextToken,
            'user' => $user
        ];

    }

    public function login(Request $request){
        $request->validate([
            'email' => "required",
            'password' => "required"

        ]);

        $user = User::where('email', $request->email)->first();

        
    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return [
        'token' => $user->createToken($user->name)->plainTextToken
    ];

    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();

        return "logged out succesfully";

    }
}
