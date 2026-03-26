<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name'        => 'Aung Kyaw Thu',
                'phone'       => '09-420111001',
                'nrc'         => '12/OUKANA(N)123456',
                'address'     => 'No.5, Shwe Pyi Thar Township, Yangon',
                'attachments' => null,
            ],
            [
                'name'        => 'Mya Thin Zar',
                'phone'       => '09-420111002',
                'nrc'         => '9/PAKANA(N)654321',
                'address'     => 'No.12, Tharkayta Township, Yangon',
                'attachments' => null,
            ],
            [
                'name'        => 'Zin Thida Oo',
                'phone'       => '09-420111003',
                'nrc'         => '7/PULANA(N)789012',
                'address'     => 'Mandalay, 78th Street',
                'attachments' => null,
            ],
            [
                'name'        => 'Kyaw Zin Oo',
                'phone'       => '09-420111004',
                'nrc'         => '12/MAHANA(N)345678',
                'address'     => 'No.45, Hlaing Township, Yangon',
                'attachments' => null,
            ],
            [
                'name'        => 'Su Myat Noe',
                'phone'       => '09-420111005',
                'nrc'         => '8/TAMANA(N)901234',
                'address'     => 'Sagaing, Chan Aye Thar Zan Township',
                'attachments' => null,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['nrc' => $customer['nrc']],
                $customer
            );
        }
    }
}
