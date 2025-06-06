<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('qr_code_path')->nullable();
            $table->string('ticket_type')->nullable(); // e.g. VIP, Regular, etc.
            $table->boolean('is_paid_for')->default(false);
            $table->string('trx_ref')->nullable();      // Chapa transaction reference
            $table->timestamps(); 

        });
    }

    /**
     * Reverse the migrations.`
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
