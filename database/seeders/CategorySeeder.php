<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::create(['name' => 'Electronics', 'slug' => 'electronics']);
        Category::create(['name' => 'Fashion', 'slug' => 'fashion']);
        Category::create(['name' => 'Books', 'slug' => 'books']);
        Category::create(['name' => 'Home & Garden', 'slug' => 'home-garden']);
    }
}