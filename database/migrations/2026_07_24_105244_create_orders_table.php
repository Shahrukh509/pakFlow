<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
           $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('order_reference'); // e.g. #ORD-1082
            $table->string('customer_name');
            $table->string('customer_phone'); // Format: 923001234567
            $table->decimal('total_amount', 10, 2);
            $table->text('items_summary'); // e.g. "1x Black Perfume 50ml"
            $table->text('shipping_address');
            $table->string('city');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'escalated'])->default('pending');
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
