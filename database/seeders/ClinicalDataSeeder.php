<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ClinicalDataSeeder extends Seeder
{
    private const DOCTORS_COUNT = 12;

    private const ASSISTANTS_COUNT = 7;

    private const PATIENTS_COUNT = 24;

    public function run(): void
    {
        $doctors = User::factory()
            ->count(self::DOCTORS_COUNT)
            ->doctor()
            ->create();

        $assistants = User::factory()
            ->count(self::ASSISTANTS_COUNT)
            ->assistant()
            ->create();

        foreach ($doctors as $doctor) {
            $doctor->assignRole('doctor');
        }

        foreach ($assistants as $assistant) {
            $assistant->assignRole('assistant');
        }

        $patients = Patient::factory()->count(self::PATIENTS_COUNT)->create();

        foreach ($patients as $patient) {
            MedicalRecord::factory()->create([
                'patient_id' => $patient->id,
            ]);
        }

        $this->seedSchedules($doctors->all());
        $this->seedAppointments($doctors->all(), $patients->all());
    }

    private function seedSchedules(array $doctors): void
    {
        $templates = [
            ['day_of_week' => 1, 'start_time' => '08:00:00', 'end_time' => '11:00:00'],
            ['day_of_week' => 3, 'start_time' => '08:00:00', 'end_time' => '11:00:00'],
            ['day_of_week' => 4, 'start_time' => '08:00:00', 'end_time' => '11:00:00'],
            ['day_of_week' => 6, 'start_time' => '15:00:00', 'end_time' => '17:00:00'],
        ];

        foreach ($doctors as $doctor) {
            foreach ($templates as $template) {
                DoctorSchedule::factory()->create([
                    'id_doctor' => $doctor->id,
                    'day_of_week' => $template['day_of_week'],
                    'start_time' => $template['start_time'],
                    'end_time' => $template['end_time'],
                ]);
            }
        }
    }

    private function seedAppointments(array $doctors, array $patients): void
    {
        $patientIds = collect($patients)->pluck('id')->values()->all();
        $usedSlots = [];

        foreach ($doctors as $doctor) {
            $schedules = DoctorSchedule::query()
                ->where('id_doctor', $doctor->id)
                ->get();

            foreach ($schedules as $schedule) {
                $startHour = (int) Carbon::parse($schedule->start_time)->format('H');
                $endHour = (int) Carbon::parse($schedule->end_time)->format('H');

                for ($weekOffset = 0; $weekOffset < 3; $weekOffset++) {
                    for ($hour = $startHour; $hour < $endHour; $hour++) {
                        $slot = now()
                            ->startOfWeek(Carbon::MONDAY)
                            ->addDays($schedule->day_of_week - 1)
                            ->addWeeks($weekOffset + 1)
                            ->setTime($hour, 0, 0);

                        $slotKey = $doctor->id.'|'.$slot->format('Y-m-d H:i:s');
                        if (isset($usedSlots[$slotKey])) {
                            continue;
                        }

                        $usedSlots[$slotKey] = true;

                        Appointment::factory()->create([
                            'patient_id' => fake()->randomElement($patientIds),
                            'doctor_id' => $doctor->id,
                            'appointment_date' => $slot,
                        ]);
                    }
                }
            }
        }
    }
}

