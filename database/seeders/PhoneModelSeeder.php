<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Phone_model;
use App\Models\Brands;
use App\Models\Category;

class PhoneModelSeeder extends Seeder
{
    public function run(): void
    {
        $appleBrand    = Brands::where('brand_name', 'Apple')->first();
        $samsungBrand  = Brands::where('brand_name', 'Samsung')->first();
        $xiaomiBrand   = Brands::where('brand_name', 'Xiaomi')->first();
        $oppoBrand     = Brands::where('brand_name', 'OPPO')->first();
        $vivoBrand     = Brands::where('brand_name', 'Vivo')->first();
        $realmeBrand   = Brands::where('brand_name', 'Realme')->first();
        $googleBrand   = Brands::where('brand_name', 'Google')->first();
        $onePlusBrand  = Brands::where('brand_name', 'OnePlus')->first();
        $huaweiBrand   = Brands::where('brand_name', 'Huawei')->first();

        $smartphone    = Category::where('category_name', 'Smartphone')->first();
        $refurbished   = Category::where('category_name', 'Refurbished')->first();

        $models = [
            [
                'brand_id'        => $appleBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'iPhone 17 Pro',
                'processor'       => 'Apple A19 Pro',
                'available_color' => [
                    ['name' => 'Black', 'value' => '#1F1F1F'],
                    ['name' => 'Silver', 'value' => '#D9D9D9'],
                    ['name' => 'Blue', 'value' => '#3E5C76'],
                    ['name' => 'Gold', 'value' => '#C9A66B'],
                ],
                'battery_capacity' => 3600,
                'release_year'    => 2025,
            ],
            [
                'brand_id'        => $appleBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'iPhone 17',
                'processor'       => 'Apple A19',
                'available_color' => [
                    ['name' => 'Black', 'value' => '#000000'],
                    ['name' => 'White', 'value' => '#F5F5F5'],
                    ['name' => 'Blue', 'value' => '#5B84B1'],
                    ['name' => 'Pink', 'value' => '#F4C2C2'],
                ],
                'battery_capacity' => 3561,
                'release_year'    => 2025,
            ],
            [
                'brand_id'        => $samsungBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'Galaxy S26 Ultra',
                'processor'       => 'Snapdragon 8 Elite',
                'available_color' => [
                    ['name' => 'Titanium Black', 'value' => '#2B2B2B'],
                    ['name' => 'Titanium Gray', 'value' => '#8A8A8A'],
                    ['name' => 'Titanium Silver', 'value' => '#C0C0C0'],
                    ['name' => 'Titanium Blue', 'value' => '#5A6E8C'],
                ],
                'battery_capacity' => 5000,
                'release_year'    => 2026,
            ],
            [
                'brand_id'        => $samsungBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'Galaxy A57',
                'processor'       => 'Exynos 1680',
                'available_color' => [
                    ['name' => 'Awesome Black', 'value' => '#111111'],
                    ['name' => 'Awesome White', 'value' => '#FFFFFF'],
                    ['name' => 'Awesome Blue', 'value' => '#4A90E2'],
                ],
                'battery_capacity' => 5000,
                'release_year'    => 2026,
            ],
            [
                'brand_id'        => $xiaomiBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'Xiaomi 17',
                'processor'       => 'Snapdragon 8 Elite',
                'available_color' => [
                    ['name' => 'Black', 'value' => '#000000'],
                    ['name' => 'White', 'value' => '#FFFFFF'],
                    ['name' => 'Green', 'value' => '#4F6F52'],
                ],
                'battery_capacity' => 5000,
                'release_year'    => 2026,
            ],
            [
                'brand_id'        => $xiaomiBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'Redmi Note 14 Pro',
                'processor'       => 'MediaTek Dimensity 7300 Ultra',
                'available_color' => [
                    ['name' => 'Midnight Black', 'value' => '#000000'],
                    ['name' => 'Aurora Purple', 'value' => '#800080'],
                ],
                'battery_capacity' => 5500,
                'release_year'    => 2025,
            ],
            [
                'brand_id'        => $oppoBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'OPPO Reno15 5G',
                'processor'       => 'MediaTek Dimensity 8400',
                'available_color' => [
                    ['name' => 'Rock Gray', 'value' => '#808080'],
                    ['name' => 'Sky Blue', 'value' => '#87CEEB'],
                ],
                'battery_capacity' => 5600,
                'release_year'    => 2026,
            ],
            [
                'brand_id'        => $googleBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'Pixel 10 Pro',
                'processor'       => 'Google Tensor G5',
                'available_color' => [
                    ['name' => 'Obsidian', 'value' => '#2D2926'],
                    ['name' => 'Porcelain', 'value' => '#F5F5F0'],
                    ['name' => 'Bay', 'value' => '#A8C5E6'],
                ],
                'battery_capacity' => 5050,
                'release_year'    => 2025,
            ],
            [
                'brand_id'        => $onePlusBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'OnePlus 13',
                'processor'       => 'Snapdragon 8 Elite',
                'available_color' => [
                    ['name' => 'Black Eclipse', 'value' => '#1C1C1C'],
                    ['name' => 'Emerald', 'value' => '#2E8B57'],
                ],
                'battery_capacity' => 6000,
                'release_year'    => 2025,
            ],
            [
                'brand_id'        => $huaweiBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'HUAWEI Pura 80 Pro',
                'processor'       => 'Kirin',
                'available_color' => [
                    ['name' => 'White', 'value' => '#F5F5F5'],
                    ['name' => 'Black', 'value' => '#000000'],
                ],
                'battery_capacity' => 5200,
                'release_year'    => 2025,
            ],
            [
                'brand_id'        => $vivoBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'Vivo V50',
                'processor'       => 'Snapdragon 7 Gen 3',
                'available_color' => [
                    ['name' => 'Spacious Black', 'value' => '#000000'],
                    ['name' => 'Rose Red', 'value' => '#C2185B'],
                ],
                'battery_capacity' => 6000,
                'release_year'    => 2025,
            ],
            [
                'brand_id'        => $realmeBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'Realme GT 7',
                'processor'       => 'Snapdragon 8 Elite',
                'available_color' => [
                    ['name' => 'Black', 'value' => '#000000'],
                    ['name' => 'Blue', 'value' => '#1E3A8A'],
                ],
                'battery_capacity' => 5800,
                'release_year'    => 2025,
            ],
            [
                'brand_id'        => $samsungBrand->id,
                'category_id'     => $refurbished->id,
                'model_name'      => 'Galaxy S24',
                'processor'       => 'Snapdragon 8 Gen 3',
                'available_color' => [
                    ['name' => 'Onyx Black', 'value' => '#111111'],
                    ['name' => 'Marble Gray', 'value' => '#D3D3D3'],
                ],
                'battery_capacity' => 4000,
                'release_year'    => 2024,
            ],
        ];

        foreach ($models as $model) {
            Phone_model::updateOrCreate(
                ['model_name' => $model['model_name']],
                $model
            );
        }
    }
}
