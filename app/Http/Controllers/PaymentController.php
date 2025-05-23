<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChapaService;
use App\Models\{Event, Payment};
use Illuminate\Support\Str;

class PaymentController extends Controller
{

    public function __construct(ChapaService $chapaService)
    {
        $this->chapaService = $chapaService;
    }


    public function eventRequestPayment(Request $request){
        $user = auth()->user();
        $event_id = $request->event_id;
        $event = Event::where('id', $event_id)->first();
        if(!$event){
            return "event doesnt exist";

        }
        $trx_ref = uniqid('tx_', true);

         $data = [
            'amount' => $request->input('amount'),
            'currency' => 'ETB',
            'email' => $request->input('email'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'tx_ref' => $trx_ref,
            'callback_url' => route('payment.callback') . '?trx_ref=' . $trx_ref,
        ];

         $response = $this->chapaService->initializePayment($data);

        
        if ($response['status'] === 'success') {
            Payment::create([
                'user_id' => $user->id,
                'trx_ref' => $trx_ref,
                'amount' => $request->input('amount'),
                'phone' => $request->input('phone'),
                'purpose' => 'event_request',
                'currency' => 'ETB',
                'related_id' => $event->id,
                'status' => 'pending',
        
            ]);
        }

        return $response['data']['checkout_url'];
    }

    
}
