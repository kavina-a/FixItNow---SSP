<?php

namespace App\Models;

use App\Models\User;
use App\Models\Customer;
use App\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;


    protected $fillable = [
        'customer_id',
        'serviceprovider_id',
        'start_time',
        'end_time',
        'location',
        'status',
        'notes',
        'price',
        'payment_status',
        'service_type',
        'declined_reason',
        'rejection_seen'
    ];

    // Define relationships if needed
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class, 'serviceprovider_id');
    }
    
    public function ratingReview()
    {
        return $this->hasOne(RatingandReview::class);
    }
}
