<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create the admin user
        User::create([
            'email' => 'admin@fixitnow.com',
            'password' => Hash::make('password123'), 
            'role' => UserRole::Administrator->value, 
        ]);
    }
}
