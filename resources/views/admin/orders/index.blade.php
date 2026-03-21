@extends('layouts.admin')

@section('header', 'Orders')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-dark-lighter rounded-xl p-6 border border-gray-700 shadow-lg">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-400 mb-1">Status</label>
                <select name="status" id="status" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label for="search" class="block text-sm font-medium text-gray-400 mb-1">Search User</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name or email..." class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Filter Orders
                </button>
            </div>
            <div class="flex items-end">
                <a href="{{ route('admin.orders.index') }}" class="w-full bg-gray-700 hover:bg-gray-600 text-white text-center font-medium py-2 px-4 rounded-lg transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-400">
                <thead class="bg-gray-800/50 text-gray-200 uppercase font-medium">
                    <tr>
                        <th class="px-6 py-4">Order #</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Payment</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-4 text-white font-medium">#{{ $order['_id'] ?? $order['id'] }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white mr-3">
                                    {{ substr(data_get($order, 'user.name') ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-white">{{ data_get($order, 'user.name') ?? 'Unknown' }}</div>
                                    <div class="text-xs">{{ data_get($order, 'user.email') ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-white font-medium">${{ number_format($order['totalAmount'] ?? $order['total_amount'] ?? 0, 2) }}</td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-500/10 text-yellow-400',
                                    'processing' => 'bg-blue-500/10 text-blue-400',
                                    'completed' => 'bg-green-500/10 text-green-400',
                                    'cancelled' => 'bg-red-500/10 text-red-400',
                                ];
                                $status = $order['status'] ?? 'pending';
                                $statusLabel = ucfirst($status);
                                $statusClass = $statusColors[$status] ?? 'bg-gray-700 text-gray-300';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $order['paymentMethod'] ?? $order['payment_method'] ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($order['createdAt'] ?? now())->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.orders.show', $order['_id'] ?? $order['id']) }}" class="text-primary hover:text-primary-light font-medium transition-colors">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            No orders found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
