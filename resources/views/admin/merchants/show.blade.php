@extends('layouts.admin')

@section('header', $merchant['businessName'] ?? 'Merchant Details')

@section('header-actions')
    <a href="{{ route('admin.merchants.index') }}" class="text-gray-400 hover:text-white text-sm">
        &larr; Back to Merchants
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content: Reviews and Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Merchant Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-dark-lighter rounded-xl border border-gray-700 p-4">
                <span class="text-gray-400 text-xs uppercase font-semibold">Total Revenue</span>
                <div class="text-2xl font-bold text-white mt-1">NGN {{ number_format($analytics['revenue']['total'] ?? 0, 2) }}</div>
                <div class="text-xs {{ ($analytics['revenue']['growth'] ?? 0) >= 0 ? 'text-green-400' : 'text-red-400' }} mt-1">
                    {{ ($analytics['revenue']['growth'] ?? 0) >= 0 ? '+' : '' }}{{ $analytics['revenue']['growth'] ?? 0 }}% from last period
                </div>
            </div>
            <div class="bg-dark-lighter rounded-xl border border-gray-700 p-4">
                <span class="text-gray-400 text-xs uppercase font-semibold">Total Orders</span>
                <div class="text-2xl font-bold text-white mt-1">{{ number_format($analytics['orders']['total'] ?? 0) }}</div>
                <div class="text-xs text-blue-400 mt-1">
                    {{ $analytics['orders']['completionRate'] ?? 0 }}% completion rate
                </div>
            </div>
            <div class="bg-dark-lighter rounded-xl border border-gray-700 p-4">
                <span class="text-gray-400 text-xs uppercase font-semibold">Customers</span>
                <div class="text-2xl font-bold text-white mt-1">{{ number_format($analytics['customers']['total'] ?? 0) }}</div>
                <div class="text-xs {{ ($analytics['customers']['growth'] ?? 0) >= 0 ? 'text-green-400' : 'text-red-400' }} mt-1">
                    {{ ($analytics['customers']['growth'] ?? 0) >= 0 ? '+' : '' }}{{ $analytics['customers']['growth'] ?? 0 }}% growth
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-white">Customer Reviews</h3>
                <span class="text-sm text-gray-400">{{ $reviews->total() }} total</span>
            @if($reviews->count() > 0)
                <div class="space-y-4">
                    @foreach($reviews as $review)
                        @php
                            $reviewer = data_get($review, 'createdBy');
                            $reviewerName = data_get($reviewer, 'firstname') 
                                ? (data_get($reviewer, 'firstname') . ' ' . data_get($reviewer, 'lastname'))
                                : (data_get($review, 'user.name') ?? data_get($review, 'customer.name') ?? 'Unknown User');
                            $reviewerInitial = substr($reviewerName, 0, 1);
                        @endphp
                        <div class="border border-gray-700 rounded-lg p-4 bg-gray-800/50">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white mr-3">
                                        {{ $reviewerInitial }}
                                    </div>
                                    <div>
                                        <div class="text-white font-medium">{{ $reviewerName }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($review['createdAt'] ?? $review['created_at'] ?? now())->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center bg-gray-700/50 px-2 py-1 rounded">
                                    <span class="text-yellow-400 mr-1">★</span>
                                    <span class="text-white font-medium">{{ $review['count'] ?? $review['rating'] ?? 0 }}</span>
                                    <span class="text-gray-500 text-xs ml-1">/ 5</span>
                                </div>
                            </div>
                            @if($review['text'] ?? $review['comment'] ?? null)
                                <p class="text-gray-300 mt-3 text-sm">{{ $review['text'] ?? $review['comment'] }}</p>
                            @endif
                            @if($review['order_id'] ?? $review['orderId'] ?? null)
                                <div class="mt-2 text-xs text-gray-500">
                                    Order: #{{ $review['order_id'] ?? $review['orderId'] }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                    
                    <div class="mt-6">
                        {{ $reviews->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500 italic">No reviews yet for this merchant.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar: Merchant Info -->
    <div class="space-y-6">
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Business Information</h3>
            
            <div class="space-y-4">
                <div>
                    <span class="text-sm text-gray-400 block">Business Name</span>
                    <span class="text-white font-medium">{{ $merchant['businessName'] ?? 'N/A' }}</span>
                </div>
                
                <div>
                    <span class="text-sm text-gray-400 block">Status</span>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @if($merchant['isVerified'] ?? false)
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-500/10 text-green-400">
                                account Verified
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-500/10 text-yellow-400">
                                Account Pending
                            </span>
                        @endif

                        @php
                            $cacStatus = $merchant['cacStatus'] ?? 'pending';
                            $cacStatusClasses = [
                                'pending' => 'bg-yellow-500/10 text-yellow-400',
                                'approved' => 'bg-green-500/10 text-green-400',
                                'rejected' => 'bg-red-500/10 text-red-400',
                            ][$cacStatus] ?? 'bg-gray-500/10 text-gray-400';
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $cacStatusClasses }}">
                            CAC: {{ ucfirst($cacStatus) }}
                        </span>
                    </div>
                </div>

                <div>
                    <span class="text-sm text-gray-400 block">CAC Number</span>
                    <span class="text-white font-medium">{{ $merchant['cacNumber'] ?? 'N/A' }}</span>
                </div>

                @if($merchant['cacDocument'] ?? null)
                <div>
                    <span class="text-sm text-gray-400 block mb-2">Registration Document</span>
                    <div class="border border-gray-700 rounded-lg overflow-hidden bg-gray-900/50 mb-3">
                        @php
                            $isPdf = str_ends_with(strtolower($merchant['cacDocument']), '.pdf');
                        @endphp
                        
                        @if($isPdf)
                            <iframe src="{{ $merchant['cacDocument'] }}" class="w-full h-48 border-none"></iframe>
                        @else
                            <img src="{{ $merchant['cacDocument'] }}" alt="CAC Document" class="w-full h-auto max-h-64 object-contain">
                        @endif
                    </div>
                    
                    <div class="flex flex-col space-y-3">
                        <a href="{{ $merchant['cacDocument'] }}" target="_blank" class="text-primary hover:text-primary-light text-xs flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Open in Full Screen
                        </a>

                        @if(($merchant['cacStatus'] ?? 'pending') === 'pending')
                        <div class="pt-2 border-t border-gray-700 space-y-3">
                            <form action="{{ route('admin.merchants.approve', $merchant['_id']) }}" method="POST" onsubmit="return confirm('Approve this business registration?')">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-500 text-white text-xs font-bold py-2 px-4 rounded transition-colors">
                                    Approve CAC Document
                                </button>
                            </form>

                            <div x-data="{ showReject: false }">
                                <button @click="showReject = !showReject" class="w-full bg-red-600/20 hover:bg-red-600/30 text-red-500 text-xs font-bold py-2 px-4 rounded border border-red-500/30 transition-colors">
                                    Reject Submission
                                </button>
                                
                                <div x-show="showReject" x-cloak class="mt-3 p-3 bg-gray-800 rounded border border-gray-700">
                                    <form action="{{ route('admin.merchants.reject', $merchant['_id']) }}" method="POST">
                                        @csrf
                                        <label class="block text-xs text-gray-400 mb-2">Rejection Reason</label>
                                        <textarea name="reason" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white text-xs focus:border-primary focus:outline-none" placeholder="Explain why the document was rejected..." required></textarea>
                                        <button type="submit" class="w-full mt-2 bg-red-600 hover:bg-red-500 text-white text-xs font-bold py-2 rounded transition-colors">
                                            Confirm Rejection
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($merchant['cacRejectionReason'] ?? null)
                        <div class="mt-4 p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                            <span class="text-xs font-bold text-red-400 uppercase block mb-1">Rejection Reason</span>
                            <p class="text-xs text-gray-300">{{ $merchant['cacRejectionReason'] }}</p>
                        </div>
                    @endif

                    @if($merchant['cacReviewedAt'] ?? null)
                        <div class="mt-4 text-[10px] text-gray-500 italic">
                            Reviewed by {{ $merchant['cacReviewedBy']['firstname'] ?? 'Admin' }} on {{ \Carbon\Carbon::parse($merchant['cacReviewedAt'])->format('M d, Y H:i') }}
                        </div>
                    @endif
                </div>
                @endif
                
                <hr class="border-gray-700 my-4">

                <div>
                    <span class="text-sm text-gray-400 block">Owner / User</span>
                    <div class="flex items-center mt-2">
                        @php
                            $user = data_get($merchant, 'userId');
                            $userName = data_get($user, 'firstname')
                                ? (data_get($user, 'firstname') . ' ' . data_get($user, 'lastname'))
                                : (data_get($merchant, 'user.name') ?? 'Unknown');
                        @endphp
                        <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white mr-3">
                            {{ substr($userName, 0, 1) }}
                        </div>
                        <div>
                            <span class="text-white text-sm block">{{ $userName }}</span>
                            <span class="text-xs text-gray-500">{{ data_get($user, 'email') ?? data_get($merchant, 'user.email') ?? '' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="text-sm text-gray-400 block">Joined On</span>
                    <span class="text-white text-sm">{{ \Carbon\Carbon::parse($merchant['createdAt'] ?? now())->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
