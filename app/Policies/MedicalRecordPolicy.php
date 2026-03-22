<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ver expedientes');
    }

    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->hasPermissionTo('ver expedientes');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('editar expedientes');
    }

    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->hasPermissionTo('editar expedientes');
    }

    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->hasPermissionTo('eliminar registros');
    }
}