@extends('layouts.admin')

@section('header', 'Disputes')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-dark-lighter rounded-xl border border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.disputes.index') }}" class="flex flex-wrap items-center gap-4">
            <div>
                <label for="status" class="sr-only">Status</label>
                <select name="status" id="status" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-primary">
                    <option value="all">All Statuses</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                Filter
            </button>
            @if(request()->has('status'))
                <a href="{{ route('admin.disputes.index') }}" class="text-gray-400 hover:text-white text-sm underline">Clear Filters</a>
            @endif
        </form>
    </div>

    <!-- Disputes List -->
    <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-400">
                <thead class="bg-gray-800 text-gray-200 uppercase font-medium">
                    <tr>
                        <th scope="col" class="px-6 py-4">ID</th>
                        <th scope="col" class="px-6 py-4">Claimant</th>
                        <th scope="col" class="px-6 py-4">Respondent</th>
                         <th scope="col" class="px-6 py-4">Reason</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4">Date</th>
                        <th scope="col" class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($disputes as $dispute)
                    <tr class="hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-white">#{{ $dispute->id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white mr-3">
                                    {{ substr($dispute->claimant->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-white font-medium">{{ $dispute->claimant->name ?? 'Unknown' }}</div>
                                    <div class="text-xs">{{ $dispute->claimant->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                         <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white mr-3">
                                    {{ substr($dispute->respondent->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-white font-medium">{{ $dispute->respondent->name ?? 'Unknown' }}</div>
                                    <div class="text-xs">{{ $dispute->respondent->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            {{ Str::limit($dispute->reason, 30) }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'open' => 'bg-green-500/10 text-green-400',
                                    'resolved' => 'bg-blue-500/10 text-blue-400',
                                    'closed' => 'bg-gray-700 text-gray-300',
                                ];
                                $statusLabel = ucfirst($dispute->status);
                                $statusClass = $statusColors[$dispute->status] ?? 'bg-gray-700 text-gray-300';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            {{ $dispute->created_at->format('M d, Y') }}<br>
                            <span class="text-xs">{{ $dispute->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.disputes.show', $dispute->id) }}" class="text-primary hover:text-primary-light font-medium transition-colors">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                </svg>
                                <p class="text-lg font-medium">No disputes found</p>
                                <p class="text-sm mt-1">Try adjusting your filters.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($disputes->hasPages())
        <div class="px-6 py-4 border-t border-gray-700 bg-gray-800">
            {{ $disputes->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
