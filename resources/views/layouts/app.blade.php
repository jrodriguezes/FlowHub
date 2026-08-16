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
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        @auth
        <aside class="w-64 bg-[#111827] border-r border-white/5 flex flex-col transition-all duration-300">
            <div class="h-16 flex items-center px-6 border-b border-white/5">
                <a href="{{ url('/home') }}" class="text-xl font-bold text-indigo-400 tracking-tight">
                    Flow<span class="text-white">Hub</span>
                </a>
            </div>
            
            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
                
                <a href="{{ route('automations.index') }}" class="{{ request()->routeIs('automations.*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors mt-2">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    Automatizaciones
                </a>

                <a href="{{ route('connections.index') }}" class="{{ request()->routeIs('connections.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors mt-2">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                    </svg>
                    Conexiones
                </a>
                
                <a href="{{ route('executions.index') }}" class="{{ request()->routeIs('executions.*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors mt-2">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Historial
                </a>
                
                <a href="{{ route('profile.index') }}" class="{{ request()->routeIs('profile.*') ? 'bg-indigo-500/10 text-indigo-400' : 'text-gray-400 hover:bg-white/5 hover:text-gray-200' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors mt-2">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Mi Perfil
                </a>
            </nav>
            
            <div class="p-4 border-t border-white/5">
                <div class="flex items-center px-2 py-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-200 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-1.5 text-gray-400 hover:text-white rounded-md hover:bg-white/5 transition-colors" title="Log Out">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        @endauth

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            @guest
            <nav class="bg-[#111827] border-b border-white/5 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <a href="{{ url('/') }}" class="text-xl font-bold text-indigo-400 tracking-tight">
                                Flow<span class="text-white">Hub</span>
                            </a>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-white transition">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-sm text-gray-400 hover:text-white transition">Register</a>
                            @endif
                        </div>
                    </div>
                </div>
            </nav>
            @endguest

            <main class="flex-1 overflow-y-auto bg-[#0B0F19] p-6 lg:p-10">
                <div class="max-w-5xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
