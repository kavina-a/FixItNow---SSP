<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->unsignedBigInteger('customer_id');  // Foreign key for customer
            $table->unsignedBigInteger('serviceprovider_id');  // Foreign key for service provider
            $table->dateTime('start_time');  // Start time of the appointment
            $table->dateTime('end_time')->nullable();  // End time, set when the job is completed
            $table->string('location');  // Location of the service
            $table->string('status');  // Status (e.g., pending, confirmed, completed)
            $table->text('notes')->nullable();  // Optional notes from the customer
            $table->integer('price');  // Change price to integer
            $table->string('service_type');  // Type of service
            $table->string('payment_status');  // Payment status (e.g., paid, pending, unpaid)
            $table->string('declined_reason')->nullable();  // Payment status (e.g., paid, pending, unpaid)
            $table->boolean('rejection_seen')->nullable();
        
            // Foreign key constraints (relationships with customer and service provider)
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('serviceprovider_id')->references('id')->on('users')->onDelete('cascade');
        
            $table->timestamps();  // Laravel's created_at and updated_at columns

            $table->string('proof_image')->nullable();  // Store the path to the proof image
            $table->timestamp('completed_at')->nullable();  // Track when the appointment was completed

            $table->timestamp('accepted_at')->nullable();  // Add accepted_at column

        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
