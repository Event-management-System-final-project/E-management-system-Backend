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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            // $table->foreign("organizerId")->references("id")->on("organizers")->onDelete("cascade")->nullable();
            $table->foreignId("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->string("title");
            $table->text("description");
            $table->string("event_type");
            $table->date("date");
            $table->time("time");
            $table->string("venue");
            $table->decimal("price", 8, 2);
            $table->integer("expected_attendees");
            $table->string("status")->nullable();
            $table->timestamps();
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
