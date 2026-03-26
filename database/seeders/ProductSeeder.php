<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Phone_model;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $models = Phone_model::all()->keyBy('model_name');

        $products = [
            [
                'phone_model_id'  => $models['iPhone 15 Pro']->id,
                'product_type'    => 'new',
                'warranty_month'  => 12,
                'stock_quantity'  => 10,
                'description'     => 'Brand new iPhone 15 Pro with A17 Pro chip and titanium design.',
                'image'           => null,
            ],
            [
                'phone_model_id'  => $models['iPhone 14']->id,
                'product_type'    => 'new',
                'warranty_month'  => 12,
                'stock_quantity'  => 15,
                'description'     => 'iPhone 14 with A15 Bionic chip.',
                'image'           => null,
            ],
            [
                'phone_model_id'  => $models['Galaxy S24 Ultra']->id,
                'product_type'    => 'new',
                'warranty_month'  => 12,
                'stock_quantity'  => 8,
                'description'     => 'Samsung Galaxy S24 Ultra with built-in S Pen.',
                'image'           => null,
            ],
            [
                'phone_model_id'  => $models['Galaxy A54']->id,
                'product_type'    => 'new',
                'warranty_month'  => 12,
                'stock_quantity'  => 20,
                'description'     => 'Samsung Galaxy A54 5G mid-range smartphone.',
                'image'           => null,
            ],
            [
                'phone_model_id'  => $models['Xiaomi 14']->id,
                'product_type'    => 'new',
                'warranty_month'  => 12,
                'stock_quantity'  => 12,
                'description'     => 'Xiaomi 14 flagship with Leica cameras.',
                'image'           => null,
            ],
            [
                'phone_model_id'  => $models['Redmi Note 13 Pro']->id,
                'product_type'    => 'new',
                'warranty_month'  => 12,
                'stock_quantity'  => 25,
                'description'     => 'Redmi Note 13 Pro+ 5G with 200MP camera.',
                'image'           => null,
            ],
            [
                'phone_model_id'  => $models['OPPO Reno 11']->id,
                'product_type'    => 'new',
                'warranty_month'  => 12,
                'stock_quantity'  => 10,
                'description'     => 'OPPO Reno 11 with AI portraits.',
                'image'           => null,
            ],
            [
                'phone_model_id'  => $models['Vivo V29']->id,
                'product_type'    => 'new',
                'warranty_month'  => 12,
                'stock_quantity'  => 10,
                'description'     => 'Vivo V29 with aura light portrait.',
                'image'           => null,
            ],
            [
                'phone_model_id'  => $models['Pixel 8 Pro']->id,
                'product_type'    => 'new',
                'warranty_month'  => 12,
                'stock_quantity'  => 5,
                'description'     => 'Google Pixel 8 Pro with advanced AI cameras.',
                'image'           => null,
            ],
            [
                'phone_model_id'  => $models['OnePlus 12']->id,
                'product_type'    => 'new',
                'warranty_month'  => 12,
                'stock_quantity'  => 8,
                'description'     => 'OnePlus 12 flagship with Hasselblad cameras.',
                'image'           => null,
            ],
            [
                'phone_model_id'  => $models['P60 Pro']->id,
                'product_type'    => 'new',
                'warranty_month'  => 12,
                'stock_quantity'  => 6,
                'description'     => 'Huawei P60 Pro with XMAGE imaging.',
                'image'           => null,
            ],
            [
                'phone_model_id'  => $models['Galaxy S23']->id,
                'product_type'    => 'second hand',
                'warranty_month'  => 3,
                'stock_quantity'  => 5,
                'description'     => 'Certified refurbished Galaxy S23 in excellent condition.',
                'image'           => null,
            ],
            [
                'phone_model_id'  => $models['Realme GT 5']->id,
                'product_type'    => 'second hand',
                'warranty_month'  => 3,
                'stock_quantity'  => 4,
                'description'     => 'Realme GT 5 refurbished unit with tested battery.',
                'image'           => null,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                [
                    'phone_model_id' => $product['phone_model_id'],
                    'product_type'   => $product['product_type']
                ],
                $product
            );
        }
    }
}
