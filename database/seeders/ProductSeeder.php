<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $vendorUser = User::where('email', 'vendor@example.com')->first();
        if (! $vendorUser) {
            $this->command?->warn('vendor@example.com not found - run UserSeeder first.');
            return;
        }

        // Demo store, already approved so its products can be sold
        $vendor = Vendor::firstOrCreate(
            ['user_id' => $vendorUser->id],
            ['store_name' => "Jane's Demo Store", 'description' => 'Demo store with sample products']
        );
        $vendor->update(['approval_status' => 'active']);

        $categories = Category::pluck('id', 'slug');
        if ($categories->isEmpty()) {
            $this->command?->warn('No categories found - run CategorySeeder first.');
            return;
        }

        Storage::disk('public')->makeDirectory('products');

        // [name, category slug, cost, selling price, stock]
        $items = [
            ['Wireless Headphones',    'electronics',  35, 59.99, 40],
            ['Smart Watch',            'electronics',  60, 99.00, 25],
            ['Bluetooth Speaker',      'electronics',  20, 39.50, 60],
            ['Laptop Backpack',        'fashion',      15, 34.99, 80],
            ['Running Shoes',          'fashion',      30, 69.00, 35],
            ['Classic Sunglasses',     'fashion',       8, 19.99, 100],
            ['Cotton Hoodie',          'fashion',      18, 44.00, 50],
            ['Bestseller Novel',       'books',         4, 12.50, 120],
            ['Cooking Cookbook',       'books',         6, 18.00, 70],
            ['Desk Lamp',              'home-garden',  10, 24.99, 45],
            ['Ceramic Plant Pot',      'home-garden',   5, 14.50, 90],
            ['Throw Pillow Set',       'home-garden',  12, 29.00, 55],
        ];

        foreach ($items as $i => [$name, $slug, $cost, $price, $stock]) {
            $n = $i + 1;
            $source = resource_path("images/ecommerce-images/product-{$n}.png");
            $imagePath = null;

            if (File::exists($source)) {
                $imagePath = "products/product-{$n}.png";
                Storage::disk('public')->put($imagePath, File::get($source));
            }

            Product::updateOrCreate(
                ['vendor_id' => $vendor->id, 'name' => $name],
                [
                    'category_id'   => $categories[$slug] ?? $categories->first(),
                    'description'   => "{$name} - quality item from our demo store.",
                    'cost_price'    => $cost,
                    'selling_price' => $price,
                    'stock'         => $stock,
                    'is_active'     => true,
                    'image'         => $imagePath,
                ]
            );
        }
    }
}
