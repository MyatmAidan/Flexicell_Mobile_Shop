<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Product;
use App\Models\Warranty;
use App\Support\VariantStock;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $warranty12 = Warranty::where('warranty_month', 12)->first();
        $warranty3  = Warranty::where('warranty_month', 3)->first();

        $ip15Pro  = Product::whereHas('phoneModel', fn ($q) => $q->where('model_name', 'iPhone 15 Pro'))->first();
        $s24Ultra = Product::whereHas('phoneModel', fn ($q) => $q->where('model_name', 'Galaxy S24 Ultra'))->first();
        $a54      = Product::whereHas('phoneModel', fn ($q) => $q->where('model_name', 'Galaxy A54'))->first();
        $xi14     = Product::whereHas('phoneModel', fn ($q) => $q->where('model_name', 'Xiaomi 14'))->first();
        $rn13     = Product::whereHas('phoneModel', fn ($q) => $q->where('model_name', 'Redmi Note 13 Pro'))->first();
        $v29      = Product::whereHas('phoneModel', fn ($q) => $q->where('model_name', 'Vivo V29'))->first();
        $p8Pro    = Product::whereHas('phoneModel', fn ($q) => $q->where('model_name', 'Pixel 8 Pro'))->first();
        $op12     = Product::whereHas('phoneModel', fn ($q) => $q->where('model_name', 'OnePlus 12'))->first();
        $p60Pro   = Product::whereHas('phoneModel', fn ($q) => $q->where('model_name', 'P60 Pro'))->first();

        $devices = [
            [
                'product_id'        => $ip15Pro->id,
                'imei'              => '352000001234561',
                'ram'               => '8GB',
                'storage'           => '256GB',
                'color'             => 'Black Titanium',
                'battery_percentage' => 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 1350000,
                'selling_price'     => 1499000,
                'warranty_id'       => $warranty12?->id,
            ],
            [
                'product_id'        => $ip15Pro->id,
                'imei'              => '352000001234562',
                'ram'               => '8GB',
                'storage'           => '512GB',
                'color'             => 'White Titanium',
                'battery_percentage' => 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 1500000,
                'selling_price'     => 1699000,
                'warranty_id'       => $warranty12?->id,
            ],
            [
                'product_id'        => $s24Ultra->id,
                'imei'              => '352000001234563',
                'ram'               => '12GB',
                'storage'           => '256GB',
                'color'             => 'Titanium Black',
                'battery_percentage' => 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 1200000,
                'selling_price'     => 1399000,
                'warranty_id'       => $warranty12?->id,
            ],
            [
                'product_id'        => $s24Ultra->id,
                'imei'              => '352000001234564',
                'ram'               => '12GB',
                'storage'           => '512GB',
                'color'             => 'Titanium Gray',
                'battery_percentage' => 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 1350000,
                'selling_price'     => 1599000,
                'warranty_id'       => $warranty12?->id,
            ],
            [
                'product_id'        => $a54->id,
                'imei'              => '352000001234565',
                'ram'               => '8GB',
                'storage'           => '128GB',
                'color'             => 'Awesome Black',
                'battery_percentage' => 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 450000,
                'selling_price'     => 549000,
                'warranty_id'       => $warranty12?->id,
            ],
            [
                'product_id'        => $a54->id,
                'imei'              => '352000001234566',
                'ram'               => '8GB',
                'storage'           => '256GB',
                'color'             => 'Awesome Violet',
                'battery_percentage' => 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 490000,
                'selling_price'     => 599000,
                'warranty_id'       => $warranty12?->id,
            ],
            [
                'product_id'        => $v29->id,
                'imei'              => '864209753102468',
                'ram'               => '8GB',
                'storage'           => '256GB',
                'color'             => 'Spacious Black',
                'battery_percentage' => 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 350000,
                'selling_price'     => 429000,
                'warranty_id'       => $warranty12?->id,
            ],
            [
                'product_id'        => $p8Pro->id,
                'imei'              => '358642097531024',
                'ram'               => '12GB',
                'storage'           => '512GB',
                'color'             => 'Bay',
                'battery_percentage' => 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 900000,
                'selling_price'     => 1099000,
                'warranty_id'       => $warranty12?->id,
            ],
            [
                'product_id'        => $op12->id,
                'imei'              => '469753102486123',
                'ram'               => '16GB',
                'storage'           => '512GB',
                'color'             => 'Flowy Emerald',
                'battery_percentage' => 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 800000,
                'selling_price'     => 949000,
                'warranty_id'       => $warranty12?->id,
            ],
            [
                'product_id'        => $p60Pro->id,
                'imei'              => '578642091357924',
                'ram'               => '8GB',
                'storage'           => '256GB',
                'color'             => 'Rococo Pearl',
                'battery_percentage' => 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 700000,
                'selling_price'     => 849000,
                'warranty_id'       => $warranty12?->id,
            ],
            [
                'product_id'        => $xi14->id,
                'imei'              => '352000001234567',
                'ram'               => '12GB',
                'storage'           => '256GB',
                'color'             => 'Black',
                'battery_percentage' => 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 850000,
                'selling_price'     => 989000,
                'warranty_id'       => $warranty12?->id,
            ],
            [
                'product_id'        => $rn13->id,
                'imei'              => '352000001234568',
                'ram'               => '8GB',
                'storage'           => '256GB',
                'color'             => 'Midnight Black',
                'battery_percentage' => 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 380000,
                'selling_price'     => 459000,
                'warranty_id'       => $warranty3?->id,
            ],
            [
                'product_id'        => $rn13->id,
                'imei'              => '352000001234569',
                'ram'               => '12GB',
                'storage'           => '512GB',
                'color'             => 'Aurora Purple',
                'battery_percentage' => 100,
                'condition_grade'   => 'A',
                'status'            => 'available',
                'purchase_price'    => 430000,
                'selling_price'     => 519000,
                'warranty_id'       => $warranty3?->id,
            ],
        ];

        foreach ($devices as $d) {
            $ramId     = VariantStock::upsertOption('ram_options', $d['ram']);
            $storageId = VariantStock::upsertOption('storage_options', $d['storage']);
            $colorId   = VariantStock::upsertOption('color_options', $d['color']);

            $variantId = VariantStock::findOrCreateVariantId(
                (int) $d['product_id'],
                $ramId,
                $storageId,
                $colorId
            );

            Device::updateOrCreate(
                ['imei' => $d['imei']],
                [
                    'product_variant_id' => $variantId,
                    'warranty_id'        => $d['warranty_id'],
                    'battery_percentage' => $d['battery_percentage'],
                    'condition_grade'    => $d['condition_grade'],
                    'status'             => $d['status'],
                    'purchase_price'     => $d['purchase_price'],
                    'selling_price'      => $d['selling_price'],
                ]
            );
        }

        foreach (Product::pluck('id') as $pid) {
            VariantStock::syncProductVariantStock((int) $pid);
        }
    }
}
