<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Super Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@ecommerce.local',
            'password' => Hash::make('password'),
            'role_id' => 1,
        ]);

        // Vendor
        User::create([
            'name' => 'Jane Smith',
            'email' => 'vendor@example.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
        ]);

        // Customer
        User::create([
            'name' => 'John Doe',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role_id' => 3,
        ]);
    }
}