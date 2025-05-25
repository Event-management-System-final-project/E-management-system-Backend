<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Event, Ticket, Payment, TicketRecipient};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\ChapaService;
use App\Notifications\TicketPurchaseNotification;


class TicketController extends Controller
{
    protected $chapaService;

    public function __construct(ChapaService $chapaService)
    {
        $this->chapaService = $chapaService;
    }

    public function buy(Request $request)
    {
        
        $user = auth()->user();
       $event = Event::findOrFail($request->event_id);
        $recipients = $request->input('recipients', []); // [{type: user}, {type: guest, name: ..., email: ...}]
        //  $quantity = count($recipients);
        // if($quantity === 0){
        //     $quantity = 1;
        // }

      
        if ($event->price == 0) {
            // Free event: Only allow 1 ticket per user
            $exists = Ticket::where('user_id', $user->id)->where('event_id', $event->id)->exists();
            if ($exists) {
                return 'You already claimed a free ticket.';
            }

            $ticket = $this->createTicket($user->id, $event->id);
            return 'Free ticket issued.';
        }

        // Paid event
        $trx_ref = uniqid('tx_', true);
        // $amount = $event->price * $quantity;

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
                'purpose' => 'ticket',
                'currency' => 'ETB',
                'related_id' => $event->id,
                'status' => 'pending',
                'meta' => [
                    'recipients' => $recipients,
                    'ticket_type' => $request->ticket_type,
                ],
            ]);
        }

        return $response['data']['checkout_url'];
    }




    public function verifyPayment(Request $request)
    {
        $trx_ref = $request->query('trx_ref');
        $payment = Payment::where('trx_ref', $trx_ref)->firstOrFail();

        $response = $this->chapaService->verifyPayment($trx_ref);
        
        if ($response['status'] === 'success' && $payment->status === 'pending') {
            $payment->update(['status' => 'paid']);

            $event_id = $payment->related_id;
            $recipients = $payment->meta['recipients'] ?? [];
            $ticket_type = $payment->meta['ticket_type'] ?? null;

            foreach ($recipients as $recipient) {
                if ($recipient['type'] === 'user') {
                    $this->createTicket($payment->user_id, $event_id, $trx_ref, null, $ticket_type);
                } elseif ($recipient['type'] === 'guest') {
                    $this->createTicket(null, $event_id, $trx_ref, $recipient);
                }
            }

            return 'Payment verified, tickets issued.';
        }

        return 'Payment verification failed.';
    }





    protected function createTicket($user_id, $event_id, $trx_ref = null, $recipientData = null, $ticket_type = null)
    {
        $ticket = Ticket::create([
            'user_id' => $user_id,
            'event_id' => $event_id,
            'trx_ref' => $trx_ref,
            'ticket_type' => $ticket_type,
            'is_paid_for' => true,
        ]);

        if ($recipientData) {
            TicketRecipient::create([
                'ticket_id' => $ticket->id,
                'name' => $recipientData['name'],
                'email' => $recipientData['email'] ?? null,
                'ticket_type' => $ticket_type,
                'is_paid_for' => true,
            ]);
        }

        $user = User::where("id", $user_id)->first();
        Notification::send($user, new TicketPurchaseNotification($ticket));


        return $ticket;
    }





    

  public function userTicket()
{
    $user = auth()->user();

    $tickets = Ticket::with(['event', 'recipient'])
        ->where('user_id', $user->id)
        ->get();

    return response()->json($tickets);
}



}

