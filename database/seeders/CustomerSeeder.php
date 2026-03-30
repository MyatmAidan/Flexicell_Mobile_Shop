<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name'    => 'Aung Kyaw Thu',
                'phone'   => '09-420111001',
                'email'   => 'aungkyawthu@gmail.com',
                'address' => 'No.5, Shwe Pyi Thar Township, Yangon',
                'points'  => 0,
            ],
            [
                'name'    => 'Mya Thin Zar',
                'phone'   => '09-420111002',
                'email'   => 'myathinzar@gmail.com',
                'address' => 'No.12, Tharkayta Township, Yangon',
                'points'  => 50,
            ],
            [
                'name'    => 'Zin Thida Oo',
                'phone'   => '09-420111003',
                'email'   => 'zinthidaoo@gmail.com',
                'address' => 'Mandalay, 78th Street',
                'points'  => 120,
            ],
            [
                'name'    => 'Kyaw Zin Oo',
                'phone'   => '09-420111004',
                'email'   => 'kyawzinoo@gmail.com',
                'address' => 'No.45, Hlaing Township, Yangon',
                'points'  => 0,
            ],
            [
                'name'    => 'Su Myat Noe',
                'phone'   => '09-420111005',
                'email'   => 'sumyatnoe@gmail.com',
                'address' => 'Sagaing, Chan Aye Thar Zan Township',
                'points'  => 30,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['phone' => $customer['phone']],
                $customer
            );
        }
    }
}
