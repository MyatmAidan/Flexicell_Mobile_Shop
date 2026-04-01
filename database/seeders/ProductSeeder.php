<?php

namespace Database\Seeders;

use App\Models\Phone_model;
use App\Models\Product;
use Illuminate\Database\Seeder;
use RuntimeException;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $modelsByName = Phone_model::all()->keyBy('model_name');

        $products = [
            [
                'model_name'    => 'iPhone 17 Pro',
                'product_type'  => 'new',
                'description'   => 'Brand new iPhone 17 Pro with A19 Pro chip and premium design.',
                'image'         => null,
            ],
            [
                'model_name'    => 'iPhone 17',
                'product_type'  => 'new',
                'description'   => 'Brand new iPhone 17 with Apple A19 chip.',
                'image'         => null,
            ],
            [
                'model_name'    => 'Galaxy S26 Ultra',
                'product_type'  => 'new',
                'description'   => 'Samsung Galaxy S26 Ultra flagship with premium camera system and S Pen.',
                'image'         => null,
            ],
            [
                'model_name'    => 'Galaxy A57',
                'product_type'  => 'new',
                'description'   => 'Samsung Galaxy A57 5G mid-range smartphone with long battery life.',
                'image'         => null,
            ],
            [
                'model_name'    => 'Xiaomi 17',
                'product_type'  => 'new',
                'description'   => 'Xiaomi 17 flagship smartphone with powerful performance and advanced cameras.',
                'image'         => null,
            ],
            [
                'model_name'    => 'Redmi Note 14 Pro',
                'product_type'  => 'new',
                'description'   => 'Redmi Note 14 Pro with high-resolution camera and fast charging.',
                'image'         => null,
            ],
            [
                'model_name'    => 'OPPO Reno15 5G',
                'product_type'  => 'new',
                'description'   => 'OPPO Reno15 5G with sleek design and AI-enhanced photography.',
                'image'         => null,
            ],
            [
                'model_name'    => 'Vivo V50',
                'product_type'  => 'new',
                'description'   => 'Vivo V50 with stylish design and portrait-focused camera features.',
                'image'         => null,
            ],
            [
                'model_name'    => 'Pixel 10 Pro',
                'product_type'  => 'new',
                'description'   => 'Google Pixel 10 Pro with advanced AI features and pro-level cameras.',
                'image'         => null,
            ],
            [
                'model_name'    => 'OnePlus 13',
                'product_type'  => 'new',
                'description'   => 'OnePlus 13 flagship with smooth performance and premium camera setup.',
                'image'         => null,
            ],
            [
                'model_name'    => 'HUAWEI Pura 80 Pro',
                'product_type'  => 'new',
                'description'   => 'HUAWEI Pura 80 Pro with premium imaging technology and elegant design.',
                'image'         => null,
            ],
            [
                'model_name'    => 'Galaxy S24',
                'product_type'  => 'second hand',
                'description'   => 'Certified refurbished Galaxy S24 in excellent condition.',
                'image'         => null,
            ],
            [
                'model_name'    => 'Realme GT 7',
                'product_type'  => 'second hand',
                'description'   => 'Realme GT 7 second-hand unit with tested battery and solid performance.',
                'image'         => null,
            ],
        ];

        foreach ($products as $row) {
            $phoneModel = $modelsByName->get($row['model_name']);
            if ($phoneModel === null) {
                throw new RuntimeException(
                    "ProductSeeder: phone model not found: \"{$row['model_name']}\". Run PhoneModelSeeder first."
                );
            }

            $payload = [
                'phone_model_id' => $phoneModel->id,
                'product_type'   => $row['product_type'],
                'description'    => $row['description'],
                'image'          => $row['image'],
            ];

            Product::updateOrCreate(
                [
                    'phone_model_id' => $phoneModel->id,
                    'product_type'   => $row['product_type'],
                ],
                $payload
            );
        }
    }
}
