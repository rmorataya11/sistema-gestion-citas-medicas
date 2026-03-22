<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permisos = [
            'ver expedientes',
            'editar expedientes',
            'eliminar registros',
            'gestionar usuarios',
            'gestionar citas',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }


        $asistente = Role::firstOrCreate(['name' => 'asistente', 'guard_name' => 'web']);
        $asistente->syncPermissions(['gestionar citas', 'ver expedientes']);

        $medico = Role::firstOrCreate(['name' => 'medico', 'guard_name' => 'web']);
        $medico->syncPermissions(['gestionar citas', 'ver expedientes', 'editar expedientes']);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permisos);
    }
}