@extends('layouts.admin')

@section('header', 'Dispute #' . $dispute->id)

@section('header-actions')
    <a href="{{ route('admin.disputes.index') }}" class="text-gray-400 hover:text-white text-sm">
        &larr; Back to Disputes
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content: Evidence and Resolution -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Dispute Details -->
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Dispute Reason</h3>
            <p class="text-gray-300 leading-relaxed">{{ $dispute->reason }}</p>
        </div>

        <!-- Evidence -->
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Evidence</h3>
            @if($evidence->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($evidence as $item)
                        <div class="border border-gray-700 rounded-lg p-4 bg-gray-800">
                            <div class="flex items-center mb-2">
                                <div class="h-6 w-6 rounded-full bg-gray-600 flex items-center justify-center text-xs font-bold text-white mr-2">
                                    {{ substr($item->uploader->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="text-sm text-gray-300">{{ $item->uploader->name ?? 'Unknown' }}</span>
                                <span class="text-xs text-gray-500 ml-auto">{{ $item->created_at->format('M d, Y') }}</span>
                            </div>
                            <p class="text-sm text-gray-400 mb-2">{{ $item->description }}</p>
                            @if($item->file_path)
                                <a href="#" class="text-primary hover:underline text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    View Attachment
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 italic">No evidence submitted yet.</p>
            @endif
        </div>

        <!-- Resolution Form -->
        @if($dispute->status === 'open')
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Resolve Dispute</h3>
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Resolve Dispute</h3>
            
            <div x-data="{ showResolveModal: false }">
                <!-- Trigger Button -->
                <button @click="showResolveModal = true" class="w-full bg-primary hover:bg-primary-dark text-white font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Process Resolution
                </button>

                <!-- Modal -->
                <div x-show="showResolveModal" 
                     class="fixed inset-0 z-50 overflow-y-auto" 
                     aria-labelledby="modal-title" 
                     role="dialog" 
                     aria-modal="true"
                     style="display: none;">
                    
                    <!-- Backdrop -->
                    <div x-show="showResolveModal" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" 
                         @click="showResolveModal = false"></div>
        
                    <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <!-- Modal Panel -->
                        <div x-show="showResolveModal" 
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             class="relative transform overflow-hidden rounded-lg bg-dark-lighter text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-gray-700">
                            
                            <div class="bg-dark-lighter px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-700">
                                <h3 class="text-xl font-semibold leading-6 text-white" id="modal-title">Resolve Dispute</h3>
                            </div>
                            
                            <form action="{{ route('admin.disputes.resolve', $dispute->id) }}" method="POST">
                                @csrf
                                <div class="p-6 space-y-4">
                                    <div>
                                        <label class="block text-sm text-gray-400 mb-1">Resolution Decision</label>
                                        <select name="status" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
                                            <option value="resolved">Mark as Resolved</option>
                                            <option value="closed">Close Dispute</option>
                                        </select>
                                    </div>

                                    <div>
                                         <label class="block text-sm text-gray-400 mb-1">Winner (Who gets the funds?)</label>
                                         <select name="winner_id" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
                                            <option value="" disabled selected>Select Winner</option>
                                            <option value="{{ $dispute->claimant_id }}">Claimant: {{ $dispute->claimant->name ?? 'Unknown' }}</option>
                                            <option value="{{ $dispute->respondent_id }}">Respondent: {{ $dispute->respondent->name ?? 'Unknown' }}</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="resolution_notes" class="block text-sm text-gray-400 mb-1">Resolution Notes</label>
                                        <textarea name="resolution_notes" id="resolution_notes" rows="4" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white focus:outline-none focus:border-primary placeholder-gray-500" placeholder="Explain the resolution..."></textarea>
                                    </div>
                                </div>

                                <div class="bg-gray-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-700">
                                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-primary hover:bg-primary-dark px-3 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto transition-colors">Submit Resolution</button>
                                    <button type="button" @click="showResolveModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-300 shadow-sm ring-1 ring-inset ring-gray-600 hover:bg-gray-600 sm:mt-0 sm:w-auto transition-colors">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        @endif
        
        <!-- Resolution Details (if resolved) -->
         @if($dispute->status !== 'open')
        <div class="bg-green-900/10 border border-green-500/30 rounded-xl p-6">
            <h3 class="text-lg font-medium text-green-400 mb-2">Resolution Details</h3>
            <p class="text-gray-300 mb-2">{{ $dispute->resolution_notes }}</p>
            <div class="text-sm text-gray-500">
                Resolved by Admin #{{ $dispute->resolved_by }} on {{ $dispute->resolved_at->format('M d, Y h:i A') }}
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar: Dispute Info -->
    <div class="space-y-6">
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Details</h3>
            
            <div class="space-y-4">
                <div>
                    <span class="text-sm text-gray-400 block">Status</span>
                     @php
                        $statusColors = [
                            'open' => 'bg-green-500/10 text-green-400',
                            'resolved' => 'bg-blue-500/10 text-blue-400',
                            'closed' => 'bg-gray-700 text-gray-300',
                        ];
                        $statusLabel = ucfirst($dispute->status);
                        $statusClass = $statusColors[$dispute->status] ?? 'bg-gray-700 text-gray-300';
                    @endphp
                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }} inline-block mt-1">
                        {{ $statusLabel }}
                    </span>
                </div>
                <div>
                    <span class="text-sm text-gray-400 block">Created</span>
                    <span class="text-white">{{ $dispute->created_at->format('M d, Y h:i A') }}</span>
                </div>
                
                <hr class="border-gray-700 my-4">

                <!-- Claimant -->
                <div>
                     <span class="text-sm text-primary block font-medium mb-1">Claimant</span>
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white mr-2">
                             {{ substr($dispute->claimant->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                             <span class="text-white block">{{ $dispute->claimant->name ?? 'Unknown' }}</span>
                             <span class="text-xs text-gray-500">{{ $dispute->claimant->email ?? '' }}</span>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-700 my-4">

                 <!-- Respondent -->
                <div>
                     <span class="text-sm text-primary block font-medium mb-1">Respondent</span>
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-gray-700 flex items-center justify-center text-xs font-bold text-white mr-2">
                             {{ substr($dispute->respondent->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                             <span class="text-white block">{{ $dispute->respondent->name ?? 'Unknown' }}</span>
                             <span class="text-xs text-gray-500">{{ $dispute->respondent->email ?? '' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
