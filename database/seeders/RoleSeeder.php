<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Platform administrator']);
        Role::firstOrCreate(['name' => 'Vendor'], ['description' => 'Store vendor']);
        Role::firstOrCreate(['name' => 'Customer'], ['description' => 'Platform customer']);
    }
}