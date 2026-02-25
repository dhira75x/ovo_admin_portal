@extends('layouts.admin')

@section('header', 'Support Tickets')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-dark-lighter rounded-xl p-6 border border-gray-700 shadow-lg">
        <form action="{{ route('admin.tickets.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-400 mb-1">Status</label>
                <select name="status" id="status" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-400 mb-1">Priority</label>
                <select name="priority" id="priority" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
                    <option value="all" {{ request('priority') == 'all' ? 'selected' : '' }}>All Priorities</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Filter Tickets
                </button>
            </div>
             <div class="flex items-end">
                <a href="{{ route('admin.tickets.index') }}" class="w-full bg-gray-700 hover:bg-gray-600 text-white text-center font-medium py-2 px-4 rounded-lg transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tickets Table -->
    <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-400">
                <thead class="bg-gray-800/50 text-gray-200 uppercase font-medium">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Subject</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Priority</th>
                        <th class="px-6 py-4">Last Updated</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-4 text-white font-medium">#{{ $ticket->id }}</td>
                        <td class="px-6 py-4 text-white">{{ Str::limit($ticket->subject, 40) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white mr-3">
                                    {{ substr($ticket->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-white">{{ $ticket->user->name ?? 'Unknown' }}</div>
                                    <div class="text-xs">{{ $ticket->user->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'open' => 'bg-green-500/10 text-green-400',
                                    'in_progress' => 'bg-yellow-500/10 text-yellow-400',
                                    'resolved' => 'bg-blue-500/10 text-blue-400',
                                    'closed' => 'bg-gray-700 text-gray-300',
                                ];
                                $statusLabel = ucfirst(str_replace('_', ' ', $ticket->status));
                                $statusClass = $statusColors[$ticket->status] ?? 'bg-gray-700 text-gray-300';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                             @php
                                $priorityColors = [
                                    'high' => 'text-red-400',
                                    'medium' => 'text-yellow-400',
                                    'low' => 'text-gray-400',
                                ];
                                $priorityClass = $priorityColors[$ticket->priority] ?? 'text-gray-400';
                            @endphp
                            <span class="font-medium {{ $priorityClass }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $ticket->updated_at->diffForHumans() }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="text-primary hover:text-primary-light font-medium transition-colors">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            No tickets found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($tickets->hasPages())
        <div class="px-6 py-4 border-t border-gray-700">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
