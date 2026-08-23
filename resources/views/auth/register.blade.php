@extends('layouts.guest')

@section('content')
<div class="mb-8 text-center relative z-10">
    <h2 class="text-3xl font-bold text-white tracking-tight">Create an account</h2>
    <p class="text-gray-400 mt-2 text-sm">Join FlowHub and automate your workflow</p>
</div>

<form method="POST" action="{{ route('register') }}" class="relative z-10">
    @csrf

    <!-- Name -->
    <div>
        <label for="name" class="block font-medium text-sm text-gray-300">Full Name</label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <input id="name" class="block w-full pl-10 bg-black/20 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 transition-colors" type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" required autofocus autocomplete="name" />
        </div>
        @error('name')
            <p class="text-red-400 text-xs mt-2 flex items-center"><svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>{{ $message }}</p>
        @enderror
    </div>

    <!-- Email Address -->
    <div class="mt-5">
        <label for="email" class="block font-medium text-sm text-gray-300">Email Address</label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <input id="email" class="block w-full pl-10 bg-black/20 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 transition-colors" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autocomplete="username" />
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
            <input id="password" class="block w-full pl-10 bg-black/20 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 transition-colors" type="password" name="password" placeholder="••••••••" required autocomplete="new-password" />
        </div>
        @error('password')
            <p class="text-red-400 text-xs mt-2 flex items-center"><svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>{{ $message }}</p>
        @enderror
    </div>

    <!-- Confirm Password -->
    <div class="mt-5">
        <label for="password_confirmation" class="block font-medium text-sm text-gray-300">Confirm Password</label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <input id="password_confirmation" class="block w-full pl-10 bg-black/20 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 transition-colors" type="password" name="password_confirmation" placeholder="••••••••" required autocomplete="new-password" />
        </div>
    </div>

    <div class="mt-8">
        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-[#111827] transition-all duration-200">
            Create Account
        </button>
    </div>
    
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-400">
            Already registered? 
            <a href="{{ route('login') }}" class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors">Sign in here</a>
        </p>
    </div>
</form>
@endsection
