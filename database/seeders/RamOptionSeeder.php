<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RamOptionSeeder extends Seeder
{
    public function run(): void
    {
        $options = ['2GB', '3GB', '4GB', '6GB', '8GB', '12GB', '16GB', '24GB', '32GB'];
        foreach ($options as $opt) {
            DB::table('ram_options')->updateOrInsert(
                ['value' => $opt],
                ['name' => $opt, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
