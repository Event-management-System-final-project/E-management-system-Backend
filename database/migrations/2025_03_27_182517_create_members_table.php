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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('organizer_id');
            $table->foreign('organizer_id')->references('id')->on('users')->onDelete('cascade');
          
            


            // $table->enum('status', ['active', 'inactive'])->default('active');


            // $table->string('invitation_code')->nullable();
            // $table->string('invitation_status')->default('pending');
            // $table->string('invitation_token')->nullable();
            // $table->string('invitation_token_expiry')->nullable();
            // $table->string('invitation_token_status')->default('pending');
            // $table->string('invitation_token_expiry_status')->default('pending');
            // $table->string('invitation_token_expiry_date')->nullable();
            // $table->string('invitation_token_expiry_time')->nullable();
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
