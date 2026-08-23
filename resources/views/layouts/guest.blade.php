<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FlowHub') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#0B0F19] text-gray-200 selection:bg-indigo-500/30">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-10 sm:pt-0 p-4">
        
        <div class="mb-6">
            <a href="{{ url('/') }}" class="text-4xl font-extrabold text-indigo-400 tracking-tight hover:text-indigo-300 transition-colors">
                Flow<span class="text-white">Hub</span>
            </a>
        </div>

        <div class="w-full sm:max-w-md px-8 py-10 bg-[#111827] border border-white/10 shadow-2xl overflow-hidden rounded-2xl relative">
            
            <!-- Decorational glow effect -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
            
            @yield('content')
        </div>
        
        <!-- Footer text -->
        <div class="mt-8 text-sm text-gray-500">
            &copy; {{ date('Y') }} FlowHub. All rights reserved.
        </div>
    </div>
</body>
</html>
