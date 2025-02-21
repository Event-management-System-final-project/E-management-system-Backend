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

        Schema::create('event_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('media_type');
            $table->string('media_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_requests');
        Schema::dropIfExists('events');
    }
};
