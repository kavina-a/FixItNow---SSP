<?php

namespace App\Models;

use App\Models\User;
use App\Enums\UserRole;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone_number',
        'address',
        'city',
        'service_type',
        'years_of_experience',
        'availability',
        'description',
        'languages',
        'profile_image',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'languages' => 'array',
        'role' => UserRole::class,  // Automatically cast the 'role' column to the Role enum
        'availability' => 'boolean', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointmentsAsServiceProvider()
    {
        return $this->hasMany(Appointment::class, 'serviceprovider_id');
    }

    public function ratingReviews()
    {
        return $this->hasMany(RatingandReview::class);
    }
    
}
