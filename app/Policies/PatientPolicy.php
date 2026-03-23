<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ver expedientes');
    }

    public function view(User $user, Patient $patient): bool
    {
        return $user->hasPermissionTo('ver expedientes');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('editar expedientes');
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->hasPermissionTo('editar expedientes');
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->hasRole('admin');
    }
}
