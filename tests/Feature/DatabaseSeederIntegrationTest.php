<?php

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('el seeder de base de datos crea datos clinicos representativos', function (): void {
    $this->seed(DatabaseSeeder::class);

    expect(User::query()->count())->toBeGreaterThanOrEqual(20)
        ->and(User::query()->where('role', 'doctor')->count())->toBeGreaterThanOrEqual(12)
        ->and(User::query()->where('role', 'assistant')->count())->toBeGreaterThanOrEqual(7)
        ->and(Patient::query()->count())->toBeGreaterThanOrEqual(20)
        ->and(MedicalRecord::query()->count())->toBe(Patient::query()->count())
        ->and(DoctorSchedule::query()->count())->toBeGreaterThanOrEqual(20)
        ->and(Appointment::query()->count())->toBeGreaterThanOrEqual(20);
});

test('el seeder de base de datos evita citas duplicadas del mismo medico en el mismo horario', function (): void {
    $this->seed(DatabaseSeeder::class);

    $duplicates = Appointment::query()
        ->selectRaw('doctor_id, appointment_date, COUNT(*) as total')
        ->groupBy('doctor_id', 'appointment_date')
        ->having('total', '>', 1)
        ->count();

    expect($duplicates)->toBe(0);
});


