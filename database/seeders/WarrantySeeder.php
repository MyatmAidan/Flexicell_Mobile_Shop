<?php

namespace Database\Seeders;

use App\Models\Warranty;
use Illuminate\Database\Seeder;

class WarrantySeeder extends Seeder
{
    public function run(): void
    {
        $warranties = [
            ['warranty_month' => 1,  'status' => 'active'],
            ['warranty_month' => 3,  'status' => 'active'],
            ['warranty_month' => 6,  'status' => 'active'],
            ['warranty_month' => 12, 'status' => 'active'],
            ['warranty_month' => 24, 'status' => 'active'],
        ];

        foreach ($warranties as $w) {
            Warranty::updateOrCreate(
                ['warranty_month' => $w['warranty_month']],
                $w
            );
        }
    }
}
