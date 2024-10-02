<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;

    //! IDK ABOUT THIS PODDAK CHECK AGAIN ITS FINE FOR NOW KEEP IT , LETS DEVELOP THE DASHBOARD LATER
    //! THERE IS ALSO THIS PART TO PUT WHEN ONLY AN ADMIN CAN LOG IN ( THATS USING JETSTREAM SERVICE PROVIDER ( DO THAT ))

    protected $fillable = ['user_id', 'name', 'phone_number'];

    protected $casts = [
        'role' => UserRole::class,  // Automatically cast the 'role' column to the Role enum
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
