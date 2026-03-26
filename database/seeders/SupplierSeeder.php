<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'company_name' => 'Tech Import Co., Ltd.',
                'address'      => 'No. 45, Merchant Street, Yangon',
                'phone'        => '09-420000001',
                'debt_total'   => 0,
            ],
            [
                'company_name' => 'Mobile Galaxy Wholesale',
                'address'      => 'Room 302, Golden City Tower, Mandalay',
                'phone'        => '09-420000002',
                'debt_total'   => 500000,
            ],
            [
                'company_name' => 'Sino Phone Distributors',
                'address'      => 'No. 12, China Street, Muse',
                'phone'        => '09-420000003',
                'debt_total'   => 1200000,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['company_name' => $supplier['company_name']],
                $supplier
            );
        }
    }
}
