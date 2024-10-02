<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    // Define the table name if not the default "listings"
    protected $table = 'listings';

    // Define the fillable fields to allow mass assignment
    protected $fillable = [
        'name',
        'description',
        'price',
        'service_type',
        'location',
        'approval_status',
    ];

    // Optionally, you can define constants for the approval statuses
    // const APPROVAL_PENDING = 'pending';
    // const APPROVAL_APPROVED = 'approved';
    // const APPROVAL_REJECTED = 'rejected';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
