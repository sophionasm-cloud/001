<?php

namespace App\Http\Controllers;

use App\Services\GeminiAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiSupportController extends Controller
{
    protected GeminiAiService $aiService;

    public function __construct(GeminiAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Handle chat messages from the floating AI support widget.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
            'history.*.role' => 'nullable|string|in:user,assistant,model',
            'history.*.text' => 'nullable|string|max:2000',
            'history.*.content' => 'nullable|string|max:2000',
        ]);

        $message = trim($validated['message']);
        $history = $validated['history'] ?? [];

        $result = $this->aiService->chat($message, $history);

        return response()->json([
            'status'      => 'success',
            'reply'       => $result['reply'],
            'products'    => $result['products'] ?? [],
            'suggestions' => $result['suggestions'] ?? [],
        ]);
    }

    /**
     * Get initial state and personalized greeting for the floating widget.
     */
    public function init(): JsonResponse
    {
        $user = Auth::user();
        $greetingName = $user ? $user->name : null;
        $roleName = $user ? ($user->role?->name ?? 'Customer') : 'Guest';

        if ($user) {
            if ($roleName === 'Vendor') {
                $greeting = "👋 Hello **{$greetingName}**! Welcome to your Vendor Support Assistant. Need help managing your products, checking vendor orders, or understanding payouts?";
            } elseif ($roleName === 'Super Admin') {
                $greeting = "👋 Welcome Admin **{$greetingName}**! I'm here to assist with store reports, vendor approvals, and platform metrics.";
            } else {
                $greeting = "👋 Hi **{$greetingName}**! Welcome to MultiVendor Store. I'm Nova, your AI assistant. Need help finding products, tracking an order, or managing addresses?";
            }
        } else {
            $greeting = "👋 Welcome to **MultiVendor Store**! I'm **Nova**, your 24/7 AI shopping assistant. Ask me to find products, track orders, explain store policies, or help you become a vendor!";
        }

        $dummyChat = $this->aiService->chat('hello', []);

        return response()->json([
            'status'       => 'success',
            'user'         => [
                'is_logged_in' => Auth::check(),
                'name'         => $greetingName,
                'role'         => $roleName,
            ],
            'greeting'     => $greeting,
            'suggestions'  => $dummyChat['suggestions'] ?? [
                "🛍️ Explore Products",
                "📦 Track An Order",
                "🏪 Become a Vendor",
                "🚚 Shipping & Returns",
            ],
        ]);
    }
}
