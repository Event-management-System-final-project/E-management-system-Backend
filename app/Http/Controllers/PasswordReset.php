<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class PasswordReset extends Controller
{
    public function sendEmail(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        Mail::send('mail', ['token' => $token, 'email' => $request->email], function($message) use ($request){
            $message->to($request->email);
            $message->subject('reset your password');
        });

        return response()->json([
            'message' => 'password reset link has been sent to your email'
        ]);

    }

    
    public function resetPassword(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $token = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if(!$token){
            return response()->json([
                'message' => 'invalid token'
            ], 400);
        }

        if($token->token !== $request->token){
            return response()->json([
                'message' => 'invalid token'
            ], 400);
        }

        DB::table('users')->where('email', $request->email)->update([
            'password' => bcrypt($request->password)
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'password reset successful'
        ]);
    }



}
