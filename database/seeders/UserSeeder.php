<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Super Admin',
                'email'    => 'superadmin@flexicell.com',
                'password' => Hash::make('password'),
                'role'     => 'superadmin',
                'phone'    => '09-111111111',
            ],
            [
                'name'     => 'Manager',
                'email'    => 'manager@flexicell.com',
                'password' => Hash::make('password'),
                'role'     => 'manager',
                'phone'    => '09-222222222',
            ],
            [
                'name'     => 'Staff',
                'email'    => 'staff@flexicell.com',
                'password' => Hash::make('password'),
                'role'     => 'staff',
                'phone'    => '09-333333333',
            ],
        ];

        foreach ($users as $data) {
            $roleCode = $data['role'];
            unset($data['role']);

            $role = Role::where('code', $roleCode)->first();

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'role_id'            => $role?->id,
                    'status'             => 1,
                    'is_primary_account' => ($roleCode === 'superadmin'),
                ])
            );

            if ($role) {
                $user->assignRole($role);
            }
        }
    }
}
