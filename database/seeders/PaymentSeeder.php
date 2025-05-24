<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Payment;


class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Get some users to associate with the payments
        

        // Example payment data
        $payments = [
            [
                'user_id' => 3,
                'trx_ref' => Str::random(10),
                'amount' => 100.00,
                'purpose' => 'ticket',
                'currency' => 'USD',
                'related_id' => 4, // Replace with an actual event or ticket ID
                'status' => 'paid',
                'meta' => json_encode(['item' => 'Ticket for Event A', 'quantity' => 1]),
            ],
        ];

        foreach ($payments as $paymentData) {
            Payment::create($paymentData);
        }

        $this->command->info('Payment table seeded!');
    }
}
