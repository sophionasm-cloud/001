<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Vendor;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_access_dashboard(): void
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Platform administrator']);
        $admin = User::firstOrCreate(['email' => 'admin@ecommerce.local'], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@ecommerce.local',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $dashResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashResponse->assertStatus(200);
    }

    public function test_vendor_can_login_and_access_dashboard(): void
    {
        $role = Role::firstOrCreate(['name' => 'Vendor'], ['description' => 'Store vendor']);
        $vendorUser = User::firstOrCreate(['email' => 'vendor@example.com'], [
            'name' => 'Vendor User',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);
        Vendor::create([
            'user_id' => $vendorUser->id,
            'store_name' => 'Test Vendor Store',
            'approval_status' => 'active',
        ]);

        $response = $this->post('/login', [
            'email' => 'vendor@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('vendor.dashboard'));
        $this->assertAuthenticatedAs($vendorUser);

        $dashResponse = $this->actingAs($vendorUser)->get(route('vendor.dashboard'));
        $dashResponse->assertStatus(200);
    }
}
