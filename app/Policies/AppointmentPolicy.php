<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('gestionar citas');
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if (! $user->hasPermissionTo('gestionar citas')) {
            return false;
        }

        if ($user->hasRole('doctor')) {
            return (int) $user->id === (int) $appointment->doctor_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('gestionar citas');
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $this->view($user, $appointment);
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('doctor')) {
            return false;
        }

        return $user->hasPermissionTo('eliminar registros');
    }

    public function deleteAny(User $user): bool
    {
        if ($user->hasRole('doctor')) {
            return false;
        }

        return $user->hasPermissionTo('eliminar registros');
    }
}
