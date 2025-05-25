<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Cart;

class UserController extends Controller
{
    // FUNTION TO REGISTER A USER
    public function register(Request $request){
       
        $formData = $request->validate([
            'firstName' => "required",
            'lastName' => "required",
            'email' => "required|email",
            'password' => "required|confirmed",
            

        ]);

        if(User::where('email', $formData['email'])->exists()){
            return "user already registered";
        }

        $user = User::create($formData);
        $user->assignRole($request->role);

       

        return [
            'message' => "Registered succesfully",
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

    // ✅ Check if user exists *before* accessing its properties
    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ])->status(401);
    }

    // Now safe to access role
    $role = $user->getRoleNames()->first();

    return response()->json([
        'token' =>  $user->createToken($user->firstName)->plainTextToken,
        'user' => $user,
        'role' => $role,
    ], 200);
}

    

// FUNCTION TO LOGOUT A USER
    public function logout(Request $request){
        $request->user()->tokens()->delete();

        return "logged out succesfully";

    }




   
    
    public function addToCart(Request $request)
        {
            $request->validate([
                'event_id' => 'required|exists:events,id', // Ensure event exists
            ]);
    
            $user = auth()->user();
    
            // Check if the event is already in the cart
            $existingCartItem = Cart::where('user_id', $user->id)
                ->where('event_id', $request->event_id)
                ->first();
    
            if ($existingCartItem) {
                return response()->json(['message' => 'Event already in cart'], 409);
            }
    
            // Add the event to the cart
            $cartItem = Cart::create([
                'user_id' => $user->id,
                'event_id' => $request->event_id,
                'quantity' => 1, // You can adjust the quantity as needed
            ]);
    
            return response()->json([
                'message' => 'Event added to cart successfully',
                'cart_item' => $cartItem
            ], 201);
        }
    
        // FUNCTION TO SHOW CART ITEMS
    public function showCart()
        {
            $user = auth()->user();
    
            // Get cart items for the user with event details
            $cartItems = Cart::where('user_id', $user->id)
                ->with('event') // Eager load the event relationship
                ->get();
    
            return response()->json([
                'message' => 'Cart items retrieved successfully',
                'cart_items' => $cartItems
            ], 200);
        }
    
        // FUNCTION TO REMOVE EVENT FROM CART
    public function removeFromCart(Request $request)
        {
            $request->validate([
                'event_id' => 'required|exists:events,id',
            ]);
    
            $user = auth()->user();
    
            // Find the cart item
            $cartItem = Cart::where('user_id', $user->id)
                ->where('event_id', $request->event_id)
                ->first();
    
            if (!$cartItem) {
                return response()->json(['message' => 'Event not found in cart'], 404);
            }
    
            // Remove the event from the cart
            $cartItem->delete();
    
            return response()->json(['message' => 'Event removed from cart successfully'], 200);
        }
    }

    

