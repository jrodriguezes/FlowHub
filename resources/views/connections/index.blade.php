@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white tracking-tight">Conexiones</h1>
        <p class="text-sm text-gray-400 mt-1">Conecta tus aplicaciones favoritas para usarlas en tus automatizaciones.</p>
    </div>

    <!-- Mensajes de Éxito/Error -->
    @if(session('success'))
        <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-xl flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    @php
        $googleConnection = $connections->where('provider', 'google')->first();
        $githubConnection = $connections->where('provider', 'github')->first();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Google Card -->
        <div class="bg-[#111827] border border-white/5 rounded-2xl p-6 relative overflow-hidden group hover:border-indigo-500/30 transition-all duration-300 shadow-lg shadow-black/20">
            <!-- Glow Effect -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-red-500/10 rounded-full blur-3xl group-hover:bg-red-500/20 transition-all"></div>
            
            <div class="flex items-start justify-between relative z-10">
                <div class="flex items-center space-x-4">
                    <!-- Google Logo -->
                    <div class="w-12 h-12 bg-white/5 rounded-xl border border-white/10 flex items-center justify-center p-2.5">
                        <svg viewBox="0 0 24 24" class="w-full h-full"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Google</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Gmail & Calendar</p>
                    </div>
                </div>
                
                @if($googleConnection && $googleConnection->status->value === 'active')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-1.5 animate-pulse"></span>
                        Conectado
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-500/10 text-gray-400 border border-gray-500/20">
                        Desconectado
                    </span>
                @endif
            </div>

            <p class="mt-4 text-sm text-gray-400 line-clamp-2 relative z-10">
                Conecta tu cuenta de Google para enviar correos desde Gmail y administrar eventos en Google Calendar automáticamente.
            </p>

            <div class="mt-6 pt-5 border-t border-white/5 relative z-10 flex items-center justify-between">
                @if($googleConnection && $googleConnection->status->value === 'active')
                    <div class="text-xs text-gray-500">
                        Conectado el {{ $googleConnection->created_at->format('d M Y') }}
                    </div>
                    <form action="{{ route('connections.update', $googleConnection->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas desconectar tu cuenta de Google?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-medium rounded-lg transition-colors border border-red-500/20">
                            Desconectar
                        </button>
                    </form>
                @else
                    <div class="text-xs text-gray-500">
                        Requiere permisos OAuth
                    </div>
                    <a href="{{ route('google.redirect') }}" class="inline-flex items-center px-3 py-1.5 bg-white/10 hover:bg-white/15 text-white text-xs font-medium rounded-lg transition-colors border border-white/10">
                        Conectar ahora
                    </a>
                @endif
            </div>
        </div>

        <!-- GitHub Card (Placeholder for Rachel) -->
        <div class="bg-[#111827] border border-white/5 rounded-2xl p-6 relative overflow-hidden group hover:border-gray-500/30 transition-all duration-300 shadow-lg shadow-black/20">
            <!-- Glow Effect -->
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/5 rounded-full blur-3xl group-hover:bg-white/10 transition-all"></div>
            
            <div class="flex items-start justify-between relative z-10">
                <div class="flex items-center space-x-4">
                    <!-- GitHub Logo -->
                    <div class="w-12 h-12 bg-white/5 rounded-xl border border-white/10 flex items-center justify-center p-2.5 text-white">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">GitHub</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Webhooks & Issues</p>
                    </div>
                </div>
                
                @if($githubConnection && $githubConnection->status->value === 'active')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-1.5 animate-pulse"></span>
                        Conectado
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-500/10 text-gray-400 border border-gray-500/20">
                        Desconectado
                    </span>
                @endif
            </div>

            <p class="mt-4 text-sm text-gray-400 line-clamp-2 relative z-10">
                Conecta tu cuenta de GitHub para escuchar eventos en tus repositorios y automatizar flujos de trabajo.
            </p>

            <div class="mt-6 pt-5 border-t border-white/5 relative z-10 flex items-center justify-between">
                @if($githubConnection && $githubConnection->status->value === 'active')
                    <div class="text-xs text-gray-500">
                        Conectado el {{ $githubConnection->created_at->format('d M Y') }}
                    </div>
                    <form action="{{ route('connections.destroy', $githubConnection->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas desconectar tu cuenta de GitHub?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-medium rounded-lg transition-colors border border-red-500/20">
                            Desconectar
                        </button>
                    </form>
                @else
                    <div class="text-xs text-gray-500">
                        Requiere permisos OAuth
                    </div>
                    <a href="{{ route('github.redirect') }}" class="inline-flex items-center px-3 py-1.5 bg-white/10 hover:bg-white/15 text-white text-xs font-medium rounded-lg transition-colors border border-white/10">
                        Conectar ahora
                    </a>
                @endif
            </div>
        </div>
        
    </div>
@endsection
