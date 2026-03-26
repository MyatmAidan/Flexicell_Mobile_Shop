<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InstallmentRate;

class InstallmentRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['month_option' => 3,  'rate' => 0.00],
            ['month_option' => 6,  'rate' => 2.50],
            ['month_option' => 12, 'rate' => 5.00],
            ['month_option' => 24, 'rate' => 8.00],
        ];

        foreach ($rates as $rate) {
            InstallmentRate::updateOrCreate(
                ['month_option' => $rate['month_option']],
                $rate
            );
        }
    }
}
