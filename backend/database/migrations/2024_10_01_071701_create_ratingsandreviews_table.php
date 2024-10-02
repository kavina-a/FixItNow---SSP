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
        Schema::create('ratings_and_reviews', function (Blueprint $table) {
            $table->id();  // Primary key

            // Foreign keys for customer, service provider, and appointment
            $table->unsignedBigInteger('customer_id');  // Foreign key for customer
            $table->unsignedBigInteger('serviceprovider_id');  // Foreign key for service provider
            $table->unsignedBigInteger('appointment_id');  // Foreign key for appointment

            // Rating and review fields
            $table->integer('rating');  // Rating between 1 to 5
            $table->text('review')->nullable();  // Optional review message
            $table->enum('moderation_status', ['pending', 'approved', 'rejected'])->default('pending');  // Add moderation status

            // Foreign key constraints
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');  // References the user who is a customer
            $table->foreign('serviceprovider_id')->references('id')->on('users')->onDelete('cascade');  // References the user who is a service provider
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');  // Appointment ID reference
            
            $table->timestamps();  // Laravel's created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings_and_reviews');
    }
};
