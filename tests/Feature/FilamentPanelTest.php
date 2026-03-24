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

test('guest is redirected to filament login from admin', function (): void {
    $this->get('/admin')
        ->assertRedirect(route('filament.admin.auth.login'));
});

test('admin can access filament dashboard', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk();
});

test('doctor cannot access user management', function (): void {
    $doctor = User::factory()->create(['role' => 'doctor']);
    $doctor->assignRole('doctor');

    $this->actingAs($doctor)
        ->get('/admin/users')
        ->assertForbidden();
});

test('doctor can access appointments index', function (): void {
    $doctor = User::factory()->create(['role' => 'doctor']);
    $doctor->assignRole('doctor');

    $this->actingAs($doctor)
        ->get('/admin/appointments')
        ->assertOk();
});

test('appointment resource query scopes rows for doctor role', function (): void {
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

test('assistant cannot access user management', function (): void {
    $assistant = User::factory()->create(['role' => 'assistant']);
    $assistant->assignRole('assistant');

    $this->actingAs($assistant)
        ->get('/admin/users')
        ->assertForbidden();
});

test('assistant can access patients index', function (): void {
    $assistant = User::factory()->create(['role' => 'assistant']);
    $assistant->assignRole('assistant');

    $this->actingAs($assistant)
        ->get('/admin/patients')
        ->assertOk();
});
