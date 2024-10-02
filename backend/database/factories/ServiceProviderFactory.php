<?php

namespace Database\Factories;

use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceProviderFactory extends Factory
{
    protected $model = ServiceProvider::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),  // Create a linked user
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'service_type' => json_encode($this->faker->randomElements(
                ['Plumber',
                'Mechanic',
                'Painter',
                'Electrician',
                'Carpenter',
                'Gardener',
                'Cleaner',
                'Welder',]
                , $this->faker->randomElement([1, 2, 3], [0.2, 0.5, 0.3]))),
            'years_of_experience' => $this->faker->numberBetween(1, 10),
            'availability' => $this->faker->boolean,
            'description' => $this->faker->paragraph,
            'languages' => json_encode($this->faker->randomElements(['English', 'Sinhala', 'Tamil'], 2)),
            'profile_image' => $this->faker->imageUrl(400, 400, 'people'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
