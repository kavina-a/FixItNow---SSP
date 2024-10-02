<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceProvider;

class ServiceProviderSeeder extends Seeder
{
    public function run()
    {
        ServiceProvider::factory()->count(10)->create();  // Create 10 service providers
    }
}
