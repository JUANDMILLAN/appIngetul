<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permisos base del módulo de usuarios
        $perms = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            // Puedes agregar más permisos por módulo (proyectos, cotizaciones, etc.)
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // Crea roles
        $admin   = Role::firstOrCreate(['name' => 'admin']);
        $editor  = Role::firstOrCreate(['name' => 'editor']);
        $viewer  = Role::firstOrCreate(['name' => 'viewer']);

        // Asigna permisos por rol
        $admin->syncPermissions(Permission::all());
        $editor->syncPermissions(['users.view','users.edit']);
        $viewer->syncPermissions(['users.view']);
    }
}
