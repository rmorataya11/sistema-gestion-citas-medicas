<?php

namespace Database\Factories;

use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorSchedule>
 */
class DoctorScheduleFactory extends Factory
{
    protected $model = DoctorSchedule::class;

    public function definition(): array
    {
        $startHour = fake()->numberBetween(7, 15);

        return [
            'id_doctor' => User::factory()->doctor(),
            'day_of_week' => fake()->numberBetween(1, 6),
            'start_time' => sprintf('%02d:00:00', $startHour),
            'end_time' => sprintf('%02d:00:00', min($startHour + 3, 20)),
        ];
    }
}

