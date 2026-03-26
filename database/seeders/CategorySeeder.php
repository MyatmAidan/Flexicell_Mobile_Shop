<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['category_name' => 'Smartphone',  'color' => '#3B82F6'],
            ['category_name' => 'Tablet',       'color' => '#8B5CF6'],
            ['category_name' => 'Feature Phone', 'color' => '#10B981'],
            ['category_name' => 'Accessories',  'color' => '#F59E0B'],
            ['category_name' => 'Refurbished',  'color' => '#EF4444'],
            ['category_name' => 'Gaming Phone',  'color' => '#EC4899'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['category_name' => $cat['category_name']],
                $cat
            );
        }
    }
}
