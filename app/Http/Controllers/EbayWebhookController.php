<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Inventory;

class EbayWebhookController extends Controller
{
    public function challenge(Request $request)
    {
        $challengeCode = $request->query('challenge_code');

        if (empty($challengeCode)) {
            return response()->json([
                'error' => 'challenge_code is required',
                'message' => 'Please provide a challenge_code'
            ], 422);
        }

        $verificationToken = env('YOUR_VERIFICATION_TOKEN');

        $endpoint = 'https://denverdiscountcomputers.com/api/ebay/account-deletion';

        $challengeResponse = hash(
            'sha256',
            $challengeCode . $verificationToken . $endpoint
        );

        return response()->json([
            'challengeResponse' => $challengeResponse
        ]);
    }

    public function notification(Request $request)
    {
        Log::info('eBay Account Deletion Notification', $request->all());

        return response()->json([
            'status' => 'OK'
        ]);
    }

    /**
     * Handle incoming real-time eBay Inventory & Quantity Webhook Notifications
     */
    public function handleInventoryNotification(Request $request)
    {
        $payload = $request->all();
        Log::info('eBay Real-Time Inventory Notification Received', $payload);

        $itemId = $payload['itemId'] ?? $payload['legacyItemId'] ?? $payload['SKU'] ?? ($payload['Item'] ?? null);
        $quantity = isset($payload['quantity']) ? (int) $payload['quantity'] : (isset($payload['availableQuantity']) ? (int) $payload['availableQuantity'] : null);

        if ($itemId && $quantity !== null) {
            $existing = Inventory::where('barcode', $itemId)->first();
            if ($existing) {
                $existing->update([
                    'quantity' => max(0, $quantity),
                    'status' => $quantity > 0 ? 'In Stock' : 'Out of Stock',
                ]);
                Log::info("Updated item #{$itemId} quantity to {$quantity} via eBay Webhook");
            }
        }

        return response()->json(['status' => 'SUCCESS']);
    }
}
