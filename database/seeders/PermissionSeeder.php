<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Support\RolePermissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guardName = RolePermissions::guardName();

        foreach (RolePermissions::allNames() as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => $guardName],
                ['label' => str_replace(['.', '_'], ' ', ucfirst($name))]
            );
        }

        $allPermissions = Permission::where('guard_name', $guardName)->get();
        $allByName = $allPermissions->keyBy('name');

        $roles = Role::where('guard_name', $guardName)->get();

        foreach ($roles as $role) {
            $permissionNames = RolePermissions::permissionNamesForRole($role->code);
            $perms = collect($permissionNames)
                ->map(fn(string $name) => $allByName->get($name))
                ->filter()
                ->values();

            $role->syncPermissions($perms);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
