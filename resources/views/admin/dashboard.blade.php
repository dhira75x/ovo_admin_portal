@extends('layouts.admin')

@section('header', 'Dashboard Overview')

@section('content')
<div class="space-y-6">
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
        @php
            $payload = $analytics['payload'] ?? [];
            $stats = [
                ['label' => 'Total Orders', 'value' => number_format($payload['totalOrders']['value'] ?? 0), 'growth' => $payload['totalOrders']['growth'] ?? 0, 'icon' => 'shopping-cart', 'color' => 'blue'],
                ['label' => 'Total Revenue', 'value' => '₦' . number_format($payload['totalRevenue']['value'] ?? 0, 2), 'growth' => $payload['totalRevenue']['growth'] ?? 0, 'icon' => 'currency-dollar', 'color' => 'green'],
                ['label' => 'Active Merchants', 'value' => number_format($payload['activeMerchants']['value'] ?? 0), 'growth' => $payload['activeMerchants']['growth'] ?? 0, 'icon' => 'user-group', 'color' => 'purple'],
                ['label' => 'Open Tickets', 'value' => number_format($payload['openTickets']['value'] ?? 0), 'growth' => $payload['openTickets']['growth'] ?? 0, 'icon' => 'ticket', 'color' => 'red'],
                ['label' => 'Total Commission', 'value' => '₦' . number_format($payload['totalCommission']['value'] ?? 0, 2), 'growth' => 0, 'icon' => 'chart-bar', 'color' => 'indigo'],
            ];
            $revenueTrend = $payload['revenueTrend'] ?? [];
            $orderDistribution = $payload['orderDistribution'] ?? [];
            $maxRevenue = collect($revenueTrend)->max('revenue') ?: 1;
        @endphp

        @foreach($stats as $stat)
        <div class="bg-dark-lighter p-6 rounded-2xl border border-gray-800 shadow-lg group hover:border-primary/50 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-400 font-medium">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-bold text-gray-100 mt-1">{{ $stat['value'] }}</p>
                </div>
                <div class="p-3 bg-{{ $stat['color'] }}-500/10 rounded-xl text-{{ $stat['color'] }}-500 group-hover:scale-110 transition-transform">
                     <!-- Icon mapping based on $stat['icon'] could go here -->
                     <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        @if($stat['icon'] == 'shopping-cart')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        @elseif($stat['icon'] == 'currency-dollar')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        @elseif($stat['icon'] == 'user-group')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        @elseif($stat['icon'] == 'chart-bar')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        @endif
                     </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs">
                @if(($stat['growth'] ?? 0) >= 0)
                    <span class="text-green-500 font-medium">+{{ $stat['growth'] ?? 0 }}%</span>
                @else
                    <span class="text-red-500 font-medium">{{ $stat['growth'] ?? 0 }}%</span>
                @endif
                <span class="text-gray-500 ml-2">from last month</span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts / Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-dark-lighter p-8 rounded-2xl border border-gray-800 shadow-lg">
            <h3 class="text-lg font-semibold text-gray-100 mb-6">Revenue Overview</h3>
            <div class="h-64 flex items-end justify-between space-x-2">
                @forelse ($revenueTrend as $item)
                    @php
                        $height = ($item['revenue'] / $maxRevenue) * 100;
                        $height = max(5, $height); // Minimum height for visibility
                    @endphp
                    <div class="w-full bg-primary/20 rounded-t-lg hover:bg-primary/40 transition-colors group relative" style="height: {{ $height }}%">
                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 border border-gray-700 shadow-xl">
                            ${{ number_format($item['revenue'], 0) }}
                        </div>
                    </div>
                @empty
                    @for ($i = 0; $i < 12; $i++)
                        <div class="w-full bg-gray-800/10 rounded-t-lg h-4"></div>
                    @endfor
                @endforelse
            </div>
            <div class="flex justify-between mt-4 text-xs text-gray-500 px-1 font-medium overflow-hidden">
                @foreach ($revenueTrend as $item)
                    <span class="w-full text-center">{{ $item['label'] }}</span>
                @endforeach
            </div>
        </div>

        <div class="bg-dark-lighter p-8 rounded-2xl border border-gray-800 shadow-lg">
            <h3 class="text-lg font-semibold text-gray-100 mb-6">Order distribution</h3>
            <div class="space-y-6">
                @foreach($orderDistribution as $cat)
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-400">{{ $cat['name'] }}</span>
                        <span class="text-sm font-bold text-gray-100">{{ $cat['percentage'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-2">
                        <div class="bg-{{ $cat['color'] }}-500 h-2 rounded-full shadow-[0_0_10px_rgba(var(--{{ $cat['color'] }}-500),0.3)]" style="width: {{ $cat['percentage'] }}%"></div>
                    </div>
                </div>
                @endforeach

                @if(empty($orderDistribution))
                    <p class="text-center text-gray-500 text-sm py-4">No order data available</p>
                @endif
            </div>
            <div class="mt-8 pt-6 border-t border-gray-800">
                <button class="w-full py-3 bg-primary/10 text-primary font-semibold rounded-xl border border-primary/20 hover:bg-primary hover:text-white transition-all">
                    View Full Report
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
