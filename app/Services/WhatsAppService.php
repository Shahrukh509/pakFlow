<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $token;
    protected string $phoneNumberId;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token', env('WHATSAPP_TOKEN'));
        $this->phoneNumberId = config('services.whatsapp.phone_number_id', env('WHATSAPP_PHONE_NUMBER_ID'));
    }

    /**
     * Outbound Text Message Send Karein
     */
    public function sendMessage(string $toPhoneNumber, string $messageText): bool
    {
        // WhatsApp API requires number format without '+' (e.g. 923001234567)
        $cleanPhone = preg_replace('/[^0-9]/', '', $toPhoneNumber);

        $url = "https://graph.facebook.com/v19.0/{$this->phoneNumberId}/messages";

        $response = Http::withToken($this->token)->post($url, [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $cleanPhone,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $messageText
            ]
        ]);

        if ($response->failed()) {
            Log::error('WhatsApp API Error: ' . $response->body());
            return false;
        }

        return true;
    }
}