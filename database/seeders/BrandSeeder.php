<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brands;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['brand_name' => 'Apple',   'color' => '#6B7280', 'logo' => null],
            ['brand_name' => 'Samsung', 'color' => '#1D4ED8', 'logo' => null],
            ['brand_name' => 'Xiaomi',  'color' => '#FF6900', 'logo' => null],
            ['brand_name' => 'OPPO',    'color' => '#059669', 'logo' => null],
            ['brand_name' => 'Vivo',    'color' => '#7C3AED', 'logo' => null],
            ['brand_name' => 'Realme',  'color' => '#DC2626', 'logo' => null],
            ['brand_name' => 'Google',  'color' => '#4285F4', 'logo' => null],
            ['brand_name' => 'OnePlus', 'color' => '#F31010', 'logo' => null],
            ['brand_name' => 'Huawei',  'color' => '#D51C2C', 'logo' => null],
        ];

        foreach ($brands as $brand) {
            Brands::updateOrCreate(
                ['brand_name' => $brand['brand_name']],
                $brand
            );
        }
    }
}
