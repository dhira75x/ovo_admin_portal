@extends('layouts.admin')

@section('header', 'KYC Review')

@section('header-actions')
    <a href="{{ route('admin.kyc.index') }}" class="text-gray-400 hover:text-white text-sm">
        &larr; Back to List
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content: Document Preview and Actions -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Document Preview -->
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Document Preview</h3>
            
            <div class="bg-gray-800 rounded-lg p-4 flex items-center justify-center min-h-[300px] border border-gray-700">
                @if(Str::endsWith($submission->document_path, ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                    <img src="{{ $submission->document_path }}" alt="KYC Document" class="max-w-full max-h-[500px] rounded shadow-lg">
                @elseif(Str::endsWith($submission->document_path, ['.pdf']))
                    <iframe src="{{ $submission->document_path }}" class="w-full h-[500px] rounded border-none"></iframe>
                @else
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-400 mb-2">Preview not available for this file type.</p>
                        <a href="{{ $submission->document_path }}" target="_blank" class="text-primary hover:underline">Download Document</a>
                    </div>
                @endif
            </div>
            <div class="mt-4 text-center">
                 <a href="{{ $submission->document_path }}" target="_blank" class="text-primary hover:text-primary-light text-sm flex justify-center items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Open in New Tab / Download
                </a>
            </div>
        </div>

        <!-- Action Forms -->
        @if($submission->status === 'pending')
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Review Action</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Approve Form & Modal -->
                <div x-data="{ showApproveModal: false }">
                    <button @click="showApproveModal = true" type="button" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Approve Submission
                    </button>
                    <p class="text-xs text-gray-500 mt-2 text-center">User will be verified immediately.</p>

                    <!-- Modal -->
                    <div x-show="showApproveModal" 
                         class="fixed inset-0 z-50 overflow-y-auto" 
                         aria-labelledby="modal-title" 
                         role="dialog" 
                         aria-modal="true"
                         style="display: none;">
                        
                        <!-- Backdrop -->
                        <div x-show="showApproveModal" 
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" 
                             @click="showApproveModal = false"></div>
            
                        <div class="flex min-h-screen items-end justify-center p-4 text-center sm:items-center sm:p-0">
                            <!-- Modal Panel -->
                            <div x-show="showApproveModal" 
                                 x-transition:enter="ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                 x-transition:leave="ease-in duration-200"
                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                 class="relative transform overflow-hidden rounded-lg bg-dark-lighter text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-700">
                                
                                <div class="bg-dark-lighter px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-900/50 sm:mx-0 sm:h-10 sm:w-10">
                                            <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                            </svg>
                                        </div>
                                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                            <h3 class="text-base font-semibold leading-6 text-white" id="modal-title">Approve KYC Submission</h3>
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-400">Are you sure you want to approve this KYC submission? This action will verify the user and cannot be undone.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-700">
                                    <form action="{{ route('admin.kyc.approve', $submission->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto transition-colors">Confirm Approval</button>
                                    </form>
                                    <button type="button" @click="showApproveModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-300 shadow-sm ring-1 ring-inset ring-gray-600 hover:bg-gray-600 sm:mt-0 sm:w-auto transition-colors">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reject Form Toggle -->
                <button type="button" onclick="document.getElementById('reject-form').classList.remove('hidden')" class="w-full bg-red-600/20 hover:bg-red-600/30 text-red-500 hover:text-red-400 border border-red-600/50 font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Reject Submission
                </button>
            </div>

            <!-- Hidden Reject Form -->
            <div id="reject-form" class="hidden mt-6 pt-6 border-t border-gray-700">
                <form action="{{ route('admin.kyc.reject', $submission->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm text-gray-400 mb-1">Reason for Rejection *</label>
                        <select name="rejection_reason" id="rejection_reason" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500 mb-2" onchange="toggleCustomReason(this)">
                            <option value="Document is blurry or unreadable">Document is blurry or unreadable</option>
                            <option value="Document has expired">Document has expired</option>
                            <option value="Name does not match account details">Name does not match account details</option>
                            <option value="Invalid document type">Invalid document type</option>
                            <option value="custom">Other (Specify below)</option>
                        </select>
                        
                        <textarea name="custom_reason" id="custom_reason" rows="3" class="hidden w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white focus:outline-none focus:border-red-500 placeholder-gray-500" placeholder="Please provide details..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="document.getElementById('reject-form').classList.add('hidden')" class="px-4 py-2 text-gray-400 hover:text-white text-sm">Cancel</button>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                            Confirm Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
        
        <!-- Review Details (if not pending) -->
        @if($submission->status !== 'pending')
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Review Details</h3>
            
            <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs text-gray-500 uppercase tracking-wider block mb-1">Status</span>
                        @php
                            $statusColors = [
                                'approved' => 'text-green-400',
                                'rejected' => 'text-red-400',
                            ];
                            $statusClass = $statusColors[$submission->status] ?? 'text-gray-400';
                        @endphp
                        <span class="font-bold {{ $statusClass }} uppercase">{{ $submission->status }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase tracking-wider block mb-1">Reviewed By</span>
                        <span class="text-gray-300">{{ $submission->reviewer->name ?? 'System' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase tracking-wider block mb-1">Date</span>
                        <span class="text-gray-300">{{ $submission->is_reviewed ? $submission->reviewed_at->format('M d, Y h:i A') : 'N/A' }}</span>
                    </div>
                </div>
                
                @if($submission->status === 'rejected')
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <span class="text-xs text-red-500 uppercase tracking-wider block mb-1">Rejection Reason</span>
                    <p class="text-gray-300">{{ $submission->rejection_reason }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar: User Info -->
    <div class="space-y-6">
        <div class="bg-dark-lighter rounded-xl border border-gray-700 shadow-lg p-6">
            <h3 class="text-lg font-medium text-white mb-4">Applicant Details</h3>
            
            <div class="flex items-center mb-6">
                <div class="h-16 w-16 rounded-full bg-primary/20 flex items-center justify-center text-xl font-bold text-primary mr-4">
                    {{ substr($submission->user->name ?? 'U', 0, 1) }}
                </div>
                <div>
                     <h2 class="text-xl font-bold text-white">{{ $submission->user->name ?? 'Unknown' }}</h2>
                     <span class="inline-block px-2 py-0.5 rounded text-xs bg-gray-700 text-gray-300 mt-1">User #{{ $submission->user->id }}</span>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <span class="text-sm text-gray-400 block mb-1">Email Address</span>
                    <a href="mailto:{{ $submission->user->email }}" class="text-primary hover:underline">{{ $submission->user->email }}</a>
                </div>
                
                <hr class="border-gray-700 my-4">
                
                <div>
                    <span class="text-sm text-gray-400 block mb-1">Document Type</span>
                    <span class="text-white capitalize">{{ str_replace('_', ' ', $submission->document_type) }}</span>
                </div>

                <div>
                    <span class="text-sm text-gray-400 block mb-1">Submission Date</span>
                    <span class="text-white">{{ $submission->created_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleCustomReason(select) {
        const customTextarea = document.getElementById('custom_reason');
        if (select.value === 'custom') {
            customTextarea.classList.remove('hidden');
            customTextarea.name = 'rejection_reason'; // Use textarea value
            select.name = 'rejection_reason_select'; // Rename select to avoid conflict
        } else {
            customTextarea.classList.add('hidden');
            customTextarea.name = 'custom_reason'; // Reset name
            select.name = 'rejection_reason'; // Use select value
        }
    }
</script>
@endsection
