@extends('layouts.admin')

@section('header', 'Ticket #' . ($ticket['_id'] ?? $ticket['id']))
@section('header-actions')
    <a href="{{ route('admin.tickets.index') }}" class="text-gray-400 hover:text-white text-sm">
        &larr; Back to Tickets
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="ticketChat()">
    <!-- Main Content: Messages -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Conversation</h3>
            
            <div id="messages-container" class="space-y-4 max-h-[600px] overflow-y-auto mb-6 pr-2 scroll-smooth">
                <!-- Initial Message (Always from Customer/User) -->
                <div class="flex flex-col space-y-2 items-start">
                    <div class="flex items-end flex-row">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white">
                                {{ substr(data_get($ticket, 'user.name') ?? 'U', 0, 1) }}
                            </div>
                        </div>
                        <div class="mx-3 bg-gray-800 p-4 rounded-lg rounded-bl-none max-w-[80%] text-gray-300">
                            <p class="text-sm font-semibold mb-1">{{ $ticket['subject'] ?? '' }}</p>
                            <p class="text-sm">{{ $ticket['message'] ?? $ticket['body'] ?? 'No content' }}</p>
                        </div>
                    </div>
                    <div class="ml-11 text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($ticket['createdAt'] ?? now())->format('M d, Y h:i A') }} - Customer
                    </div>
                </div>

                <!-- Conversation History -->
                <div id="chat-history" class="space-y-4">
                    @forelse($messages as $message)
                        @php
                            $customerUserId = data_get($ticket, 'createdBy._id') ?? data_get($ticket, 'createdBy') ?? '';
                            $messageUserId = data_get($message, 'createdBy._id') ?? data_get($message, 'createdBy') ?? '';
                            
                            // If the message sender is NOT the original ticket creator, it's an admin response
                            $isAdmin = $messageUserId && $messageUserId !== $customerUserId;
                            
                            $userName = data_get($message, 'createdBy.firstname') 
                                ? (data_get($message, 'createdBy.firstname') . ' ' . data_get($message, 'createdBy.lastname')) 
                                : ($isAdmin ? 'Admin' : (data_get($ticket, 'user.name') ?? 'User'));
                        @endphp
                        <div class="flex flex-col space-y-2 {{ $isAdmin ? 'items-end' : 'items-start' }} mt-4">
                            <div class="flex items-end {{ $isAdmin ? 'flex-row-reverse' : 'flex-row' }}">
                                <div class="flex-shrink-0">
                                    @if($isAdmin)
                                        <div class="h-8 w-8 rounded-full bg-primary flex items-center justify-center text-xs font-bold text-white">
                                            A
                                        </div>
                                    @else
                                        <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white">
                                            {{ substr($userName, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mx-3 p-4 rounded-lg max-w-[80%] {{ $isAdmin ? 'bg-primary/20 text-white rounded-br-none' : 'bg-gray-800 text-gray-300 rounded-bl-none' }}">
                                    <p class="text-sm">{{ $message['text'] ?? $message['message'] ?? '' }}</p>
                                    @if(isset($message['media']))
                                        <div class="mt-2 text-center">
                                            <img src="{{ $message['media'] }}" alt="Attachment" class="rounded max-w-full max-h-48 mx-auto">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 {{ $isAdmin ? 'mr-11' : 'ml-11' }}">
                                {{ \Carbon\Carbon::parse($message['createdAt'] ?? now())->format('M d, Y h:i A') }} - {{ $isAdmin ? 'Admin' : $userName }}
                            </div>
                        </div>
                    @empty
                        <!-- No replies yet -->
                    @endforelse
                </div>
            </div>

            <!-- Reply Form -->
            <form @submit.prevent="sendReply" id="reply-form" action="{{ route('admin.tickets.reply', $ticket['_id'] ?? $ticket['id']) }}" method="POST" class="mt-6">
                @csrf
                <div class="mb-4">
                    <label for="reply-message" class="sr-only">Reply</label>
                    <textarea x-model="replyMessage" name="message" id="reply-message" rows="3" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-4 text-white focus:outline-none focus:border-primary placeholder-gray-500" placeholder="Type your reply here..."></textarea>
                </div>
                <div class="flex justify-end items-center space-x-4">
                    <button type="submit" :disabled="sending || !replyMessage.trim()" class="flex items-center space-x-2 bg-primary hover:bg-primary-dark disabled:opacity-50 disabled:cursor-not-allowed text-white font-medium py-2 px-6 rounded-lg transition-colors">
                        <template x-if="sending">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="sending ? 'sending...' : 'Send Reply'"></span>
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
                    <span class="text-white font-medium">{{ $ticket['subject'] ?? '' }}</span>
                </div>
                 <div>
                    <span class="text-sm text-gray-400 block">User</span>
                    <div class="flex items-center mt-1">
                        <div class="h-6 w-6 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white mr-2">
                            {{ substr(data_get($ticket, 'user.name') ?? 'U', 0, 1) }}
                        </div>
                        <span class="text-white">{{ data_get($ticket, 'user.name') ?? 'Unknown' }}</span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ data_get($ticket, 'user.email') ?? '' }}</div>
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
                        $status = $ticket['status'] ?? 'open';
                        $statusLabel = ucfirst(str_replace('_', ' ', $status));
                        $statusClass = $statusColors[$status] ?? 'bg-gray-700 text-gray-300';
                    @endphp
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }} inline-block mt-1">
                        {{ $statusLabel }}
                    </span>
                </div>
                <div>
                    <span class="text-sm text-gray-400 block">Priority</span>
                    <span class="text-white font-medium capitalize">{{ $ticket['priority'] ?? 'low' }}</span>
                </div>
                <div>
                    <span class="text-sm text-gray-400 block">Created</span>
                    <span class="text-white">{{ \Carbon\Carbon::parse($ticket['createdAt'] ?? now())->format('M d, Y h:i A') }}</span>
                </div>
            </div>
            
            <hr class="border-gray-700 my-6">

            <h4 class="text-white font-medium mb-3">Update Status</h4>
            <form action="{{ route('admin.tickets.updateStatus', $ticket['_id'] ?? $ticket['id']) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <select name="status" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
                        <option value="open" {{ ($ticket['status'] ?? '') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ ($ticket['status'] ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ ($ticket['status'] ?? '') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ ($ticket['status'] ?? '') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Update Status
                </button>
            </form>

            <hr class="border-gray-700 my-6">

            <h4 class="text-white font-medium mb-3">Update Priority</h4>
            <form action="{{ route('admin.tickets.updatePriority', $ticket['_id'] ?? $ticket['id']) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <select name="priority" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
                        <option value="low" {{ ($ticket['priority'] ?? '') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ ($ticket['priority'] ?? '') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ ($ticket['priority'] ?? '') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    Update Priority
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function ticketChat() {
        return {
            replyMessage: '',
            sending: false,
            
            init() {
                this.scrollToBottom();
            },
            
            scrollToBottom() {
                const container = document.getElementById('messages-container');
                if (container) {
                    setTimeout(() => {
                        container.scrollTop = container.scrollHeight;
                    }, 50);
                }
            },
            
            async sendReply() {
                if (!this.replyMessage.trim() || this.sending) return;
                
                this.sending = true;
                const form = document.getElementById('reply-form');
                const formData = new FormData(form);
                formData.set('message', this.replyMessage);
                
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.status === 'success') {
                        // Append the message to the UI
                        this.appendMessage(data.payload);
                        this.replyMessage = '';
                        this.scrollToBottom();
                    } else {
                        alert('Failed to send reply. Please try again.');
                    }
                } catch (error) {
                    console.error('Error sending reply:', error);
                    alert('An error occurred. Please try again.');
                } finally {
                    this.sending = false;
                }
            },
            
            appendMessage(payload) {
                if (!payload) return;
                
                const history = document.getElementById('chat-history');
                const now = new Date();
                const formattedDate = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' ' + 
                                    now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                
                const messageHtml = `
                    <div class="flex flex-col space-y-2 items-end mt-4">
                        <div class="flex items-end flex-row-reverse">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-primary flex items-center justify-center text-xs font-bold text-white">
                                    A
                                </div>
                            </div>
                            <div class="mx-3 p-4 rounded-lg max-w-[80%] bg-primary/20 text-white rounded-br-none">
                                <p class="text-sm">${this.escapeHtml(payload.text || payload.message || '')}</p>
                                ${payload.media ? `
                                    <div class="mt-2 text-center">
                                        <img src="${payload.media}" alt="Attachment" class="rounded max-w-full max-h-48 mx-auto">
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 mr-11">
                            ${formattedDate} - Admin
                        </div>
                    </div>
                `;
                
                history.insertAdjacentHTML('beforeend', messageHtml);
            },
            
            escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        }
    }
</script>
@endsection
