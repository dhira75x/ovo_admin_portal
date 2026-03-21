@extends('layouts.admin')

@section('header', 'Merchants')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-dark-lighter rounded-xl p-6 border border-gray-700 shadow-lg">
        <form action="{{ route('admin.merchants.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-400 mb-1">Search Merchant</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name or email..." class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Filter Merchants
                </button>
            </div>
            <div class="flex items-end">
                <a href="{{ route('admin.merchants.index') }}" class="w-full bg-gray-700 hover:bg-gray-600 text-white text-center font-medium py-2 px-4 rounded-lg transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Merchants Table -->
    <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-400">
                <thead class="bg-gray-800/50 text-gray-200 uppercase font-medium">
                    <tr>
                        <th class="px-6 py-4">Merchant</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Verification</th>
                        <th class="px-6 py-4">Rating</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($merchants as $merchant)
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center text-sm font-bold text-white mr-3">
                                    {{ substr($merchant['businessName'] ?? $merchant['name'] ?? 'M', 0, 1) }}
                                </div>
                                <div class="text-white font-medium">{{ $merchant['businessName'] ?? $merchant['name'] ?? 'Unknown' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">{{ data_get($merchant, 'user.email') ?? $merchant['email'] ?? '' }}</td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                @if($merchant['isVerified'] ?? false)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-500/10 text-green-400 block w-fit">
                                        Verified
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-500/10 text-yellow-400 block w-fit">
                                        Pending
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
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $cacStatusClasses }} block w-fit">
                                    CAC: {{ strtoupper($cacStatus) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $avgRating = data_get($merchant, 'reviews_received_avg_rating') ?? 0;
                            @endphp
                            @if($avgRating > 0)
                                <div class="flex items-center">
                                    <span class="text-yellow-400 mr-1">★</span>
                                    <span class="text-white font-medium">{{ number_format($avgRating, 1) }}</span>
                                    <span class="text-gray-500 text-xs ml-1">/ 5</span>
                                </div>
                            @else
                                <span class="text-gray-500">No ratings</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.merchants.show', $merchant['_id'] ?? $merchant['id']) }}" class="text-primary hover:text-primary-light font-medium transition-colors">View Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            No merchants found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($merchants->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $merchants->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
