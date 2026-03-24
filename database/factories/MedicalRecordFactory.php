<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'blood_type' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'allergies' => fake()->boolean(35) ? fake()->words(3, true) : null,
            'family_history' => fake()->boolean(45) ? fake()->sentence() : null,
        ];
    }
}

