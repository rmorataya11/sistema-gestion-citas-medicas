<?php

use App\Filament\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('invitado es redirigido al login de filament desde admin', function (): void {
    $this->get('/admin')
        ->assertRedirect(route('filament.admin.auth.login'));
});

test('admin puede acceder al dashboard de filament', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk();
});

test('doctor no puede acceder a gestion de usuarios', function (): void {
    $doctor = User::factory()->create(['role' => 'doctor']);
    $doctor->assignRole('doctor');

    $this->actingAs($doctor)
        ->get('/admin/users')
        ->assertForbidden();
});

test('doctor puede acceder al listado de citas', function (): void {
    $doctor = User::factory()->create(['role' => 'doctor']);
    $doctor->assignRole('doctor');

    $this->actingAs($doctor)
        ->get('/admin/appointments')
        ->assertOk();
});

test('la consulta de citas filtra registros para rol doctor', function (): void {
    $doc1 = User::factory()->create(['role' => 'doctor', 'email' => 'doc1@test.com']);
    $doc1->assignRole('doctor');
    $doc2 = User::factory()->create(['role' => 'doctor', 'email' => 'doc2@test.com']);
    $doc2->assignRole('doctor');

    $patient = Patient::create([
        'first_name' => 'Pat',
        'last_name' => 'Ient',
        'dui' => '99999999-9',
        'birth_date' => '1990-01-01',
        'phone' => '611111111',
    ]);

    Appointment::create([
        'patient_id' => $patient->id,
        'doctor_id' => $doc2->id,
        'appointment_date' => now()->addDay(),
        'reason' => 'check',
        'status' => 'pending',
    ]);

    $this->actingAs($doc1);
    expect(AppointmentResource::getEloquentQuery()->count())->toBe(0);

    $this->actingAs($doc2);
    expect(AppointmentResource::getEloquentQuery()->count())->toBe(1);
});

test('asistente no puede acceder a gestion de usuarios', function (): void {
    $assistant = User::factory()->create(['role' => 'assistant']);
    $assistant->assignRole('assistant');

    $this->actingAs($assistant)
        ->get('/admin/users')
        ->assertForbidden();
});

test('asistente puede acceder al listado de pacientes', function (): void {
    $assistant = User::factory()->create(['role' => 'assistant']);
    $assistant->assignRole('assistant');

    $this->actingAs($assistant)
        ->get('/admin/patients')
        ->assertOk();
});

test('doctor no puede eliminar citas por auditoria', function (): void {
    $doctor = User::factory()->create(['role' => 'doctor']);
    $doctor->assignRole('doctor');

    $patient = Patient::create([
        'first_name' => 'Mario',
        'last_name' => 'Lopez',
        'dui' => '12345678-1',
        'birth_date' => '1992-02-10',
        'phone' => '70000000',
    ]);

    $appointment = Appointment::create([
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'appointment_date' => now()->addDay(),
        'reason' => 'Control',
        'status' => 'pending',
    ]);

    expect($doctor->can('delete', $appointment))->toBeFalse();
});

