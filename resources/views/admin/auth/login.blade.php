@extends('layouts.admin-auth')

@section('content')
<div class="max-w-md w-full space-y-8">
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
            
            <div class="rounded-md space-y-5">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                        class="appearance-none rounded-xl relative block w-full px-4 py-4 border border-transparent placeholder-gray-500 text-gray-300 bg-[#2C2C3E] focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent focus:z-10 sm:text-sm transition-all duration-200" 
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
                    <a href="#" class="font-medium text-primary hover:text-primary/80">
                        Forgot password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-1/2 mx-auto flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary focus:ring-offset-gray-900 transition-all duration-200 shadow-lg shadow-primary/30">
                    Sign in
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
