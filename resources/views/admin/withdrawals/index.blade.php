@extends('layouts.admin')

@section('header', 'Withdrawal Management')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-dark-lighter rounded-xl p-6 border border-gray-700 shadow-lg" x-data="{ showFilters: false }">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-200">Withdrawal Requests</h3>
            <button @click="showFilters = !showFilters" class="text-sm text-primary hover:text-primary-light transition-colors">
                <span x-show="!showFilters">Show Filters</span>
                <span x-show="showFilters">Hide Filters</span>
            </button>
        </div>
        
        <form action="{{ route('admin.withdrawals.index') }}" method="GET" x-show="showFilters" x-cloak class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-4 border-t border-gray-800">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-400 mb-1">Status</label>
                <select name="status" id="status" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label for="search" class="block text-sm font-medium text-gray-400 mb-1">Search Account/Bank</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Account or Bank..." class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Filter
                </button>
            </div>
            <div class="flex items-end">
                <a href="{{ route('admin.withdrawals.index') }}" class="w-full bg-gray-700 hover:bg-gray-600 text-white text-center font-medium py-2 px-4 rounded-lg transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Withdrawals Table -->
    <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-400">
                <thead class="bg-gray-800/50 text-gray-200 uppercase font-medium">
                    <tr>
                        <th class="px-6 py-4">Merchant</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Bank Details</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Requested At</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @php
                        $withdrawalList = $withdrawals['payload']['docs'] ?? $withdrawals['payload'] ?? [];
                    @endphp
                    @forelse($withdrawalList as $withdrawal)
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center text-xs font-bold text-primary mr-3">
                                    {{ substr($withdrawal['user']['businessName'] ?? $withdrawal['user']['name'] ?? 'M', 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-white font-medium">{{ $withdrawal['user']['businessName'] ?? $withdrawal['user']['name'] ?? 'Unknown' }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $withdrawal['userId'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-white font-bold">
                            {{ $withdrawal['currency'] ?? '₦' }}{{ number_format($withdrawal['amount'], 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-300">{{ $withdrawal['bank'] }}</div>
                            <div class="text-xs text-gray-500">{{ $withdrawal['accountNumber'] }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $status = $withdrawal['status'] ?? 'pending';
                                $statusClasses = [
                                    'pending' => 'bg-yellow-500/10 text-yellow-400',
                                    'success' => 'bg-green-500/10 text-green-400',
                                    'failed' => 'bg-red-500/10 text-red-100',
                                    'rejected' => 'bg-red-500/10 text-red-400',
                                ][$status] ?? 'bg-gray-500/10 text-gray-400';
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusClasses }} block w-fit">
                                {{ strtoupper($status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs">
                            {{ \Carbon\Carbon::parse($withdrawal['createdAt'])->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($status === 'pending')
                            <div class="flex items-center justify-end space-x-3" x-data="{ approving: false, rejecting: false }">
                                <form action="{{ route('admin.withdrawals.approve', $withdrawal['_id']) }}" method="POST" @submit="approving = true">
                                    @csrf
                                    <button type="submit" :disabled="approving || rejecting" class="text-green-500 hover:text-green-400 font-medium transition-colors disabled:opacity-50">
                                        <span x-show="!approving">Approve</span>
                                        <span x-show="approving">...</span>
                                    </button>
                                </form>
                                
                                <button @click="rejecting = !rejecting" :disabled="approving || rejecting" class="text-red-500 hover:text-red-400 font-medium transition-colors disabled:opacity-50">
                                    Reject
                                </button>

                                <div x-show="rejecting" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
                                    <div class="bg-dark-lighter p-6 rounded-xl border border-gray-700 shadow-2xl w-full max-w-md text-left">
                                        <h4 class="text-lg font-bold text-white mb-4">Reject Withdrawal</h4>
                                        <form action="{{ route('admin.withdrawals.reject', $withdrawal['_id']) }}" method="POST">
                                            @csrf
                                            <div class="mb-4">
                                                <label class="block text-sm text-gray-400 mb-2">Reason for rejection</label>
                                                <textarea name="comment" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary" rows="3" placeholder="Explain why this is being rejected..."></textarea>
                                            </div>
                                            <div class="flex justify-end space-x-3">
                                                <button type="button" @click="rejecting = false" class="px-4 py-2 text-gray-400 hover:text-white transition-colors">Cancel</button>
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors">Confirm Rejection</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @else
                                <span class="text-gray-600 text-xs italic">No actions available</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            No withdrawal requests found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
