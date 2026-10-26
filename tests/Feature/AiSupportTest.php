<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiSupportTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $vendorUser;
    private Role $customerRole;
    private Role $vendorRole;
    private Product $product;
    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerRole = Role::firstOrCreate(['name' => 'Customer'], ['description' => 'Store customer']);
        $this->vendorRole = Role::firstOrCreate(['name' => 'Vendor'], ['description' => 'Store vendor']);

        $this->customer = User::firstOrCreate(['email' => 'customer@example.com'], [
            'name'     => 'Charlie Customer',
            'password' => bcrypt('password'),
            'role_id'  => $this->customerRole->id,
        ]);

        $this->vendorUser = User::firstOrCreate(['email' => 'vendor@example.com'], [
            'name'     => 'Bob Vendor',
            'password' => bcrypt('password'),
            'role_id'  => $this->vendorRole->id,
        ]);

        $vendor = Vendor::firstOrCreate(['user_id' => $this->vendorUser->id], [
            'store_name'      => 'TechMart Store',
            'approval_status' => 'active',
        ]);

        $category = Category::firstOrCreate(['name' => 'Smartphones'], ['slug' => 'smartphones']);

        $this->product = Product::firstOrCreate(['name' => 'Ultra Phone Pro'], [
            'vendor_id'     => $vendor->id,
            'category_id'   => $category->id,
            'description'   => 'Flagship smartphone with high-end camera',
            'selling_price' => 799.99,
            'cost_price'    => 500.00,
            'stock'         => 15,
            'is_active'     => true,
        ]);

        $this->order = Order::create([
            'user_id'          => $this->customer->id,
            'subtotal'         => 799.99,
            'shipping_cost'    => 0.00,
            'tax'              => 0.00,
            'total'            => 799.99,
            'status'           => 'processing',
            'shipping_address' => '456 Tech Boulevard, Silicon Valley, CA 94025',
        ]);

        OrderItem::create([
            'order_id'   => $this->order->id,
            'product_id' => $this->product->id,
            'vendor_id'  => $vendor->id,
            'quantity'   => 1,
            'price'      => 799.99,
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Hello! As your MultiVendor AI assistant, you can register as a vendor or browse smartphones.']
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);
    }

    public function test_ai_chat_init_endpoint(): void
    {
        // 1. Guest visitor init
        $response = $this->getJson('/api/ai-chat/init');
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'user' => [
                'is_logged_in' => false,
                'role' => 'Guest',
            ],
        ]);
        $this->assertNotEmpty($response->json('greeting'));
        $this->assertIsArray($response->json('suggestions'));

        // 2. Authenticated customer init
        $authResponse = $this->actingAs($this->customer)->getJson('/api/ai-chat/init');
        $authResponse->assertStatus(200);
        $authResponse->assertJson([
            'status' => 'success',
            'user' => [
                'is_logged_in' => true,
                'name' => 'Charlie Customer',
                'role' => 'Customer',
            ],
        ]);
        $this->assertStringContainsString('Charlie Customer', $authResponse->json('greeting'));
    }

    public function test_ai_chat_validation(): void
    {
        $response = $this->postJson('/api/ai-chat', [
            'message' => '',
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
    }

    public function test_ai_chat_vendor_onboarding_inquiry(): void
    {
        $response = $this->postJson('/api/ai-chat', [
            'message' => 'How can I become a vendor on this platform?',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $reply = $response->json('reply');
        $this->assertNotEmpty($reply);
        $this->assertStringContainsString('vendor', strtolower($reply));
    }

    public function test_ai_chat_product_recommendation(): void
    {
        $response = $this->postJson('/api/ai-chat', [
            'message' => 'Recommend me a smartphone to buy',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertNotEmpty($response->json('reply'));
    }

    public function test_ai_chat_order_tracking(): void
    {
        $response = $this->actingAs($this->customer)->postJson('/api/ai-chat', [
            'message' => 'Where is my order #' . $this->order->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $reply = $response->json('reply');
        $this->assertStringContainsString((string)$this->order->id, $reply);
        $this->assertStringContainsString('processing', strtolower($reply));
    }

    public function test_ai_widget_rendered_in_storefront(): void
    {
        $response = $this->get('/products');
        $response->assertStatus(200);
        $response->assertSee('ai-support-container', false);
        $response->assertSee('Nova AI Support');
    }
}
