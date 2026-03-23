<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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

        $assistant = Role::firstOrCreate(['name' => 'assistant', 'guard_name' => 'web']);
        $assistant->syncPermissions(['gestionar citas', 'ver expedientes']);

        $doctor = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
        $doctor->syncPermissions(['gestionar citas', 'ver expedientes', 'editar expedientes']);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($permisos);
    }
}
