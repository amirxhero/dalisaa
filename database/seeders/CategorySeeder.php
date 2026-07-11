<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'موبایل', 'slug' => 'mobile', 'icon' => 'mobile', 'sort_order' => 1],
            ['name' => 'تبلت', 'slug' => 'tablet', 'icon' => 'tablet', 'sort_order' => 2],
            ['name' => 'ساعت', 'slug' => 'watch', 'icon' => 'watch', 'sort_order' => 3],
            ['name' => 'هدفون', 'slug' => 'headphone', 'icon' => 'headphone', 'sort_order' => 4],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
