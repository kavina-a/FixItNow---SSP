<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingandReview extends Model
{
    use HasFactory;

    protected $table = 'ratings_and_reviews';  // Explicitly set the table name

    protected $fillable = [
        'customer_id',
        'serviceprovider_id',
        'appointment_id',
        'rating',
        'review',
        'moderation_status'
    ];

    // Relationship with Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationship with Service Provider
    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class, 'serviceprovider_id');
    }

    // Relationship with Appointment
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
