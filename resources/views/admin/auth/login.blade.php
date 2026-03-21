@extends('layouts.admin-auth')

@section('content')
<div class="max-w-md w-full space-y-8" 
    x-data="{ 
        showResetModal: false, 
        step: 1, 
        email: '{{ old('email') }}', 
        otp: '', 
        newPassword: '', 
        confirmPassword: '', 
        loading: false, 
        message: '', 
        error: '',
        async requestOTP() {
            if (!this.email) { this.error = 'Email is required'; return; }
            this.loading = true; this.error = ''; this.message = '';
            try {
                const res = await fetch('{{ route('admin.forgot-password') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({ email: this.email })
                });
                const data = await res.json();
                if (data.status === 'OK') {
                    this.step = 2;
                    this.message = 'Verification code sent to your email.';
                } else {
                    this.error = data.message || 'Failed to send code.';
                }
            } catch (e) { this.error = 'An error occurred. Please try again.'; }
            this.loading = false;
        },
        async resetPassword() {
            if (this.newPassword !== this.confirmPassword) { this.error = 'Passwords do not match'; return; }
            if (this.newPassword.length < 8) { this.error = 'Password must be at least 8 characters'; return; }
            this.loading = true; this.error = ''; this.message = '';
            try {
                const res = await fetch('{{ route('admin.reset-password') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({ email: this.email, otp: this.otp, newPassword: this.newPassword })
                });
                const data = await res.json();
                if (data.status === 'OK') {
                    this.step = 3;
                    this.message = 'Password reset successfully. You can now login.';
                } else {
                    this.error = data.message || 'Failed to reset password.';
                }
            } catch (e) { this.error = 'An error occurred. Please try again.'; }
            this.loading = false;
        }
    }">
    <div class="bg-[#1C1C2E] p-10 rounded-3xl shadow-2xl relative overflow-hidden">
        <!-- Top Reflection/Glow Effect -->
        <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-32 h-1 bg-white/10 rounded-b-full"></div>

        <div>
            <h2 class="mt-2 text-center text-xl font-medium tracking-widest text-white uppercase">
                Login
            </h2>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            
            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/50 text-red-500 px-4 py-3 rounded-xl text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="rounded-md space-y-5">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" x-model="email" autocomplete="email" required 
                        class="appearance-none rounded-xl relative block w-full px-4 py-4 border border-transparent placeholder-gray-500 text-gray-300 bg-[#2C2C3E] focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent focus:z-10 sm:text-sm transition-all duration-200 @error('email') border-red-500/50 @enderror" 
                        placeholder="Email address">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                        class="appearance-none rounded-xl relative block w-full px-4 py-4 border border-transparent placeholder-gray-500 text-gray-300 bg-[#2C2C3E] focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent focus:z-10 sm:text-sm transition-all duration-200" 
                        placeholder="Password">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-600 rounded bg-[#2C2C3E]">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-400">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" @click.prevent="showResetModal = true; step = 1; error = ''; message = ''" class="font-medium text-primary hover:text-primary/80 transition-colors">
                        Forgot password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary focus:ring-offset-gray-900 transition-all duration-200 shadow-lg shadow-primary/30 uppercase tracking-widest">
                    Log in
                </button>
            </div>
        </form>
    </div>

    <!-- Password Reset Modal -->
    <div x-show="showResetModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak>
        
        <div class="bg-[#1C1C2E] w-full max-w-md p-8 rounded-3xl shadow-2xl relative border border-white/5"
            @click.away="showResetModal = false">
            
            <!-- Close Button -->
            <button @click="showResetModal = false" class="absolute top-4 right-4 text-gray-500 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Step 1: Request OTP -->
            <div x-show="step === 1">
                <h3 class="text-xl font-medium text-white text-center mb-6 uppercase tracking-widest">Reset Password</h3>
                <p class="text-gray-400 text-sm mb-6 text-center">Enter your email and we'll send you a verification code.</p>
                
                <input type="email" x-model="email" class="w-full px-4 py-4 rounded-xl border border-transparent placeholder-gray-500 text-gray-300 bg-[#2C2C3E] focus:outline-none focus:ring-2 focus:ring-primary mb-4 sm:text-sm" placeholder="Email address">
                
                <div x-show="error" class="mb-4 text-red-500 text-sm text-center" x-text="error"></div>
                
                <button @click="requestOTP()" :disabled="loading" class="w-full py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 disabled:opacity-50 transition-all uppercase tracking-widest shadow-lg shadow-primary/20">
                    <span x-show="!loading">Send Code</span>
                    <span x-show="loading">Sending...</span>
                </button>
            </div>

            <!-- Step 2: Verify OTP & New Password -->
            <div x-show="step === 2">
                <h3 class="text-xl font-medium text-white text-center mb-6 uppercase tracking-widest">Verify Code</h3>
                <div x-show="message" class="mb-4 text-green-500 text-sm text-center" x-text="message"></div>
                
                <div class="space-y-4">
                    <input type="text" x-model="otp" maxlength="6" class="w-full px-4 py-4 rounded-xl border border-transparent placeholder-gray-500 text-gray-300 bg-[#2C2C3E] focus:outline-none focus:ring-2 focus:ring-primary text-center tracking-[1em] text-lg font-bold" placeholder="000000">
                    
                    <input type="password" x-model="newPassword" class="w-full px-4 py-4 rounded-xl border border-transparent placeholder-gray-500 text-gray-300 bg-[#2C2C3E] focus:outline-none focus:ring-2 focus:ring-primary" placeholder="New Password">
                    
                    <input type="password" x-model="confirmPassword" class="w-full px-4 py-4 rounded-xl border border-transparent placeholder-gray-500 text-gray-300 bg-[#2C2C3E] focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Confirm Password">
                </div>
                
                <div x-show="error" class="mt-4 text-red-500 text-sm text-center" x-text="error"></div>
                
                <button @click="resetPassword()" :disabled="loading" class="w-full mt-6 py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 disabled:opacity-50 transition-all uppercase tracking-widest shadow-lg shadow-primary/20">
                    <span x-show="!loading">Reset Password</span>
                    <span x-show="loading">Resetting...</span>
                </button>
                
                <button @click="step = 1" class="w-full mt-4 text-sm text-gray-500 hover:text-gray-300 transition-colors">Back to Email</button>
            </div>

            <!-- Step 3: Success -->
            <div x-show="step === 3" class="text-center">
                <div class="mb-6 flex justify-center">
                    <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center text-green-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-medium text-white mb-4 uppercase tracking-widest">Success!</h3>
                <p class="text-gray-400 text-sm mb-8" x-text="message"></p>
                <button @click="showResetModal = false" class="w-full py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-all uppercase tracking-widest shadow-lg shadow-primary/20">
                    Back to Login
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
