@extends('layouts.admin')

@section('header', 'Ticket #' . $ticket->id)
@section('header-actions')
    <a href="{{ route('admin.tickets.index') }}" class="text-gray-400 hover:text-white text-sm">
        &larr; Back to Tickets
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content: Messages -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Conversation</h3>
            
            <div class="space-y-4 max-h-[600px] overflow-y-auto mb-6 pr-2">
                <!-- Initial Ticket Description as first message -->
                <div class="flex flex-col space-y-2">
                    <div class="flex items-end">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white">
                                {{ substr($ticket->user->name ?? 'U', 0, 1) }}
                            </div>
                        </div>
                        <div class="ml-3 bg-gray-800 p-4 rounded-lg rounded-bl-none max-w-[80%]">
                            <p class="text-sm text-gray-300">{{ $ticket->subject }}</p>
                            <!-- If the ticket has an initial description content column, use it here. 
                                 Assuming subject + messages is the structure for now or first message is separate. 
                                 If there is no 'content' column in tickets table, we rely on messages. -->
                        </div>
                    </div>
                    <div class="ml-11 text-xs text-gray-500">
                        {{ $ticket->created_at->format('M d, Y h:i A') }}
                    </div>
                </div>

                @foreach($ticket->messages as $message)
                    <div class="flex flex-col space-y-2 {{ $message->admin_id ? 'items-end' : 'items-start' }}">
                        <div class="flex items-end {{ $message->admin_id ? 'flex-row-reverse' : 'flex-row' }}">
                            <div class="flex-shrink-0">
                                @if($message->admin_id)
                                    <div class="h-8 w-8 rounded-full bg-primary flex items-center justify-center text-xs font-bold text-white">
                                        A
                                    </div>
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white">
                                        {{ substr($message->user->name ?? 'U', 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="mx-3 p-4 rounded-lg max-w-[80%] {{ $message->admin_id ? 'bg-primary/20 text-white rounded-br-none' : 'bg-gray-800 text-gray-300 rounded-bl-none' }}">
                                <p class="text-sm">{{ $message->message }}</p>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 {{ $message->admin_id ? 'mr-11' : 'ml-11' }}">
                            {{ $message->created_at->format('M d, Y h:i A') }} - {{ $message->admin_id ? 'Admin' : ($message->user->name ?? 'User') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Reply Form -->
            <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST" class="mt-6">
                @csrf
                <div class="mb-4">
                    <label for="message" class="sr-only">Reply</label>
                    <textarea name="message" id="message" rows="3" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-4 text-white focus:outline-none focus:border-primary placeholder-gray-500" placeholder="Type your reply here..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-medium py-2 px-6 rounded-lg transition-colors">
                        Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar: Ticket Info -->
    <div class="space-y-6">
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Ticket Details</h3>
            
            <div class="space-y-4">
                <div>
                    <span class="text-sm text-gray-400 block">Subject</span>
                    <span class="text-white font-medium">{{ $ticket->subject }}</span>
                </div>
                 <div>
                    <span class="text-sm text-gray-400 block">User</span>
                    <div class="flex items-center mt-1">
                        <div class="h-6 w-6 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white mr-2">
                            {{ substr($ticket->user->name ?? 'U', 0, 1) }}
                        </div>
                        <span class="text-white">{{ $ticket->user->name ?? 'Unknown' }}</span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ $ticket->user->email ?? '' }}</div>
                </div>
                <div>
                    <span class="text-sm text-gray-400 block">Status</span>
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
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }} inline-block mt-1">
                        {{ $statusLabel }}
                    </span>
                </div>
                <div>
                    <span class="text-sm text-gray-400 block">Priority</span>
                    <span class="text-white font-medium capitalize">{{ $ticket->priority }}</span>
                </div>
                <div>
                    <span class="text-sm text-gray-400 block">Created</span>
                    <span class="text-white">{{ $ticket->created_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
            
            <hr class="border-gray-700 my-6">

            <h4 class="text-white font-medium mb-3">Update Status</h4>
            <form action="{{ route('admin.tickets.updateStatus', $ticket->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <select name="status" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
                        <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Update Status
                </button>
            </form>

            <hr class="border-gray-700 my-6">

            <h4 class="text-white font-medium mb-3">Update Priority</h4>
            <form action="{{ route('admin.tickets.updatePriority', $ticket->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <select name="priority" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
                        <option value="low" {{ $ticket->priority == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ $ticket->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ $ticket->priority == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Update Priority
                </button>
            </form>
        </div>
        

    </div>
</div>
@endsection
