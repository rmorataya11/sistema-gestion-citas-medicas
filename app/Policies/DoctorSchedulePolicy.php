<?php

namespace App\Policies;

use App\Models\DoctorSchedule;
use App\Models\User;

class DoctorSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'assistant']);
    }

    public function view(User $user, DoctorSchedule $doctorSchedule): bool
    {
        if ($user->hasRole('doctor')) {
            return (int) $user->id === (int) $doctorSchedule->id_doctor;
        }

        return $user->hasAnyRole(['admin', 'assistant']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'assistant']);
    }

    public function update(User $user, DoctorSchedule $doctorSchedule): bool
    {
        return $this->view($user, $doctorSchedule);
    }

    public function delete(User $user, DoctorSchedule $doctorSchedule): bool
    {
        return $user->hasAnyRole(['admin', 'assistant']);
    }
}
