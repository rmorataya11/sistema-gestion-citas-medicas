<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function store(StoreAppointmentRequest $request)
    {
        // Obtenemos los datos validados del request
        $data = $request->validated();
        
        // Asignamos el estatus inicial requerido por la migración
        $data['status'] = 'pending';

        // Creamos la cita
        $appointment = Appointment::create($data);

        return response()->json([
            'message' => 'Cita agendada exitosamente.',
            'data' => $appointment
        ], 201);
    }
}