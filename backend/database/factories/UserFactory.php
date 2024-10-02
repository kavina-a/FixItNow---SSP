<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),  // Default password for testing
            'role' => $this->faker->randomElement(['service_provider', 'customer']), // Randomly assign a role
            'remember_token' => Str::random(10),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'), // Users created in the last 3 months
            'updated_at' => now(),
        ];
    }
}
