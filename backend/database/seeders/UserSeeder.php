<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Generate 50 users with a mix of administrators, service providers, and customers
        User::factory()->count(10)->create();
    }
}
