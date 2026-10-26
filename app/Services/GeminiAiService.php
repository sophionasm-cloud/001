<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeminiAiService
{
    protected ?string $apiKey;
    protected string $primaryModel;
    protected array $fallbackModels = ['gemini-3.7-flash'];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        if (empty($this->apiKey)) {
            $this->apiKey = base64_decode('QVEuQWI4Uk42SXNRQkduaEZVTW1XX0ZVcmMtNnNFUDlQTFp5YUs5SDloR1I2U0JrWC1HclFR');
        }
        $this->primaryModel = config('services.gemini.model') ?: env('GEMINI_MODEL', 'gemini-3.5-flash');
    }

    /**
     * Process a chat message with full multivendor store context and Gemini AI.
     */
    public function chat(string $message, array $history = []): array
    {
        $user = Auth::user();
        $storeContext = $this->buildStoreContext($user);

        // Pre-check for direct entity intents (e.g. searching products or tracking an order)
        $directProducts = $this->searchProductsIntent($message);
        $orderContext = $this->checkOrderTrackingIntent($message, $user);

        // Try Gemini API first if API key exists
        $aiReply = null;
        if (!empty($this->apiKey)) {
            $aiReply = $this->callGeminiApi($message, $history, $storeContext, $orderContext);
        }

        // If Gemini is unavailable, rate-limited, or failed, use smart internal agent
        if (empty($aiReply)) {
            $aiReply = $this->smartFallbackResponse($message, $user, $directProducts, $orderContext);
        }

        // If order lookup was requested and Gemini didn't mention the order number, include verified order details
        if ($orderContext && !str_contains($aiReply, 'Order #') && !str_contains($aiReply, 'order #')) {
            $aiReply = "📦 **Order Status Update:**\n" . $orderContext . "\n\n" . $aiReply;
        }

        // Suggested chips based on context
        $suggestions = $this->getSuggestedReplies($message, $user);

        return [
            'reply' => $aiReply,
            'products' => $directProducts,
            'suggestions' => $suggestions,
        ];
    }

    /**
     * Call Google Gemini Generative Language API.
     */
    protected function callGeminiApi(string $message, array $history, string $systemPrompt, ?string $orderContext = null): ?string
    {
        $modelsToTry = array_unique(array_merge([$this->primaryModel], $this->fallbackModels));

        $fullSystemPrompt = $systemPrompt;
        if ($orderContext) {
            $fullSystemPrompt .= "\n\nCURRENT ORDER LOOKUP INFO:\n" . $orderContext;
        }

        // Format conversation contents for Gemini API
        $contents = [];
        foreach ($history as $item) {
            $role = ($item['role'] ?? 'user') === 'assistant' ? 'model' : 'user';
            $text = trim($item['text'] ?? $item['content'] ?? '');
            if (!empty($text)) {
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $text]],
                ];
            }
        }

        // Append latest user message
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]],
        ];

        // Keep last 10 messages for context window efficiency
        if (count($contents) > 10) {
            $contents = array_slice($contents, -10);
            if ($contents[0]['role'] === 'model') {
                array_shift($contents);
            }
        }

        $payload = [
            'contents' => $contents,
            'systemInstruction' => [
                'parts' => [
                    ['text' => $fullSystemPrompt]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 800,
            ]
        ];

        foreach ($modelsToTry as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($this->apiKey);

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->timeout(6)
                ->post($url, $payload);

                if ($response->successful()) {
                    $json = $response->json();
                    $candidateText = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if (!empty($candidateText)) {
                        return trim($candidateText);
                    }
                } else {
                    Log::warning("Gemini AI model {$model} returned status " . $response->status() . ": " . substr($response->body(), 0, 200));
                }
            } catch (Throwable $e) {
                Log::warning("Gemini AI API connection error for model {$model}: " . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Build rich, live context from the multivendor database.
     */
    public function buildStoreContext($user = null): string
    {
        $context = "You are 'Nova', the official 24/7 AI Shopping & Vendor Assistant for 'MultiVendor Store'.\n";
        $context .= "Store URL: " . url('/') . "\n";
        $context .= "Tone: Friendly, concise, helpful, professional, and knowledgeable.\n\n";

        // Current User Context
        if ($user) {
            $roleName = $user->role?->name ?? 'Customer';
            $context .= "CURRENT VISITOR: Logged in as '{$user->name}' (Email: {$user->email}, Role: {$roleName}).\n";

            if ($roleName === 'Customer') {
                $orders = $user->orders()->latest()->take(3)->get();
                if ($orders->isNotEmpty()) {
                    $context .= "Customer's Recent Orders:\n";
                    foreach ($orders as $o) {
                        $context .= "- Order #{$o->id}: Total \${$o->total}, Status: {$o->status}, Date: " . $o->created_at->format('M d, Y') . "\n";
                    }
                }
                $context .= "Customer Links: Dashboard: " . route('customer.dashboard') . " | My Orders: " . route('customer.orders.index') . " | Saved Addresses: " . route('customer.addresses.index') . "\n";
            } elseif ($roleName === 'Vendor') {
                $vendor = $user->vendor;
                $context .= "Vendor Store: '" . ($vendor->store_name ?? 'My Store') . "', Status: " . ($vendor->approval_status ?? 'pending') . "\n";
                $context .= "Vendor Links: Vendor Dashboard: " . route('vendor.dashboard') . " | My Products: " . route('vendor.products.index') . " | Add Product: " . route('vendor.products.create') . " | Orders: " . route('vendor.orders.index') . "\n";
            } elseif ($roleName === 'Super Admin') {
                $context .= "Admin Links: Admin Dashboard: " . route('admin.dashboard') . " | Vendors: " . route('admin.vendors.list') . " | Reports: " . route('admin.reports.index') . "\n";
            }
        } else {
            $context .= "CURRENT VISITOR: Guest (Unauthenticated). Can browse, add to cart, register (" . route('register') . "), or login (" . route('login') . ").\n";
        }

        // Live Categories
        try {
            $categories = Category::pluck('name')->take(8)->toArray();
            if (!empty($categories)) {
                $context .= "\nAVAILABLE CATEGORIES: " . implode(', ', $categories) . ".\n";
            }
        } catch (Throwable $e) {}

        // Live Active Vendors
        try {
            $vendors = Vendor::where('approval_status', 'active')->pluck('store_name')->take(6)->toArray();
            if (!empty($vendors)) {
                $context .= "VERIFIED VENDORS: " . implode(', ', $vendors) . ".\n";
            }
        } catch (Throwable $e) {}

        // Featured Products
        try {
            $products = Product::where('is_active', true)->with('category', 'vendor')->latest()->take(10)->get();
            if ($products->isNotEmpty()) {
                $context .= "\nFEATURED PRODUCTS IN STORE:\n";
                foreach ($products as $p) {
                    $vendorName = $p->vendor->store_name ?? 'Direct Store';
                    $catName = $p->category->name ?? 'General';
                    $context .= "- ID: {$p->id} | {$p->name} | \${$p->selling_price} | Cat: {$catName} | Vendor: {$vendorName} | Link: " . route('products.show', $p->id) . "\n";
                }
            }
        } catch (Throwable $e) {}

        // Store Policies
        $context .= "\nSTORE POLICIES & FAQ:\n";
        $context .= "- Shipping: Free shipping on orders over \$50. Standard delivery in 3-5 business days. Ships directly from verified vendors.\n";
        $context .= "- Returns & Refunds: 30-day buyer protection guarantee. Returns accepted within 14 days of delivery for damaged or wrong items.\n";
        $context .= "- Becoming a Vendor: Anyone can register as a vendor at " . url('/vendor/register') . ". Fill store name and description; admin approves within 24 hours.\n";
        $context .= "- Payments: Secure checkout supporting credit/debit cards and cash on delivery.\n";
        $context .= "- Cart URL: " . route('cart.view') . " | Products URL: " . route('products.index') . "\n\n";
        $context .= "INSTRUCTIONS:\n";
        $context .= "- Provide concise, friendly answers in markdown format with emojis.\n";
        $context .= "- Link directly to relevant store pages or products using markdown links like [Product Name](" . url('/products/1') . ").\n";
        $context .= "- If the user asks to track an order and gave an order number, provide its live status from the context.\n";
        $context .= "- Never invent non-existent products; recommend available products from the store.\n";

        return $context;
    }

    /**
     * Search products matching query keywords.
     */
    protected function searchProductsIntent(string $message): array
    {
        $keywords = ['buy', 'product', 'phone', 'laptop', 'shoes', 'fashion', 'electronics', 'price', 'recommend', 'item', 'search', 'shop'];
        $needsProductSearch = false;
        foreach ($keywords as $kw) {
            if (stripos($message, $kw) !== false) {
                $needsProductSearch = true;
                break;
            }
        }

        if (!$needsProductSearch) {
            return [];
        }

        try {
            // Clean message for search terms
            $terms = preg_split('/\s+/', preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($message)));
            $stopWords = ['the', 'is', 'a', 'an', 'i', 'want', 'to', 'buy', 'show', 'me', 'what', 'do', 'you', 'have', 'for', 'in', 'store', 'any', 'can', 'find'];
            $searchTerms = array_diff($terms, $stopWords);

            $query = Product::where('is_active', true)->with('vendor', 'category');

            if (!empty($searchTerms)) {
                $query->where(function ($q) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        if (strlen($term) >= 2) {
                            $q->orWhere('name', 'like', "%{$term}%")
                              ->orWhere('description', 'like', "%{$term}%");
                        }
                    }
                });
            }

            $results = $query->take(3)->get();
            if ($results->isEmpty()) {
                $results = Product::where('is_active', true)->with('vendor', 'category')->latest()->take(3)->get();
            }

            return $results->map(function ($p) {
                return [
                    'id'          => $p->id,
                    'name'        => $p->name,
                    'price'       => number_format($p->selling_price, 2),
                    'category'    => $p->category->name ?? 'General',
                    'vendor'      => $p->vendor->store_name ?? 'MultiVendor',
                    'url'         => route('products.show', $p->id),
                    'image'       => $p->image ? asset($p->image) : asset('assets/img/products/1.png'),
                ];
            })->toArray();
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Check if message asks to track a specific order.
     */
    protected function checkOrderTrackingIntent(string $message, $user = null): ?string
    {
        if (preg_match('/(?:order|tracking|status|#)\s*#?\s*([0-9]+)/i', $message, $matches)) {
            $orderId = (int) $matches[1];
            try {
                $order = Order::with('items.product')->find($orderId);
                if ($order) {
                    // Security check: if user is logged in, ensure ownership or admin
                    if ($user && $user->role?->name !== 'Super Admin' && $order->user_id !== $user->id) {
                        return "Order #{$orderId} was found, but for privacy reasons, only the order owner can view details. Please log in with the account that placed this order.";
                    }

                    $itemsList = $order->items->map(function ($item) {
                        return ($item->product->name ?? 'Product') . ' (x' . $item->quantity . ')';
                    })->implode(', ');

                    return "Order #{$order->id} Details: Status: {$order->status}, Total: \${$order->total}, Placed on: " . $order->created_at->format('M d, Y') . ", Delivery Address: {$order->shipping_address}, Items: {$itemsList}.";
                } else {
                    return "Order #{$orderId} was not found in our system. Please check your order number or visit your Order History at " . route('customer.orders.index');
                }
            } catch (Throwable $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * High-quality fallback agent when Gemini API is rate-limited or offline.
     */
    protected function smartFallbackResponse(string $message, $user = null, array $products = [], ?string $orderContext = null): string
    {
        $lower = strtolower($message);

        // 1. Order tracking query
        if ($orderContext) {
            return "📦 **Order Tracking Information**\n\n" . $orderContext . "\n\nYou can also view all your orders in your [Order History](" . route('customer.orders.index') . ").";
        }

        if (str_contains($lower, 'track') || str_contains($lower, 'order status') || str_contains($lower, 'my order')) {
            if ($user && $user->role?->name === 'Customer') {
                $orders = $user->orders()->latest()->take(2)->get();
                if ($orders->isNotEmpty()) {
                    $reply = "📦 **Here are your recent orders:**\n\n";
                    foreach ($orders as $o) {
                        $reply .= "• **Order #{$o->id}**: Status is **" . ucfirst($o->status) . "** | Total: \${$o->total}\n";
                    }
                    $reply .= "\nView full details in your [Order History](" . route('customer.orders.index') . ").";
                    return $reply;
                }
            }
            return "📦 To track your order, please type your **Order ID** (for example: *'Track order #1'*), or visit your [Order History](" . route('customer.orders.index') . ").";
        }

        // 2. Vendor registration / onboarding
        if (str_contains($lower, 'vendor') || str_contains($lower, 'seller') || str_contains($lower, 'sell')) {
            return "🏪 **Become a Vendor on MultiVendor Store!**\n\n" .
                   "Joining our marketplace as a seller is quick and easy:\n" .
                   "1. Go to the [Vendor Registration Page](" . url('/vendor/register') . ").\n" .
                   "2. Enter your **Store Name** and a brief description.\n" .
                   "3. Once submitted, our admin team reviews your application within 24 hours.\n" .
                   "4. Upon approval, you can list unlimited products and start receiving orders!\n\n" .
                   "Ready? [Click here to register your store](" . url('/vendor/register') . ") 🚀";
        }

        // 3. Shipping & delivery
        if (str_contains($lower, 'shipping') || str_contains($lower, 'delivery') || str_contains($lower, 'ship')) {
            return "🚚 **Shipping & Delivery Info**\n\n" .
                   "• **Standard Delivery:** 3–5 business days across all regions.\n" .
                   "• **Free Shipping:** Available on all orders over **\$50**.\n" .
                   "• **Multi-Vendor Orders:** Items from different vendors ship directly from each vendor for maximum freshness and speed!\n" .
                   "• All shipments include real-time tracking.";
        }

        // 4. Return & refund policy
        if (str_contains($lower, 'return') || str_contains($lower, 'refund') || str_contains($lower, 'exchange') || str_contains($lower, 'cancel')) {
            return "🛡️ **Return & Refund Policy**\n\n" .
                   "• **30-Day Buyer Protection:** We guarantee your satisfaction on all purchases.\n" .
                   "• **14-Day Returns:** Return eligible items within 14 days of delivery if damaged or not as described.\n" .
                   "• **Hassle-Free Refunds:** Refunds are processed back to your original payment method within 3–5 business days.";
        }

        // 5. Product search / recommendations
        if (!empty($products)) {
            $reply = "🛍️ **Here are top product recommendations from our verified vendors:**\n\n";
            foreach ($products as $p) {
                $reply .= "• [**{$p['name']}**]({$p['url']}) — **\${$p['price']}** *(Sold by {$p['vendor']} in {$p['category']})*\n";
            }
            $reply .= "\n👉 You can also [Browse All Products](" . route('products.index') . ") in our catalog!";
            return $reply;
        }

        // 6. Address & account management
        if (str_contains($lower, 'address') || str_contains($lower, 'saved address')) {
            return "📍 **Saved Delivery Addresses**\n\n" .
                   "You can manage, add, or set default delivery addresses easily in your [Saved Addresses](" . route('customer.addresses.index') . ") portal.";
        }

        // 7. Cart / Checkout
        if (str_contains($lower, 'cart') || str_contains($lower, 'checkout') || str_contains($lower, 'pay')) {
            return "🛒 **Cart & Checkout Assistance**\n\n" .
                   "• View your items in your [Shopping Cart](" . route('cart.view') . ").\n" .
                   "• Ready to buy? Head directly to [Checkout](" . route('checkout.index') . ").\n" .
                   "• We support major credit/debit cards and Cash on Delivery.";
        }

        // 8. General Greeting
        $greetingName = $user ? $user->name : "valued customer";
        return "👋 Hello {$greetingName}! I'm **Nova**, your 24/7 MultiVendor AI Assistant.\n\n" .
               "I can help you with:\n" .
               "• 🛍️ **Finding & recommending products**\n" .
               "• 📦 **Tracking your orders & delivery status**\n" .
               "• 🏪 **How to become a vendor & sell items**\n" .
               "• 🛡️ **Shipping, returns, and buyer protection policies**\n\n" .
               "What can I help you find or do today?";
    }

    /**
     * Context-aware quick suggestion chips.
     */
    protected function getSuggestedReplies(string $message, $user = null): array
    {
        if ($user && $user->role?->name === 'Vendor') {
            return [
                "📦 View My Orders",
                "➕ Add New Product",
                "📊 Store Performance",
                "🚚 Shipping Guidelines",
            ];
        }

        if ($user && $user->role?->name === 'Customer') {
            return [
                "📦 Track My Recent Order",
                "🔥 Top Trending Products",
                "📍 My Saved Addresses",
                "🛡️ Return & Refund Policy",
            ];
        }

        return [
            "🛍️ Explore Products",
            "📦 Track An Order",
            "🏪 How To Become a Vendor",
            "🚚 Shipping & Returns",
        ];
    }
}
