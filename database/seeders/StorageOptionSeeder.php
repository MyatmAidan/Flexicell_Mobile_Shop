<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StorageOptionSeeder extends Seeder
{
    public function run(): void
    {
        $options = ['16GB', '32GB', '64GB', '128GB', '256GB', '512GB', '1TB', '2TB'];
        foreach ($options as $opt) {
            DB::table('storage_options')->updateOrInsert(
                ['value' => $opt],
                ['name' => $opt, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
