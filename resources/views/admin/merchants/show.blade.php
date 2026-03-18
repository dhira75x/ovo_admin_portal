@extends('layouts.admin')

@section('header', $merchant->name)

@section('header-actions')
    <a href="{{ route('admin.merchants.index') }}" class="text-gray-400 hover:text-white text-sm">
        &larr; Back to Merchants
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content: Reviews -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Reviews Section -->
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-white">Customer Reviews</h3>
                <span class="text-sm text-gray-400">{{ $reviews->total() }} total</span>
            </div>
            
            @if($reviews->count() > 0)
                <div class="space-y-4">
                    @foreach($reviews as $review)
                        <div class="border border-gray-700 rounded-lg p-4 bg-gray-800/50">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white mr-3">
                                        {{ substr($review->customer->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-white font-medium">{{ $review->customer->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500">{{ $review->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center bg-gray-700/50 px-2 py-1 rounded">
                                    <span class="text-yellow-400 mr-1">★</span>
                                    <span class="text-white font-medium">{{ $review->rating }}</span>
                                    <span class="text-gray-500 text-xs ml-1">/ 5</span>
                                </div>
                            </div>
                            @if($review->comment)
                                <p class="text-gray-300 mt-3 text-sm">{{ $review->comment }}</p>
                            @endif
                            @if($review->order_id)
                                <div class="mt-2 text-xs text-gray-500">
                                    Order #{{ $review->order_id }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                @if($reviews->hasPages())
                <div class="mt-4 pt-4 border-t border-gray-700">
                    {{ $reviews->links() }}
                </div>
                @endif
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <p>No reviews yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar: Merchant Info -->
    <div class="space-y-6">
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Merchant Details</h3>
            
            <div class="flex items-center mb-6">
                <div class="h-16 w-16 rounded-full bg-gray-700 flex items-center justify-center text-xl font-bold text-white mr-4">
                    {{ substr($merchant->name, 0, 1) }}
                </div>
                <div>
                    <div class="text-white font-medium text-lg">{{ $merchant->name }}</div>
                    <div class="text-sm text-gray-400">{{ $merchant->email }}</div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-gray-800/50 rounded-lg p-4">
                    <div class="text-sm text-gray-400 mb-1">Average Rating</div>
                    @if($merchant->reviews_received_avg_rating)
                        <div class="flex items-center">
                            <span class="text-yellow-400 text-2xl mr-2">★</span>
                            <span class="text-white text-2xl font-bold">{{ number_format($merchant->reviews_received_avg_rating, 1) }}</span>
                            <span class="text-gray-500 text-sm ml-1">/ 5</span>
                        </div>
                    @else
                        <span class="text-gray-500">No ratings yet</span>
                    @endif
                </div>
                
                <div class="bg-gray-800/50 rounded-lg p-4">
                    <div class="text-sm text-gray-400 mb-1">Total Reviews</div>
                    <div class="text-white text-2xl font-bold">{{ $merchant->reviews_received_count }}</div>
                </div>
                
                <div class="bg-gray-800/50 rounded-lg p-4">
                    <div class="text-sm text-gray-400 mb-1">Member Since</div>
                    <div class="text-white">{{ $merchant->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Rating Breakdown -->
        @if($merchant->reviews_received_count > 0)
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Rating Breakdown</h3>
            
            @php
                $ratings = $merchant->reviewsReceived->groupBy('rating')->map->count();
                $total = $merchant->reviews_received_count;
            @endphp
            
            <div class="space-y-2">
                @foreach([5, 4, 3, 2, 1] as $star)
                    @php
                        $count = $ratings[$star] ?? 0;
                        $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                    @endphp
                    <div class="flex items-center">
                        <span class="text-sm text-gray-400 w-8">{{ $star }} ★</span>
                        <div class="flex-1 mx-3 bg-gray-700 rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-sm text-gray-400 w-8 text-right">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
