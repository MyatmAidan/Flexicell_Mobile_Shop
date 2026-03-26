<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PermissionSeeder::class,
            RamOptionSeeder::class,
            StorageOptionSeeder::class,
            ColorOptionSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            PhoneModelSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            DeviceSeeder::class,
            InstallmentRateSeeder::class,
            SupplierSeeder::class,
            BlogSeeder::class,
        ]);
    }
}
