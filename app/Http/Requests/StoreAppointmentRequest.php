<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Usamos el permiso
        return $this->user()->can('gestionar citas');
    }

    public function rules(): array
    {
        return [
            'doctor_id' => ['required', 'exists:users,id'],
            'patient_id' => ['required', 'exists:patients,id'],
            // Exigimos formato completo: 2026-03-25 09:30:00
            'appointment_date' => ['required', 'date_format:Y-m-d H:i:s', 'after:now'],
            'reason' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $doctorId = $this->input('doctor_id');
            $appointmentDate = $this->input('appointment_date');

            if (!$doctorId || !$appointmentDate) return;

            // Extraemos día y hora de la fecha combinada
            $date = Carbon::parse($appointmentDate);
            $dayOfWeek = $date->dayOfWeek; // 0 (Domingo) al 6 (Sábado)
            $time = $date->format('H:i:s');

            //  Validar que el médico atienda ese día y en esa hora
            // OJO: Carla nombró la columna 'id_doctor' en esta tabla específica
            $isWorking = DB::table('doctor_schedules')
                ->where('id_doctor', $doctorId)
                ->where('day_of_week', $dayOfWeek)
                ->where('start_time', '<=', $time)
                ->where('end_time', '>', $time)
                ->exists();

            if (!$isWorking) {
                $validator->errors()->add(
                    'appointment_date', 
                    'El médico no tiene horario de atención disponible para este día y hora.'
                );
            }

            //  Validar solapamiento (Mismo doctor, misma hora exacta)
            $conflict = Appointment::where('doctor_id', $doctorId)
                ->where('appointment_date', $appointmentDate)
                ->whereIn('status', ['pending', 'completed']) // Si está cancelada, el bloque está libre
                ->exists();

            if ($conflict) {
                $validator->errors()->add(
                    'appointment_date', 
                    'Este horario ya está reservado para el médico seleccionado.'
                );
            }
        });
    }
}