<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Platform administrator']);
        $vendorRole = Role::firstOrCreate(['name' => 'Vendor'], ['description' => 'Store vendor']);
        $customerRole = Role::firstOrCreate(['name' => 'Customer'], ['description' => 'Platform customer']);

        // Super Admin
        User::firstOrCreate(
            ['email' => 'admin@ecommerce.local'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

        // Vendor
        $vendorUser = User::firstOrCreate(
            ['email' => 'vendor@example.com'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('password'),
                'role_id' => $vendorRole->id,
            ]
        );

        // Ensure Vendor record exists and is active
        Vendor::firstOrCreate(
            ['user_id' => $vendorUser->id],
            [
                'store_name' => "Jane's Boutique",
                'description' => 'Quality products from our premier vendor store.',
                'approval_status' => 'active',
            ]
        );

        // Customer
        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'role_id' => $customerRole->id,
            ]
        );
    }
}