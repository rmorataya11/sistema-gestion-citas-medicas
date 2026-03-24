<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'dui' => fake()->unique()->numerify('########-#'),
            'birth_date' => fake()->dateTimeBetween('-85 years', '-18 years')->format('Y-m-d'),
            'phone' => fake()->numerify('7########'),
        ];
    }
}


