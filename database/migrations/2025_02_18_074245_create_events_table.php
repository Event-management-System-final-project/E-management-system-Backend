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
            $table->foreignId('organizer_id')->references('id')->on('users')->onDelete('cascade')->nullable();
            $table->string("title");
            $table->text("description");
            $table->string("category");
            $table->date("date");
            $table->time("time");
            $table->string("location");
            $table->decimal("price", 8, 2);
            $table->integer("attendees");
            $table->boolean("featured")->default(false);
            $table->enum("approval_status", ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->enum("event_status", ['upcoming', 'live', 'completed', 'canceled'])->nullable();
            $table->timestamps();
          
        });

        Schema::create('event_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->string('media_type')->default('image');
            $table->string('media_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_media');
        Schema::dropIfExists('events');
        
    }
};
