<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            RamOptionSeeder::class,
            StorageOptionSeeder::class,
            ColorOptionSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            PhoneModelSeeder::class,
            SupplierSeeder::class,
            WarrantySeeder::class,
            ProductSeeder::class,
            DeviceSeeder::class,
            CustomerSeeder::class,
            InstallmentRateSeeder::class,
            BlogSeeder::class,
        ]);
    }
}
