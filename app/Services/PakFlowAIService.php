<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use App\Models\Order;
use App\Models\Conversation;

class PakFlowAIService
{
    /**
     * Process incoming message from customer and return AI response
     */
    public function handleCustomerMessage(Order $order, string $incomingMessage): string
    {
        // 1. Save user message to database
        Conversation::create([
            'order_id' => $order->id,
            'customer_phone' => $order->customer_phone,
            'sender' => 'user',
            'message' => $incomingMessage
        ]);

        // 2. Load recent conversation thread (Last 10 messages for context)
        $history = Conversation::where('order_id', $order->id)
            ->latest()
            ->take(10)
            ->get()
            ->reverse();

        $formattedMessages = [];

        // System Prompt tuning for Pakistani Market
        $systemPrompt = "You are 'PakFlow AI', a polite, helpful e-commerce order confirmation and support representative for a business in Pakistan.
- Speak in natural Roman Urdumixed with light English (e.g., 'Aapka order confirm ho chuka hai', 'Delivery me 2-3 working days lagenge').
- Keep responses short, concise, and professional (suitable for WhatsApp).
- Current Order Details:
  * Order Reference: {$order->order_reference}
  * Customer Name: {$order->customer_name}
  * Total Amount: PKR {$order->total_amount} (Cash on Delivery)
  * Shipping Address: {$order->shipping_address}, {$order->city}
  * Order Items: {$order->items_summary}

Goal:
1. Ask the customer to confirm their address and order placement.
2. If the user explicitly confirms (e.g., 'haan ok hai', 'confirm kar do', 'yes correct'), mark your intent internally or respond positively.
3. If the user wants to cancel (e.g., 'galti se hua', 'cancel kar do'), accept politely and ask for a quick reason.
4. If they ask about delivery time, state 'Standard delivery time is 2 to 4 working days across Pakistan via courier.'";

        $formattedMessages[] = ['role' => 'system', 'content' => $systemPrompt];

        foreach ($history as $chat) {
            $formattedMessages[] = [
                'role' => $chat->sender === 'user' ? 'user' : 'assistant',
                'content' => $chat->message
            ];
        }

        // 3. Call OpenAI API (gpt-4o-mini is optimized for low latency and high quality)
        $response = OpenAI::chat()->create([
            'model' => config('app.openai_model', 'gpt-4o-mini'),
            'messages' => $formattedMessages,
            'temperature' => 0.4,
            'max_tokens' => 200,
        ]);

        $aiReply = $response->choices[0]->message->content;

        // 4. Save AI response to DB
        Conversation::create([
            'order_id' => $order->id,
            'customer_phone' => $order->customer_phone,
            'sender' => 'assistant',
            'message' => $aiReply
        ]);

        // 5. Update Order status based on basic detection (Can be enhanced with Function Calling)
        $this->updateOrderStatusIfNeeded($order, $incomingMessage);

        return $aiReply;
    }

    private function updateOrderStatusIfNeeded(Order $order, string $message): void
    {
        $lowercased = strtolower($message);

        $confirmKeywords = ['confirm', 'ok hai', 'yes', 'sahi hai', 'bhej do', 'yup', 'haan'];
        $cancelKeywords = ['cancel', 'nahi chahiye', 'cazel', 'no', 'galti se'];

        foreach ($confirmKeywords as $keyword) {
            if (str_contains($lowercased, $keyword) && $order->status === 'pending') {
                $order->update(['status' => 'confirmed']);
                return;
            }
        }

        foreach ($cancelKeywords as $keyword) {
            if (str_contains($lowercased, $keyword) && $order->status === 'pending') {
                $order->update(['status' => 'cancelled', 'cancellation_reason' => $message]);
                return;
            }
        }
    }
}