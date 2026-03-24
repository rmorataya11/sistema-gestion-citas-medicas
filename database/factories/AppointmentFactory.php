<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => User::factory()->doctor(),
            'appointment_date' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'reason' => fake()->sentence(),
            'status' => fake()->randomElement(['pending', 'completed', 'cancelled']),
        ];
    }
}

