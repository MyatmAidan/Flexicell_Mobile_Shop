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
        $gaming        = Category::where('category_name', 'Gaming Phone')->first();
        $refurbished   = Category::where('category_name', 'Refurbished')->first();

        $models = [
            [
                'brand_id'        => $appleBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'iPhone 15 Pro',
                'processor'       => 'Apple A17 Pro',
                'available_color' => [
                    ['name' => 'Black Titanium', 'value' => '#333333'],
                    ['name' => 'White Titanium', 'value' => '#F2F2F2'],
                    ['name' => 'Natural Titanium', 'value' => '#BEBEBE'],
                    ['name' => 'Blue Titanium', 'value' => '#434A54'],
                ],
                'battery_capacity'=> 3274,
                'release_year'    => 2023,
            ],
            [
                'brand_id'        => $appleBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'iPhone 14',
                'processor'       => 'Apple A15 Bionic',
                'available_color' => [
                    ['name' => 'Midnight', 'value' => '#191970'],
                    ['name' => 'Starlight', 'value' => '#F0F8FF'],
                    ['name' => 'Red', 'value' => '#FF0000'],
                    ['name' => 'Blue', 'value' => '#0000FF'],
                ],
                'battery_capacity'=> 3279,
                'release_year'    => 2022,
            ],
            [
                'brand_id'        => $samsungBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'Galaxy S24 Ultra',
                'processor'       => 'Snapdragon 8 Gen 3',
                'available_color' => [
                    ['name' => 'Titanium Black', 'value' => '#212121'],
                    ['name' => 'Titanium Gray', 'value' => '#8E8E8E'],
                    ['name' => 'Titanium Violet', 'value' => '#4D435E'],
                    ['name' => 'Titanium Yellow', 'value' => '#F6E6B4'],
                ],
                'battery_capacity'=> 5000,
                'release_year'    => 2024,
            ],
            [
                'brand_id'        => $samsungBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'Galaxy A54',
                'processor'       => 'Exynos 1380',
                'available_color' => [
                    ['name' => 'Awesome Black', 'value' => '#000000'],
                    ['name' => 'Awesome White', 'value' => '#FFFFFF'],
                ],
                'battery_capacity'=> 5000,
                'release_year'    => 2023,
            ],
            [
                'brand_id'        => $xiaomiBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'Xiaomi 14',
                'processor'       => 'Snapdragon 8 Gen 3',
                'available_color' => [
                    ['name' => 'Black', 'value' => '#000000'],
                    ['name' => 'White', 'value' => '#FFFFFF'],
                ],
                'battery_capacity'=> 4610,
                'release_year'    => 2023,
            ],
            [
                'brand_id'        => $xiaomiBrand->id,
                'category_id'     => $gaming->id,
                'model_name'      => 'Redmi Note 13 Pro',
                'processor'       => 'MediaTek Dimensity 7200 Ultra',
                'available_color' => [
                    ['name' => 'Midnight Black', 'value' => '#000000'],
                    ['name' => 'Aurora Purple', 'value' => '#800080'],
                ],
                'battery_capacity'=> 5100,
                'release_year'    => 2023,
            ],
            [
                'brand_id'        => $oppoBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'OPPO Reno 11',
                'processor'       => 'MediaTek Dimensity 8200',
                'available_color' => [
                    ['name' => 'Rock Gray', 'value' => '#808080'],
                    ['name' => 'Sky Blue', 'value' => '#0000FF'],
                ],
                'battery_capacity'=> 4800,
                'release_year'    => 2023,
            ],
            [
                'brand_id'        => $googleBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'Pixel 8 Pro',
                'processor'       => 'Google Tensor G3',
                'available_color' => [
                    ['name' => 'Bay', 'value' => '#A8C5E6'],
                    ['name' => 'Obsidian', 'value' => '#2D2926']
                ],
                'battery_capacity'=> 5050,
                'release_year'    => 2023,
            ],
            [
                'brand_id'        => $onePlusBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'OnePlus 12',
                'processor'       => 'Snapdragon 8 Gen 3',
                'available_color' => [
                    ['name' => 'Flowy Emerald', 'value' => '#508D69'],
                    ['name' => 'Silky Black', 'value' => '#212121']
                ],
                'battery_capacity'=> 5400,
                'release_year'    => 2023,
            ],
            [
                'brand_id'        => $huaweiBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'P60 Pro',
                'processor'       => 'Snapdragon 8+ Gen 1 4G',
                'available_color' => [
                    ['name' => 'Rococo Pearl', 'value' => '#F5F5F5'],
                    ['name' => 'Black', 'value' => '#000000']
                ],
                'battery_capacity'=> 4815,
                'release_year'    => 2023,
            ],
            [
                'brand_id'        => $vivoBrand->id,
                'category_id'     => $smartphone->id,
                'model_name'      => 'Vivo V29',
                'processor'       => 'Snapdragon 778G',
                'available_color' => [
                    ['name' => 'Spacious Black', 'value' => '#000000'],
                ],
                'battery_capacity'=> 4600,
                'release_year'    => 2023,
            ],
            [
                'brand_id'        => $realmeBrand->id,
                'category_id'     => $refurbished->id,
                'model_name'      => 'Realme GT 5',
                'processor'       => 'Snapdragon 8 Gen 2',
                'available_color' => [
                    ['name' => 'Black Rain', 'value' => '#000000'],
                ],
                'battery_capacity'=> 5240,
                'release_year'    => 2023,
            ],
            [
                'brand_id'        => $samsungBrand->id,
                'category_id'     => $refurbished->id,
                'model_name'      => 'Galaxy S23',
                'processor'       => 'Snapdragon 8 Gen 2',
                'available_color' => [
                    ['name' => 'Phantom Black', 'value' => '#000000'],
                    ['name' => 'Cream', 'value' => '#FFFDD0'],
                ],
                'battery_capacity'=> 3900,
                'release_year'    => 2023,
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
