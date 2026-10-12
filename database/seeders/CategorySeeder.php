<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::firstOrCreate(['slug' => 'electronics'], ['name' => 'Electronics']);
        Category::firstOrCreate(['slug' => 'fashion'], ['name' => 'Fashion']);
        Category::firstOrCreate(['slug' => 'books'], ['name' => 'Books']);
        Category::firstOrCreate(['slug' => 'home-garden'], ['name' => 'Home & Garden']);
    }
}