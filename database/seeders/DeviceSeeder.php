<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\Product;
use App\Models\Phone_model;
use App\Support\VariantStock;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        // iPhone 15 Pro product
        $ip15Pro = Product::whereHas('phoneModel', fn($q) => $q->where('model_name', 'iPhone 15 Pro'))->first();
        // Galaxy S24 Ultra product
        $s24Ultra = Product::whereHas('phoneModel', fn($q) => $q->where('model_name', 'Galaxy S24 Ultra'))->first();
        // Galaxy A54 product
        $a54 = Product::whereHas('phoneModel', fn($q) => $q->where('model_name', 'Galaxy A54'))->first();
        // Xiaomi 14 product
        $xi14 = Product::whereHas('phoneModel', fn($q) => $q->where('model_name', 'Xiaomi 14'))->first();
        // Redmi Note 13 Pro product
        $rn13 = Product::whereHas('phoneModel', fn($q) => $q->where('model_name', 'Redmi Note 13 Pro'))->first();

        // Vivo V29 product
        $v29 = Product::whereHas('phoneModel', fn($q) => $q->where('model_name', 'Vivo V29'))->first();
        // Pixel 8 Pro product
        $p8Pro = Product::whereHas('phoneModel', fn($q) => $q->where('model_name', 'Pixel 8 Pro'))->first();
        // OnePlus 12 product
        $op12 = Product::whereHas('phoneModel', fn($q) => $q->where('model_name', 'OnePlus 12'))->first();
        // Huawei P60 Pro product
        $p60Pro = Product::whereHas('phoneModel', fn($q) => $q->where('model_name', 'P60 Pro'))->first();

        $devices = [
            [
                'product_id'        => $ip15Pro->id,
                'imei'              => '352000001234561',
                'ram'               => '8GB',
                'storage'           => '256GB',
                'color'             => 'Black Titanium',
                'battery_percentage'=> 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 1350000,
                'selling_price'     => 1499000,
            ],
            [
                'product_id'        => $ip15Pro->id,
                'imei'              => '352000001234562',
                'ram'               => '8GB',
                'storage'           => '512GB',
                'color'             => 'White Titanium',
                'battery_percentage'=> 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 1500000,
                'selling_price'     => 1699000,
            ],
            [
                'product_id'        => $s24Ultra->id,
                'imei'              => '352000001234563',
                'ram'               => '12GB',
                'storage'           => '256GB',
                'color'             => 'Titanium Black',
                'battery_percentage'=> 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 1200000,
                'selling_price'     => 1399000,
            ],
            [
                'product_id'        => $s24Ultra->id,
                'imei'              => '352000001234564',
                'ram'               => '12GB',
                'storage'           => '512GB',
                'color'             => 'Titanium Gray',
                'battery_percentage'=> 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 1350000,
                'selling_price'     => 1599000,
            ],
            [
                'product_id'        => $a54->id,
                'imei'              => '352000001234565',
                'ram'               => '8GB',
                'storage'           => '128GB',
                'color'             => 'Awesome Black',
                'battery_percentage'=> 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 450000,
                'selling_price'     => 549000,
            ],
            [
                'product_id'        => $a54->id,
                'imei'              => '352000001234566',
                'ram'               => '8GB',
                'storage'           => '256GB',
                'color'             => 'Awesome Violet',
                'battery_percentage'=> 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 490000,
                'selling_price'     => 599000,
            ],
            [
                'product_id'        => $v29->id,
                'imei'              => '864209753102468',
                'ram'               => '8GB',
                'storage'           => '256GB',
                'color'             => 'Spacious Black',
                'battery_percentage'=> 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 350000,
                'selling_price'     => 429000,
            ],
            [
                'product_id'        => $p8Pro->id,
                'imei'              => '358642097531024',
                'ram'               => '12GB',
                'storage'           => '512GB',
                'color'             => 'Bay',
                'battery_percentage'=> 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 900000,
                'selling_price'     => 1099000,
            ],
            [
                'product_id'        => $op12->id,
                'imei'              => '469753102486123',
                'ram'               => '16GB',
                'storage'           => '512GB',
                'color'             => 'Flowy Emerald',
                'battery_percentage'=> 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 800000,
                'selling_price'     => 949000,
            ],
            [
                'product_id'        => $p60Pro->id,
                'imei'              => '578642091357924',
                'ram'               => '8GB',
                'storage'           => '256GB',
                'color'             => 'Rococo Pearl',
                'battery_percentage'=> 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 700000,
                'selling_price'     => 849000,
            ],
            [
                'product_id'        => $xi14->id,
                'imei'              => '352000001234567',
                'ram'               => '12GB',
                'storage'           => '256GB',
                'color'             => 'Black',
                'battery_percentage'=> 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 850000,
                'selling_price'     => 989000,
            ],
            [
                'product_id'        => $rn13->id,
                'imei'              => '352000001234568',
                'ram'               => '8GB',
                'storage'           => '256GB',
                'color'             => 'Midnight Black',
                'battery_percentage'=> 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 380000,
                'selling_price'     => 459000,
            ],
            [
                'product_id'        => $rn13->id,
                'imei'              => '352000001234569',
                'ram'               => '12GB',
                'storage'           => '512GB',
                'color'             => 'Aurora Purple',
                'battery_percentage'=> 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 430000,
                'selling_price'     => 519000,
            ],
        ];

        foreach ($devices as $d) {
            $ramId = VariantStock::upsertOption('ram_options', $d['ram']);
            $storageId = VariantStock::upsertOption('storage_options', $d['storage']);
            $colorId = VariantStock::upsertOption('color_options', $d['color']);

            Device::updateOrCreate(
                ['imei' => $d['imei']],
                [
                    'product_id'        => $d['product_id'],
                    'ram_option_id'     => $ramId,
                    'storage_option_id' => $storageId,
                    'color_option_id'   => $colorId,
                    'battery_percentage'=> $d['battery_percentage'],
                    'condition_grade'   => $d['condition_grade'],
                    'status'            => $d['status'],
                    'purchase_price'    => $d['purchase_price'],
                    'selling_price'     => $d['selling_price'],
                ]
            );
        }
    }
}
