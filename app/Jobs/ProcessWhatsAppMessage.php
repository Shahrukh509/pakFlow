<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\PakFlowAIService;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $incomingMessage
    ) {}

    public function handle(PakFlowAIService $aiService, WhatsAppService $whatsAppService): void
    {
        // 1. Pass message to OpenAI Engine
        $aiReply = $aiService->handleCustomerMessage($this->order, $this->incomingMessage);

        // 2. Reply back to customer via WhatsApp
        $whatsAppService->sendMessage($this->order->customer_phone, $aiReply);
    }
}