<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::create(['name' => 'Super Admin', 'description' => 'Platform administrator']);
        Role::create(['name' => 'Vendor', 'description' => 'Store vendor']);
        Role::create(['name' => 'Customer', 'description' => 'Platform customer']);
    }
}