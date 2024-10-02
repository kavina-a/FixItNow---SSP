<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition()
    {
        $createdAt = $this->faker->dateTimeBetween('-1 month', 'now');

        $acceptedAt = $this->faker->dateTimeBetween($createdAt->modify('+5 minutes'), $createdAt->modify('+40 minutes'));

        $endTime = $this->faker->dateTimeBetween($acceptedAt, '+1 week');

        return [
            'customer_id' => Customer::factory(),  // Create a linked customer
            'serviceprovider_id' => ServiceProvider::factory(),  // Create a linked service provider
            'start_time' => $acceptedAt,  // Start time is when the job is accepted
            'end_time' => $endTime,  // End time of the appointment
            'location' => $this->faker->address,
            'status' => $this->faker->randomElement(['Pending', 'Completed', 'Accepted', 'Declined']),
            'notes' => $this->faker->sentence,
            'price' => $this->faker->numberBetween(1000, 10000),  // Price as an integer
            'service_type' => $this->faker->randomElement([
                'Plumber', 'Mechanic', 'Painter', 'Electrician',
                'Carpenter', 'Gardener', 'Cleaner', 'Welder'
            ]),
            'payment_status' => $this->faker->randomElement(['paid']),
            'declined_reason' => $this->faker->optional()->sentence,
            'rejection_seen' => $this->faker->boolean,
            'completed_at' => $endTime,  // Match end_time with completed_at
            'accepted_at' => $acceptedAt,  // Simulate acceptance time 5 to 40 minutes after created_at
            'created_at' => $createdAt,  // Set creation time
            'updated_at' => now(),
        ];
    }
}
