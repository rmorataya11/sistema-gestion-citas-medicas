<?php

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);
});

function crearDoctorConHorario(CarbonInterface $fechaCita): User
{
    $doctor = User::factory()->create(['role' => 'doctor']);
    $doctor->assignRole('doctor');

    DoctorSchedule::create([
        'id_doctor' => $doctor->id,
        'day_of_week' => $fechaCita->dayOfWeek,
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
    ]);

    return $doctor;
}

function crearPaciente(string $dui = '12345678-9'): Patient
{
    return Patient::create([
        'first_name' => 'Juan',
        'last_name' => 'Perez',
        'dui' => $dui,
        'birth_date' => '1995-05-10',
        'phone' => '70000000',
    ]);
}

test('api crea una cita valida y responde 201', function (): void {
    $asistente = User::factory()->create(['role' => 'assistant']);
    $asistente->assignRole('assistant');

    Sanctum::actingAs($asistente);

    $fechaCita = now()->next(Carbon::MONDAY)->setTime(9, 0, 0);
    $doctor = crearDoctorConHorario($fechaCita);
    $paciente = crearPaciente('11111111-1');

    $payload = [
        'doctor_id' => $doctor->id,
        'patient_id' => $paciente->id,
        'appointment_date' => $fechaCita->format('Y-m-d H:i:s'),
        'reason' => 'Control general',
    ];

    $this->postJson('/api/appointments', $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Cita agendada exitosamente.')
        ->assertJsonPath('data.doctor_id', $doctor->id)
        ->assertJsonPath('data.patient_id', $paciente->id)
        ->assertJsonPath('data.status', 'pending');

    expect(Appointment::query()->count())->toBe(1);
});

test('api rechaza cita duplicada para el mismo medico y misma hora con 422', function (): void {
    $asistente = User::factory()->create(['role' => 'assistant']);
    $asistente->assignRole('assistant');

    Sanctum::actingAs($asistente);

    $fechaCita = now()->next(Carbon::WEDNESDAY)->setTime(10, 0, 0);
    $doctor = crearDoctorConHorario($fechaCita);
    $paciente1 = crearPaciente('22222222-2');
    $paciente2 = crearPaciente('33333333-3');

    Appointment::create([
        'doctor_id' => $doctor->id,
        'patient_id' => $paciente1->id,
        'appointment_date' => $fechaCita,
        'reason' => 'Revision',
        'status' => 'pending',
    ]);

    $payload = [
        'doctor_id' => $doctor->id,
        'patient_id' => $paciente2->id,
        'appointment_date' => $fechaCita->format('Y-m-d H:i:s'),
        'reason' => 'Seguimiento',
    ];

    $this->postJson('/api/appointments', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['appointment_date']);

    expect(Appointment::query()->count())->toBe(1);
});

test('api requiere autenticacion con sanctum y responde 401', function (): void {
    $fechaCita = now()->next(Carbon::THURSDAY)->setTime(9, 0, 0);
    $doctor = crearDoctorConHorario($fechaCita);
    $paciente = crearPaciente('44444444-4');

    $payload = [
        'doctor_id' => $doctor->id,
        'patient_id' => $paciente->id,
        'appointment_date' => $fechaCita->format('Y-m-d H:i:s'),
        'reason' => 'Consulta',
    ];

    $this->postJson('/api/appointments', $payload)
        ->assertUnauthorized();
});

test('api rechaza usuario sin permiso gestionar citas con 403', function (): void {
    $usuarioSinPermiso = User::factory()->create(['role' => 'doctor']);
    $usuarioSinPermiso->syncRoles([]);

    Sanctum::actingAs($usuarioSinPermiso);

    $fechaCita = now()->next(Carbon::FRIDAY)->setTime(9, 0, 0);
    $doctor = crearDoctorConHorario($fechaCita);
    $paciente = crearPaciente('55555555-5');

    $payload = [
        'doctor_id' => $doctor->id,
        'patient_id' => $paciente->id,
        'appointment_date' => $fechaCita->format('Y-m-d H:i:s'),
        'reason' => 'Consulta',
    ];

    $this->postJson('/api/appointments', $payload)
        ->assertForbidden();
});

test('api rechaza cita fuera del horario del medico con 422', function (): void {
    $asistente = User::factory()->create(['role' => 'assistant']);
    $asistente->assignRole('assistant');

    Sanctum::actingAs($asistente);

    $fechaCita = now()->next(Carbon::MONDAY)->setTime(14, 0, 0);
    $doctor = crearDoctorConHorario($fechaCita->copy()->setTime(9, 0, 0));
    $paciente = crearPaciente('66666666-6');

    $payload = [
        'doctor_id' => $doctor->id,
        'patient_id' => $paciente->id,
        'appointment_date' => $fechaCita->format('Y-m-d H:i:s'),
        'reason' => 'Consulta fuera de horario',
    ];

    $this->postJson('/api/appointments', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['appointment_date']);
});


