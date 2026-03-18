@extends('layouts.admin')

@section('header', 'Order #' . $order->id)

@section('header-actions')
    <a href="{{ route('admin.orders.index') }}" class="text-gray-400 hover:text-white text-sm">
        &larr; Back to Orders
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content: Order Items -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Order Items -->
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Order Items</h3>
            @if($order->items->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-400">
                        <thead class="bg-gray-800/50 text-gray-200 uppercase font-medium">
                            <tr>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3 text-right">Qty</th>
                                <th class="px-4 py-3 text-right">Unit Price</th>
                                <th class="px-4 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-4 py-3 text-white">{{ $item->product_name }}</td>
                                    <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-4 py-3 text-right text-white font-medium">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-800/30">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-gray-300 font-medium">Total:</td>
                                <td class="px-4 py-3 text-right text-white font-bold">${{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-gray-500 italic">No items in this order.</p>
            @endif
        </div>

        <!-- Shipping Information -->
        @if($order->shipping_address)
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Shipping Address</h3>
            <p class="text-gray-300 whitespace-pre-line">{{ $order->shipping_address }}</p>
            
            @if($order->tracking_number)
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <span class="text-sm text-gray-400 block">Tracking Number</span>
                    <span class="text-white font-medium">{{ $order->tracking_number }}</span>
                </div>
            @endif
            
            @if($order->shipped_at)
                <div class="mt-2">
                    <span class="text-sm text-gray-400 block">Shipped On</span>
                    <span class="text-white">{{ $order->shipped_at->format('M d, Y h:i A') }}</span>
                </div>
            @endif
        </div>
        @endif

        <!-- Billing Information -->
        @if($order->billing_address)
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Billing Address</h3>
            <p class="text-gray-300 whitespace-pre-line">{{ $order->billing_address }}</p>
        </div>
        @endif
    </div>

    <!-- Sidebar: Order Info -->
    <div class="space-y-6">
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Order Summary</h3>
            
            <div class="space-y-4">
                <div>
                    <span class="text-sm text-gray-400 block">Order ID</span>
                    <span class="text-white font-medium">#{{ $order->id }}</span>
                </div>
                
                <div>
                    <span class="text-sm text-gray-400 block">Status</span>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-500/10 text-yellow-400',
                            'processing' => 'bg-blue-500/10 text-blue-400',
                            'completed' => 'bg-green-500/10 text-green-400',
                            'cancelled' => 'bg-red-500/10 text-red-400',
                        ];
                        $statusLabel = ucfirst($order->status);
                        $statusClass = $statusColors[$order->status] ?? 'bg-gray-700 text-gray-300';
                    @endphp
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }} inline-block mt-1">
                        {{ $statusLabel }}
                    </span>
                </div>
                
                <div>
                    <span class="text-sm text-gray-400 block">Payment Method</span>
                    <span class="text-white">{{ $order->payment_method ?? 'N/A' }}</span>
                </div>
                
                <div>
                    <span class="text-sm text-gray-400 block">Total Amount</span>
                    <span class="text-white font-bold text-lg">${{ number_format($order->total_amount, 2) }}</span>
                </div>
                
                <hr class="border-gray-700 my-4">

                <div>
                    <span class="text-sm text-gray-400 block">Order Date</span>
                    <span class="text-white">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                </div>
                
                <div>
                    <span class="text-sm text-gray-400 block">Last Updated</span>
                    <span class="text-white">{{ $order->updated_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Customer</h3>
            
            <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center text-sm font-bold text-white mr-3">
                    {{ substr($order->user->name ?? 'U', 0, 1) }}
                </div>
                <div>
                    <span class="text-white block font-medium">{{ $order->user->name ?? 'Unknown' }}</span>
                    <span class="text-sm text-gray-500">{{ $order->user->email ?? '' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
