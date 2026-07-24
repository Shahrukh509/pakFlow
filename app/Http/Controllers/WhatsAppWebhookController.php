<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Jobs\ProcessWhatsAppMessage;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Webhook Verification (Required by Meta Webhook Setup)
     */
    public function verify(Request $request)
    {
        $verifyToken = env('WHATSAPP_VERIFY_TOKEN', 'pakflow_secure_webhook_token_123');

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    /**
     * Incoming WhatsApp Messages Webhook
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        // Check if payload contains messages
        if (isset($payload['entry'][0]['changes'][0]['value']['messages'][0])) {
            $messageData = $payload['entry'][0]['changes'][0]['value']['messages'][0];
            $fromPhone = $messageData['from']; // Customer phone number
            $text = $messageData['text']['body'] ?? '';

            if (!empty($text)) {
                // Find latest pending or active order for this phone number
                $order = Order::where('customer_phone', $fromPhone)
                    ->latest()
                    ->first();

                if ($order) {
                    // Dispatch background queue job
                    ProcessWhatsAppMessage::dispatch($order, $text);
                }
            }
        }

        // Meta requires instant HTTP 200 response
        return response()->json(['status' => 'SUCCESS'], 200);
    }
}