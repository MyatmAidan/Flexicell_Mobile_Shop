<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Product;
use App\Models\Warranty;
use App\Support\VariantStock;
use Illuminate\Database\Seeder;
use RuntimeException;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $warranty12 = Warranty::where('warranty_month', 12)->first();
        $warranty3  = Warranty::where('warranty_month', 3)->first();

        $devices = [
            [
                'model_name'         => 'iPhone 17 Pro',
                'product_type'       => 'new',
                'imei'               => '352000001234561',
                'ram'                => '8GB',
                'storage'            => '256GB',
                'color'              => 'Black',
                'battery_percentage' => 100,
                'condition_grade'    => 'A',
                'status'             => 'available',
                'purchase_price'     => 6250000,
                'selling_price'      => 6799000,
                'warranty_id'        => $warranty12?->id,
            ],
            [
                'model_name'         => 'iPhone 17 Pro',
                'product_type'       => 'new',
                'imei'               => '352000001234562',
                'ram'                => '8GB',
                'storage'            => '512GB',
                'color'              => 'Silver',
                'battery_percentage' => 100,
                'condition_grade'    => 'A',
                'status'             => 'available',
                'purchase_price'     => 6600000,
                'selling_price'      => 7299000,
                'warranty_id'        => $warranty12?->id,
            ],
            [
                'model_name'         => 'Galaxy S26 Ultra',
                'product_type'       => 'new',
                'imei'               => '352000001234563',
                'ram'                => '12GB',
                'storage'            => '256GB',
                'color'              => 'Titanium Black',
                'battery_percentage' => 100,
                'condition_grade'    => 'A',
                'status'             => 'available',
                'purchase_price'     => 4300000,
                'selling_price'      => 4799000,
                'warranty_id'        => $warranty12?->id,
            ],
            [
                'model_name'         => 'Galaxy S26 Ultra',
                'product_type'       => 'new',
                'imei'               => '352000001234564',
                'ram'                => '12GB',
                'storage'            => '512GB',
                'color'              => 'Titanium Gray',
                'battery_percentage' => 100,
                'condition_grade'    => 'A',
                'status'             => 'available',
                'purchase_price'     => 4650000,
                'selling_price'      => 5299000,
                'warranty_id'        => $warranty12?->id,
            ],
            [
                'model_name'         => 'Galaxy A57',
                'product_type'       => 'new',
                'imei'               => '352000001234565',
                'ram'                => '8GB',
                'storage'            => '128GB',
                'color'              => 'Awesome Black',
                'battery_percentage' => 100,
                'condition_grade'    => 'A',
                'status'             => 'available',
                'purchase_price'     => 1500000,
                'selling_price'      => 1619000,
                'warranty_id'        => $warranty12?->id,
            ],
            [
                'model_name'         => 'Galaxy A57',
                'product_type'       => 'new',
                'imei'               => '352000001234566',
                'ram'                => '8GB',
                'storage'            => '256GB',
                'color'              => 'Awesome Blue',
                'battery_percentage' => 100,
                'condition_grade'    => 'A',
                'status'             => 'available',
                'purchase_price'     => 1550000,
                'selling_price'      => 1679000,
                'warranty_id'        => $warranty12?->id,
            ],
            [
                'model_name'         => 'Vivo V50',
                'product_type'       => 'new',
                'imei'               => '864209753102468',
                'ram'                => '8GB',
                'storage'            => '256GB',
                'color'              => 'Spacious Black',
                'battery_percentage' => 100,
                'condition_grade'    => 'A',
                'status'             => 'available',
                'purchase_price'     => 1720000,
                'selling_price'      => 1919000,
                'warranty_id'        => $warranty12?->id,
            ],
            [
                'model_name'         => 'Pixel 10 Pro',
                'product_type'       => 'new',
                'imei'               => '358642097531024',
                'ram'                => '12GB',
                'storage'            => '512GB',
                'color'              => 'Bay',
                'battery_percentage' => 100,
                'condition_grade'    => 'A',
                'status'             => 'available',
                'purchase_price'     => 3980000,
                'selling_price'      => 4799000,
                'warranty_id'        => $warranty12?->id,
            ],
            [
                'model_name'         => 'OnePlus 13',
                'product_type'       => 'new',
                'imei'               => '469753102486123',
                'ram'                => '16GB',
                'storage'            => '512GB',
                'color'              => 'Black Eclipse',
                'battery_percentage' => 100,
                'condition_grade'    => 'A',
                'status'             => 'available',
                'purchase_price'     => 2900000,
                'selling_price'      => 3249000,
                'warranty_id'        => $warranty12?->id,
            ],
            [
                'model_name'         => 'HUAWEI Pura 80 Pro',
                'product_type'       => 'new',
                'imei'               => '578642091357924',
                'ram'                => '12GB',
                'storage'            => '256GB',
                'color'              => 'White',
                'battery_percentage' => 100,
                'condition_grade'    => 'A',
                'status'             => 'available',
                'purchase_price'     => 2820000,
                'selling_price'      => 3469000,
                'warranty_id'        => $warranty12?->id,
            ],
            [
                'model_name'         => 'Xiaomi 17',
                'product_type'       => 'new',
                'imei'               => '352000001234567',
                'ram'                => '12GB',
                'storage'            => '256GB',
                'color'              => 'Black',
                'battery_percentage' => 100,
                'condition_grade'    => 'A',
                'status'             => 'available',
                'purchase_price'     => 2930000,
                'selling_price'      => 3189000,
                'warranty_id'        => $warranty12?->id,
            ],
            [
                'model_name'         => 'Redmi Note 14 Pro',
                'product_type'       => 'new',
                'imei'               => '352000001234568',
                'ram'                => '8GB',
                'storage'            => '256GB',
                'color'              => 'Midnight Black',
                'battery_percentage' => 100,
                'condition_grade'    => 'A',
                'status'             => 'available',
                'purchase_price'     => 1020000,
                'selling_price'      => 1299000,
                'warranty_id'        => $warranty3?->id,
            ],
            [
                'model_name'         => 'Redmi Note 14 Pro',
                'product_type'       => 'new',
                'imei'               => '352000001234569',
                'ram'                => '12GB',
                'storage'            => '512GB',
                'color'              => 'Aurora Purple',
                'battery_percentage' => 100,
                'condition_grade'    => 'A',
                'status'             => 'available',
                'purchase_price'     => 1270000,
                'selling_price'      => 1569000,
                'warranty_id'        => $warranty3?->id,
            ],
        ];

        foreach ($devices as $d) {
            $productId = $this->resolveProductId($d['model_name'], $d['product_type']);

            $ramId     = VariantStock::upsertOption('ram_options', $d['ram']);
            $storageId = VariantStock::upsertOption('storage_options', $d['storage']);
            $colorId   = VariantStock::upsertOption('color_options', $d['color']);

            $variantId = VariantStock::findOrCreateVariantId(
                $productId,
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

    private function resolveProductId(string $modelName, string $productType): int
    {
        $product = Product::query()
            ->where('product_type', $productType)
            ->whereHas('phoneModel', fn($q) => $q->where('model_name', $modelName))
            ->first();

        if ($product === null) {
            throw new RuntimeException(
                "DeviceSeeder: no product for model \"{$modelName}\" (type \"{$productType}\"). Run PhoneModelSeeder and ProductSeeder first."
            );
        }

        return (int) $product->id;
    }
}
