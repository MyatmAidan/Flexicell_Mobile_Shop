<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }
    }
}

