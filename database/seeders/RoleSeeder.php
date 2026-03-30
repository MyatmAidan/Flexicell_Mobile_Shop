<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $guardName = 'web';

        $roles = [
            ['name' => 'Super Admin', 'code' => 'superadmin', 'sort_order' => 0],
            ['name' => 'Manager',     'code' => 'manager',    'sort_order' => 1],
            ['name' => 'Staff',       'code' => 'staff',      'sort_order' => 2],
            ['name' => 'Customer',    'code' => 'user',       'sort_order' => 3],
        ];

        foreach ($roles as $roleDef) {
            Role::firstOrCreate(
                ['code' => $roleDef['code'], 'guard_name' => $guardName],
                ['name' => $roleDef['name'], 'sort_order' => $roleDef['sort_order']]
            );
        }
    }
}
