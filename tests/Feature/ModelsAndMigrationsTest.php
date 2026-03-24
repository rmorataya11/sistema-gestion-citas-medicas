<?php

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('migraciones aplican y modelos persisten con relaciones correctas', function () {
    $doctor = User::factory()->create(['role' => 'doctor']);

    $patient = Patient::create([
        'first_name' => 'Ana',
        'last_name' => 'Garcia',
        'dui' => '12345678-9',
        'birth_date' => '1990-05-15',
        'phone' => '600000000',
    ]);

    $record = MedicalRecord::create([
        'patient_id' => $patient->id,
        'blood_type' => 'A+',
        'allergies' => 'Ninguna',
        'family_history' => 'Sin antecedentes',
    ]);

    $schedule = DoctorSchedule::create([
        'id_doctor' => $doctor->id,
        'day_of_week' => 1,
        'start_time' => '09:00:00',
        'end_time' => '13:00:00',
    ]);

    $appointment = Appointment::create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'appointment_date' => now()->addDay(),
        'reason' => 'Revision general',
        'status' => 'pending',
    ]);

    expect($patient->fresh()->medicalRecord->id)->toBe($record->id);
    expect($patient->appointments()->count())->toBe(1);
    expect($doctor->appointments()->first()->id)->toBe($appointment->id);
    expect($doctor->doctorSchedules()->first()->id)->toBe($schedule->id);

    expect($record->patient->dui)->toBe('12345678-9');
    expect($schedule->doctor->id)->toBe($doctor->id);

    expect($appointment->patient->first_name)->toBe('Ana');
    expect($appointment->doctor->role)->toBe('doctor');
});

test('el borrado en cascada elimina expediente medico y citas del paciente', function () {
    $doctor = User::factory()->create(['role' => 'doctor']);
    $patient = Patient::create([
        'first_name' => 'Luis',
        'last_name' => 'Perez',
        'dui' => '87654321-0',
        'birth_date' => '1985-01-01',
        'phone' => '611111111',
    ]);

    MedicalRecord::create([
        'patient_id' => $patient->id,
        'blood_type' => 'O+',
        'allergies' => null,
        'family_history' => null,
    ]);

    Appointment::create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'appointment_date' => now(),
        'reason' => null,
        'status' => 'pending',
    ]);

    $patient->delete();

    expect(MedicalRecord::query()->count())->toBe(0);
    expect(Appointment::query()->count())->toBe(0);
});
