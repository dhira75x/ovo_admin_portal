@extends('layouts.admin')

@section('header', 'Order #' . ($order['_id'] ?? $order['id']))

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
            @if(count($order['items'] ?? []) > 0)
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
                            @foreach($order['items'] ?? [] as $item)
                                @php
                                    $unitPrice = $item['price'] ?? 0;
                                    $qty = $item['count'] ?? 0;
                                    $subtotal = $unitPrice * $qty;
                                    $currency = $order['currency'] ?? 'NGN';
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-white">
                                        <div class="font-medium">{{ $item['title'] ?? 'Product' }}</div>
                                        @if($item['description'] ?? null)
                                            <div class="text-xs text-gray-500 mt-1">{{ Str::limit($item['description'], 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">{{ $qty }}</td>
                                    <td class="px-4 py-3 text-right">{{ $currency }} {{ number_format($unitPrice, 2) }}</td>
                                    <td class="px-4 py-3 text-right text-white font-medium">{{ $currency }} {{ number_format($subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-800/30">
                            @php
                                $subtotal = $order['subtotal'] ?? $order['total'] ?? 0;
                                $taxAmount = isset($order['subtotal']) ? ($order['tax'] ?? 0) : (($order['total'] ?? 0) * ($order['tax'] ?? 0));
                                $shippingFee = $order['shippingFee'] ?? 0;
                                $finalTotal = $order['totalAmount'] ?? ($subtotal + $taxAmount + $shippingFee);
                                $currency = $order['currency'] ?? 'NGN';
                            @endphp
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-gray-400">Subtotal:</td>
                                <td class="px-4 py-3 text-right text-white">{{ $currency }} {{ number_format($subtotal, 2) }}</td>
                            </tr>
                            @if($taxAmount > 0)
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-gray-400">Tax:</td>
                                <td class="px-4 py-3 text-right text-white">{{ $currency }} {{ number_format($taxAmount, 2) }}</td>
                            </tr>
                            @endif
                            @if($shippingFee > 0)
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-gray-400">Shipping Fee:</td>
                                <td class="px-4 py-3 text-right text-white">{{ $currency }} {{ number_format($shippingFee, 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right text-gray-300 font-medium">Total:</td>
                                <td class="px-4 py-3 text-right text-white font-bold">{{ $currency }} {{ number_format($finalTotal, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-gray-500 italic">No items in this order.</p>
            @endif
        </div>

        <!-- Shipping Information -->
        @php
            $shippingAddress = $order['shippingAddress'] ?? $order['shipping_address'] ?? null;
            if (is_array($shippingAddress)) {
                $shippingAddress = implode(', ', array_filter([
                    $shippingAddress['address'] ?? $shippingAddress['street'] ?? null,
                    $shippingAddress['city'] ?? null,
                    $shippingAddress['state'] ?? null,
                    $shippingAddress['zipCode'] ?? $shippingAddress['zip'] ?? null,
                    $shippingAddress['country'] ?? null,
                ]));
            }
        @endphp
        @if($shippingAddress)
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Shipping Address</h3>
            <p class="text-gray-300 whitespace-pre-line">{{ $shippingAddress }}</p>
            
            @if($order['trackingNumber'] ?? $order['tracking_number'] ?? null)
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <span class="text-sm text-gray-400 block">Tracking Number</span>
                    <span class="text-white font-medium">{{ $order['trackingNumber'] ?? $order['tracking_number'] }}</span>
                </div>
            @endif
            
            @if($order['shippedAt'] ?? $order['shipped_at'] ?? null)
                <div class="mt-2">
                    <span class="text-sm text-gray-400 block">Shipped On</span>
                    <span class="text-white">{{ \Carbon\Carbon::parse($order['shippedAt'] ?? $order['shipped_at'])->format('M d, Y h:i A') }}</span>
                </div>
            @endif
        </div>
        @endif

        <!-- Billing Information -->
        @php
            $billingAddress = $order['billingAddress'] ?? $order['billing_address'] ?? null;
            if (is_array($billingAddress)) {
                $billingAddress = implode(', ', array_filter([
                    $billingAddress['address'] ?? $billingAddress['street'] ?? null,
                    $billingAddress['city'] ?? null,
                    $billingAddress['state'] ?? null,
                    $billingAddress['zipCode'] ?? $billingAddress['zip'] ?? null,
                    $billingAddress['country'] ?? null,
                ]));
            }
        @endphp
        @if($billingAddress)
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Billing Address</h3>
            <p class="text-gray-300 whitespace-pre-line">{{ $billingAddress }}</p>
        </div>
        @endif
    </div>

    <!-- Sidebar: Order Info -->
    <div class="space-y-6">
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Order Summary</h3>
            
            <div class="space-y-4">
                <div>
                    <span class="text-sm text-gray-400 block">Order Number</span>
                    <span class="text-white font-medium">#{{ $order['orderNumber'] ?? $order['_id'] ?? $order['id'] }}</span>
                </div>

                @if($order['tx_ref'] ?? null)
                <div>
                    <span class="text-sm text-gray-400 block">Transaction Reference</span>
                    <span class="text-xs text-gray-500 break-all">{{ $order['tx_ref'] }}</span>
                </div>
                @endif
                
                <div>
                    <span class="text-sm text-gray-400 block">Status</span>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-500/10 text-yellow-400',
                            'processing' => 'bg-blue-500/10 text-blue-400',
                            'shipped' => 'bg-indigo-500/10 text-indigo-400',
                            'completed' => 'bg-green-500/10 text-green-400',
                            'refunded' => 'bg-gray-500/10 text-gray-400',
                            'cancelled' => 'bg-red-500/10 text-red-400',
                        ];
                        $status = $order['status'] ?? 'pending';
                        $statusLabel = ucfirst($status);
                        $statusClass = $statusColors[$status] ?? 'bg-gray-700 text-gray-300';
                    @endphp
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }} inline-block mt-1">
                        {{ $statusLabel }}
                    </span>
                </div>
                
                <div>
                    <span class="text-sm text-gray-400 block">Payment Method</span>
                    <span class="text-white">{{ $order['paymentMethod'] ?? $order['payment_method'] ?? 'N/A' }}</span>
                </div>
                
                <div>
                    <span class="text-sm text-gray-400 block">Total Amount</span>
                    @php
                        $subtotal = $order['subtotal'] ?? $order['total'] ?? 0;
                        $taxAmount = isset($order['subtotal']) ? ($order['tax'] ?? 0) : (($order['total'] ?? 0) * ($order['tax'] ?? 0));
                        $shippingFee = $order['shippingFee'] ?? 0;
                        $finalTotal = $order['totalAmount'] ?? ($subtotal + $taxAmount + $shippingFee);
                    @endphp
                    <span class="text-white font-bold text-lg">{{ $order['currency'] ?? 'NGN' }} {{ number_format($finalTotal, 2) }}</span>
                </div>
                
                <hr class="border-gray-700 my-4">

                <div>
                    <span class="text-sm text-gray-400 block">Order Date</span>
                    <span class="text-white">{{ \Carbon\Carbon::parse($order['createdAt'] ?? now())->format('M d, Y h:i A') }}</span>
                </div>
                
                <div>
                    <span class="text-sm text-gray-400 block">Last Updated</span>
                    <span class="text-white">{{ \Carbon\Carbon::parse($order['updatedAt'] ?? now())->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Customer</h3>
            
            <div class="flex items-center">
                @php
                    $creator = data_get($order, 'createdBy');
                    $creatorName = data_get($creator, 'firstname')
                        ? (data_get($creator, 'firstname') . ' ' . data_get($creator, 'lastname'))
                        : (data_get($order, 'user.name') ?? 'Unknown User');
                    $creatorInitial = substr($creatorName, 0, 1);
                @endphp
                <div class="h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center text-sm font-bold text-white mr-3">
                    {{ $creatorInitial }}
                </div>
                <div>
                    <span class="text-white block font-medium">{{ $creatorName }}</span>
                    <span class="text-sm text-gray-500">{{ data_get($creator, 'email') ?? data_get($order, 'user.email') ?? '' }}</span>
                    @if(data_get($creator, 'phone') ?? null)
                        <div class="text-xs text-gray-500 mt-0.5">{{ data_get($creator, 'phone') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
