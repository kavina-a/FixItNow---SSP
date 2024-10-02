<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id(); 
            $table->string('name'); 
            $table->text('description'); 
            $table->decimal('price', 8, 2); // Price with 2 decimal precision
            $table->string('service_type'); 
            $table->string('location'); 
            $table->string('approval_status')->default('pending'); 
            $table->unsignedBigInteger('user_id'); // Foreign key to the user (service provider)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps(); // Created at, Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listings');
    }
}
