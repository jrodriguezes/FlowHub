@extends('layouts.guest')

@section('content')
<div class="mb-8 text-center relative z-10">
    <h2 class="text-3xl font-bold text-white tracking-tight">Welcome back</h2>
    <p class="text-gray-400 mt-2 text-sm">Sign in to your account to continue</p>
</div>

<form method="POST" action="{{ route('login') }}" class="relative z-10">
    @csrf

    <!-- Email Address -->
    <div>
        <label for="email" class="block font-medium text-sm text-gray-300">Email Address</label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <input id="email" class="block w-full pl-10 bg-black/20 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 transition-colors" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="username" />
        </div>
        @error('email')
            <p class="text-red-400 text-xs mt-2 flex items-center"><svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div class="mt-5">
        <label for="password" class="block font-medium text-sm text-gray-300">Password</label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <input id="password" class="block w-full pl-10 bg-black/20 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 transition-colors" type="password" name="password" placeholder="••••••••" required autocomplete="current-password" />
        </div>
        @error('password')
            <p class="text-red-400 text-xs mt-2 flex items-center"><svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>{{ $message }}</p>
        @enderror
    </div>

    <div class="mt-8">
        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-[#111827] transition-all duration-200">
            Sign In
        </button>
    </div>
    
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-400">
            Don't have an account? 
            <a href="{{ route('register') }}" class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors">Create one now</a>
        </p>
    </div>
</form>
@endsection
