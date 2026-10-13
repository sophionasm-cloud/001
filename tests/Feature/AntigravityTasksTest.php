<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AntigravityTasksTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $vendorUser;
    private User $customer;
    private Role $adminRole;
    private Role $vendorRole;
    private Role $customerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole    = Role::firstOrCreate(['name' => 'Super Admin'], ['description' => 'Platform administrator']);
        $this->vendorRole   = Role::firstOrCreate(['name' => 'Vendor'], ['description' => 'Store vendor']);
        $this->customerRole = Role::firstOrCreate(['name' => 'Customer'], ['description' => 'Store customer']);

        $this->admin = User::firstOrCreate(['email' => 'admin@ecommerce.local'], [
            'name'     => 'Alice Admin',
            'password' => bcrypt('password'),
            'role_id'  => $this->adminRole->id,
        ]);

        $this->vendorUser = User::firstOrCreate(['email' => 'vendor@example.com'], [
            'name'     => 'Bob Vendor',
            'password' => bcrypt('password'),
            'role_id'  => $this->vendorRole->id,
        ]);

        Vendor::firstOrCreate(['user_id' => $this->vendorUser->id], [
            'store_name'      => 'Bob Store',
            'approval_status' => 'active',
        ]);

        $this->customer = User::firstOrCreate(['email' => 'customer@example.com'], [
            'name'     => 'Charlie Customer',
            'password' => bcrypt('password'),
            'role_id'  => $this->customerRole->id,
        ]);
    }

    /**
     * TASK 1: Role Display on navbar
     */
    public function test_task1_role_display_for_all_roles(): void
    {
        // 1. Guest visitor on product page sees "Guest"
        $guestResponse = $this->get(route('products.index'));
        $guestResponse->assertStatus(200);
        $guestResponse->assertSee('Guest');

        // 2. Admin sees "Alice Admin" and "Admin"
        $adminResponse = $this->actingAs($this->admin)->get(route('products.index'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Alice Admin');
        $adminResponse->assertSee('Admin');

        // 3. Vendor sees "Bob Vendor" and "Vendor"
        $vendorResponse = $this->actingAs($this->vendorUser)->get(route('products.index'));
        $vendorResponse->assertStatus(200);
        $vendorResponse->assertSee('Bob Vendor');
        $vendorResponse->assertSee('Vendor');

        // 4. Customer sees "Charlie Customer" and "User"
        $customerResponse = $this->actingAs($this->customer)->get(route('products.index'));
        $customerResponse->assertStatus(200);
        $customerResponse->assertSee('Charlie Customer');
        $customerResponse->assertSee('User');
    }

    /**
     * TASK 2: Sidebar Navigation & Route Definitions for All Roles
     */
    public function test_task2_navigation_paths_and_authorization(): void
    {
        // Guest navigation
        $this->get('/')->assertStatus(200);
        $this->get('/products')->assertStatus(200);
        $this->get('/cart')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
        $this->get('/vendor/register')->assertStatus(200);

        // Admin navigation
        $this->actingAs($this->admin)->get('/admin/dashboard')->assertStatus(200);
        $this->actingAs($this->admin)->get('/admin/vendors')->assertStatus(200);
        $this->actingAs($this->admin)->get('/admin/vendors/pending')->assertStatus(200);
        $this->actingAs($this->admin)->get('/admin/users')->assertStatus(200);
        $this->actingAs($this->admin)->get('/admin/reports')->assertStatus(200);

        // Vendor navigation
        $this->actingAs($this->vendorUser)->get('/vendor/dashboard')->assertStatus(200);
        $this->actingAs($this->vendorUser)->get('/vendor/products')->assertStatus(200);
        $this->actingAs($this->vendorUser)->get('/vendor/products/create')->assertStatus(200);
        $this->actingAs($this->vendorUser)->get('/vendor/orders')->assertStatus(200);

        // Customer navigation
        $this->actingAs($this->customer)->get('/customer/dashboard')->assertStatus(200);
        $this->actingAs($this->customer)->get('/customer/orders')->assertStatus(200);
        $this->actingAs($this->customer)->get('/customer/addresses')->assertStatus(200);

        // Authorization checks
        // Customer cannot access admin routes (403)
        $this->actingAs($this->customer)->get('/admin/dashboard')->assertStatus(403);
        // Customer cannot access vendor routes (403)
        $this->actingAs($this->customer)->get('/vendor/dashboard')->assertStatus(403);
        // Unauthenticated visitor is redirected to login for protected pages
        \Illuminate\Support\Facades\Auth::logout();
        $this->flushSession();
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
        $this->get('/customer/dashboard')->assertRedirect(route('login'));
    }

    /**
     * TASK 3: Login Page Image Visibility & Assets
     */
    public function test_task3_login_page_renders_with_valid_image(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Customer Login');
        $response->assertSee('auth-v2-login-illustration-light.png');
    }

    /**
     * TASK 4: Admin Reports Graphs & Data
     */
    public function test_task4_admin_reports_with_graphs(): void
    {
        // Seed a sample order with items for the admin report
        $category = Category::firstOrCreate(['name' => 'Electronics'], ['slug' => 'electronics']);
        $product = Product::firstOrCreate(['name' => 'Test Phone'], [
            'vendor_id'     => Vendor::first()->id,
            'category_id'   => $category->id,
            'description'   => 'A test smartphone description',
            'selling_price' => 299.99,
            'cost_price'    => 200.00,
            'stock'         => 10,
            'is_active'     => true,
        ]);

        $order = Order::create([
            'user_id'          => $this->customer->id,
            'subtotal'         => 299.99,
            'shipping_cost'    => 10.00,
            'tax'              => 0.00,
            'total'            => 309.99,
            'status'           => 'delivered',
            'shipping_address' => '123 Main St, New York, NY 10001',
        ]);

        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'vendor_id'  => $product->vendor_id,
            'quantity'   => 1,
            'price'      => 299.99,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Reports & Analytics');
        $response->assertSee('id="revenue-chart"', false);
        $response->assertSee('id="category-chart"', false);
        $response->assertSee('ApexCharts');
        $response->assertSee('Total Revenue');
    }

    /**
     * TASK 5: Customer Saved Addresses Feature (CRUD & Authorization)
     */
    public function test_task5_saved_addresses_crud_and_security(): void
    {
        // 1. Customer can view saved addresses page
        $response = $this->actingAs($this->customer)->get(route('customer.addresses.index'));
        $response->assertStatus(200);
        $response->assertSee('Saved Addresses');

        // 2. Customer can add an address
        $storeResponse = $this->actingAs($this->customer)->post(route('customer.addresses.store'), [
            'type'          => 'Home',
            'first_name'    => 'Charlie',
            'last_name'     => 'Customer',
            'address_line1' => '742 Evergreen Terrace',
            'city'          => 'Springfield',
            'state'         => 'OR',
            'zip_code'      => '97477',
            'country'       => 'United States',
            'phone'         => '555-1234',
            'is_default'    => '1',
        ]);

        $storeResponse->assertRedirect(route('customer.addresses.index'));
        $this->assertDatabaseHas('addresses', [
            'user_id'       => $this->customer->id,
            'address_line1' => '742 Evergreen Terrace',
            'city'          => 'Springfield',
            'is_default'    => 1,
        ]);

        $address = Address::where('user_id', $this->customer->id)->first();
        $this->assertNotNull($address);

        // 3. Customer can update their address
        $updateResponse = $this->actingAs($this->customer)->put(route('customer.addresses.update', $address->id), [
            'type'          => 'Office',
            'first_name'    => 'Charlie',
            'last_name'     => 'Customer',
            'address_line1' => '100 Industrial Way',
            'city'          => 'Springfield',
            'state'         => 'OR',
            'zip_code'      => '97477',
            'country'       => 'United States',
            'phone'         => '555-5678',
            'is_default'    => '1',
        ]);

        $updateResponse->assertRedirect(route('customer.addresses.index'));
        $this->assertDatabaseHas('addresses', [
            'id'            => $address->id,
            'type'          => 'Office',
            'address_line1' => '100 Industrial Way',
        ]);

        // 4. Security: Another user cannot update or delete Charlie's address
        $unauthorizedPut = $this->actingAs($this->vendorUser)->put(route('customer.addresses.update', $address->id), [
            'type'          => 'Office',
            'first_name'    => 'Hacker',
            'last_name'     => 'Bob',
            'address_line1' => 'Hacked Street',
            'city'          => 'Hacked',
            'zip_code'      => '00000',
            'country'       => 'United States',
        ]);
        $unauthorizedPut->assertStatus(403);

        $unauthorizedDelete = $this->actingAs($this->vendorUser)->delete(route('customer.addresses.destroy', $address->id));
        $unauthorizedDelete->assertStatus(403);

        // 5. Customer can delete their own address
        $deleteResponse = $this->actingAs($this->customer)->delete(route('customer.addresses.destroy', $address->id));
        $deleteResponse->assertRedirect(route('customer.addresses.index'));
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);

        // 6. Shortcut redirect /addresses works
        $redirectResponse = $this->actingAs($this->customer)->get('/addresses');
        $redirectResponse->assertRedirect('/customer/addresses');
    }
}
