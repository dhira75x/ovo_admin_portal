@extends('layouts.admin')

@section('header', 'KYC Submissions')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-dark-lighter rounded-xl border border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.kyc.index') }}" class="flex flex-wrap items-center gap-4">
            <div>
                <label for="status" class="sr-only">Status</label>
                <select name="status" id="status" class="bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-primary">
                    <option value="all">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                Filter
            </button>
            @if(request()->has('status'))
                <a href="{{ route('admin.kyc.index') }}" class="text-gray-400 hover:text-white text-sm underline">Clear Filters</a>
            @endif
        </form>
    </div>

    <!-- KYC List -->
    <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-400">
                <thead class="bg-gray-800 text-gray-200 uppercase font-medium">
                    <tr>
                        <th scope="col" class="px-6 py-4">ID</th>
                        <th scope="col" class="px-6 py-4">User</th>
                        <th scope="col" class="px-6 py-4">Document Type</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4">Submitted At</th>
                        <th scope="col" class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($submissions as $submission)
                    <tr class="hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-white">#{{ $submission->id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white mr-3">
                                    {{ substr($submission->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-white font-medium">{{ $submission->user->name ?? 'Unknown' }}</div>
                                    <div class="text-xs">{{ $submission->user->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-white">
                            {{ ucfirst(str_replace('_', ' ', $submission->document_type)) }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-500/10 text-yellow-400',
                                    'approved' => 'bg-green-500/10 text-green-400',
                                    'rejected' => 'bg-red-500/10 text-red-400',
                                ];
                                $statusLabel = ucfirst($submission->status);
                                $statusClass = $statusColors[$submission->status] ?? 'bg-gray-700 text-gray-300';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            {{ $submission->created_at->format('M d, Y') }}<br>
                            <span class="text-xs">{{ $submission->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.kyc.show', $submission->id) }}" class="text-primary hover:text-primary-light font-medium transition-colors">
                                Review
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-lg font-medium">No KYC submissions found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($submissions->hasPages())
        <div class="px-6 py-4 border-t border-gray-700 bg-gray-800">
            {{ $submissions->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
