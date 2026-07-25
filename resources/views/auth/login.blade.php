@extends('layouts.app')

@section('content')
<div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-gray-800 border border-gray-700 shadow-xl overflow-hidden sm:rounded-lg">
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-white">Sign in to your account</h2>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-medium text-sm text-gray-300">Email</label>
            <input id="email" class="block mt-1 w-full rounded-md shadow-sm bg-gray-900 border-gray-600 text-white focus:border-indigo-400 focus:ring-indigo-400 py-2 px-3 border" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
            @error('email')
                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-300">Password</label>
            <input id="password" class="block mt-1 w-full rounded-md shadow-sm bg-gray-900 border-gray-600 text-white focus:border-indigo-400 focus:ring-indigo-400 py-2 px-3 border" type="password" name="password" required autocomplete="current-password" />
            @error('password')
                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-400 hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-indigo-400 transition" href="{{ route('register') }}">
                Don't have an account?
            </a>

            <button type="submit" class="ms-3 inline-flex items-center px-4 py-2 bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-400 focus:bg-indigo-400 active:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Log in
            </button>
        </div>
    </form>
</div>
@endsection
