<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PakFlow AI - Order Automation Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">PakFlow AI Order Hub</h1>
            <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">System Live</span>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <p class="text-sm text-gray-500 font-medium">Total Orders</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_orders'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 border-l-4 border-l-green-500">
                <p class="text-sm text-gray-500 font-medium">Confirmed Orders</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['confirmed'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 border-l-4 border-l-red-500">
                <p class="text-sm text-gray-500 font-medium">Cancelled Orders</p>
                <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['cancelled'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 border-l-4 border-l-yellow-500">
                <p class="text-sm text-gray-500 font-medium">Pending Confirmation</p>
                <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['pending'] }}</p>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 font-bold text-gray-700">Recent COD Orders</div>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-xs text-gray-500 uppercase border-b">
                        <th class="p-4">Order Ref</th>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Phone</th>
                        <th class="p-4">Amount</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($recentOrders as $order)
                    <tr>
                        <td class="p-4 font-semibold text-blue-600">{{ $order->order_reference }}</td>
                        <td class="p-4">{{ $order->customer_name }}</td>
                        <td class="p-4">{{ $order->customer_phone }}</td>
                        <td class="p-4 font-semibold">PKR {{ number_format($order->total_amount) }}</td>
                        <td class="p-4">
                            @if($order->status == 'confirmed')
                                <span class="bg-green-100 text-green-700 text-xs px-2.5 py-1 rounded font-semibold">Confirmed</span>
                            @elseif($order->status == 'cancelled')
                                <span class="bg-red-100 text-red-700 text-xs px-2.5 py-1 rounded font-semibold">Cancelled</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-700 text-xs px-2.5 py-1 rounded font-semibold">Pending</span>
                            @endif
                        </td>
                        <td class="p-4 text-gray-500">{{ $order->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-500">No orders logged yet. Seed demo orders to test!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>