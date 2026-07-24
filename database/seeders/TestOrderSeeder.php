<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\Order;

class TestOrderSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::create([
            'name' => 'Mystic Scents Pakistan',
            'owner_phone' => '923000000000',
        ]);

        Order::create([
            'store_id' => $store->id,
            'order_reference' => '#PAK-9912',
            'customer_name' => 'Shahrukh Khan',
            'customer_phone' => '923001234567', // <-- APNA TESTING WHATSAPP NUMBER YAHAN DALEIN
            'total_amount' => 4500.00,
            'items_summary' => '1x Velvet Rose Perfume 50ml',
            'shipping_address' => 'House 42, Block 6, PECHS',
            'city' => 'Karachi',
            'status' => 'pending'
        ]);
    }
}