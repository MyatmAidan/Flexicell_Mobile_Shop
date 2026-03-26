<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorOptionSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['name' => 'Black', 'value' => '#000000'],
            ['name' => 'White', 'value' => '#FFFFFF'],
            ['name' => 'Silver', 'value' => '#C0C0C0'],
            ['name' => 'Space Gray', 'value' => '#808080'],
            ['name' => 'Gold', 'value' => '#FFD700'],
            ['name' => 'Rose Gold', 'value' => '#B76E79'],
            ['name' => 'Midnight', 'value' => '#191970'],
            ['name' => 'Starlight', 'value' => '#F0F8FF'],
            ['name' => 'Red', 'value' => '#FF0000'],
            ['name' => 'Blue', 'value' => '#0000FF'],
            ['name' => 'Green', 'value' => '#008000'],
            ['name' => 'Purple', 'value' => '#800080'],
            ['name' => 'Yellow', 'value' => '#FFFF00'],
            ['name' => 'Titanium Black', 'value' => '#212121'],
            ['name' => 'Titanium Gray', 'value' => '#8E8E8E'],
            ['name' => 'Titanium Violet', 'value' => '#4D435E'],
            ['name' => 'Titanium Yellow', 'value' => '#F6E6B4'],
            ['name' => 'Natural Titanium', 'value' => '#BEBEBE'],
            ['name' => 'Blue Titanium', 'value' => '#434A54'],
            ['name' => 'White Titanium', 'value' => '#F2F2F2'],
            ['name' => 'Black Titanium', 'value' => '#333333'],
        ];

        foreach ($colors as $color) {
            DB::table('color_options')->updateOrInsert(
                ['value' => $color['value']],
                [
                    'name' => $color['name'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
