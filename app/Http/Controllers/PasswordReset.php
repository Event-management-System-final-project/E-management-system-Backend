<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class PasswordReset extends Controller
{
    public function passwordReset(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        Mail::send('mail', ['token' => $token], function($message) use ($request){
            $message->to($request->email);
            $message->subject('reset your password');
        });

        return response()->json([
            'message' => 'password reset link has been sent to your email'
        ]);

    }


    
}
