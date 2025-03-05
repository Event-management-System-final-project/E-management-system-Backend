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
        Schema::create('event_requests', function (Blueprint $table) {
            $table->id();
            // $table->foreign("eventId")->references("requesterId")->on("events")->onDelete("cascade");
            // $table->string("budget_info");
            // $table->string("services_requested");
            // $table->string("special_requests");
            // $table->string("status");
            $table->timestamps();
            
        });

       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_requests');
    
    }
};
