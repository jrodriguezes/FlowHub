@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('automations.index') }}" class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-indigo-400 transition-colors mb-4">
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver a Automatizaciones
        </a>
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white tracking-tight">{{ $automation->name }}</h1>
                <p class="mt-2 text-gray-400">{{ $automation->description ?? 'Sin descripción' }}</p>
            </div>
            <div class="ml-4 flex-shrink-0">
                @if($automation->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500/10 text-green-400 border border-green-500/20 shadow-[0_0_10px_rgba(34,197,94,0.1)]">
                        <span class="w-2 h-2 mr-2 bg-green-500 rounded-full animate-pulse"></span>
                        Activo
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-500/10 text-gray-400 border border-gray-500/20">
                        Inactivo
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Visual Flow Builder Representation -->
    <div class="relative mt-12 bg-[#0B0F19] rounded-2xl border border-white/5 p-8 shadow-2xl overflow-hidden">
        <!-- Fondo decorativo -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-indigo-500/10 blur-[120px] rounded-full pointer-events-none"></div>

        <div class="relative z-10 space-y-0 max-w-2xl mx-auto">
            
            <!-- 1. Trigger -->
            <div class="flex flex-col items-center">
                <div class="w-full bg-[#111827] border border-white/10 rounded-xl p-6 shadow-lg hover:border-indigo-500/50 transition-colors group">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-indigo-500/10 rounded-lg flex items-center justify-center border border-indigo-500/20 text-indigo-400 group-hover:bg-indigo-500/20 transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider">Disparador</h3>
                            <p class="text-lg font-medium text-white mt-0.5">{{ $automation->trigger->type ?? 'No definido' }}</p>
                            @if($automation->trigger?->cron_expression)
                                <p class="text-sm text-gray-500 mt-1 font-mono bg-white/5 px-2 py-0.5 rounded inline-block">{{ $automation->trigger->cron_expression }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flecha conectora -->
            <div class="flex justify-center py-4">
                <div class="w-0.5 h-8 bg-gradient-to-b from-indigo-500/50 to-white/10"></div>
            </div>

            <!-- 2. Conditions -->
            @if($automation->conditions->isNotEmpty())
            <div class="flex flex-col items-center">
                <div class="w-full bg-[#111827] border border-white/10 rounded-xl p-6 shadow-lg hover:border-purple-500/50 transition-colors group relative overflow-hidden">
                    <div class="absolute left-0 top-0 w-1 h-full bg-purple-500/50"></div>
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-purple-500/10 rounded-lg flex items-center justify-center border border-purple-500/20 text-purple-400 group-hover:bg-purple-500/20 transition-colors flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-3">Condiciones ({{ $automation->conditions->count() }})</h3>
                            <div class="space-y-2">
                                @foreach($automation->conditions as $condition)
                                <div class="bg-[#0B0F19] rounded p-2 text-sm text-gray-300 flex items-center space-x-2 border border-white/5">
                                    <span class="font-mono text-indigo-400">{{ $condition->field }}</span>
                                    <span class="text-gray-500 italic">{{ $condition->operator }}</span>
                                    <span class="font-mono text-green-400">{{ $condition->value }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flecha conectora -->
            <div class="flex justify-center py-4">
                <div class="w-0.5 h-8 bg-gradient-to-b from-white/10 to-emerald-500/50"></div>
            </div>
            @endif

            <!-- 3. Actions -->
            <div class="flex flex-col items-center">
                <div class="w-full bg-[#111827] border border-white/10 rounded-xl p-6 shadow-lg hover:border-emerald-500/50 transition-colors group relative overflow-hidden">
                    <div class="absolute left-0 top-0 w-1 h-full bg-emerald-500/50"></div>
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-emerald-500/10 rounded-lg flex items-center justify-center border border-emerald-500/20 text-emerald-400 group-hover:bg-emerald-500/20 transition-colors flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-gray-400 uppercase tracking-wider mb-3">Acciones a ejecutar ({{ $automation->actions->count() }})</h3>
                            <div class="space-y-3">
                                @foreach($automation->actions as $action)
                                <div class="bg-[#0B0F19] rounded p-3 text-sm text-gray-300 border border-white/5 relative">
                                    <div class="absolute -left-3 top-3 w-6 h-6 bg-[#111827] border border-white/10 rounded-full flex items-center justify-center text-xs text-gray-500">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div class="pl-4">
                                        <div class="font-medium text-white">{{ $action->type }}</div>
                                        @if($action->config)
                                            <div class="mt-2 text-xs text-gray-400 font-mono bg-white/5 p-2 rounded overflow-x-auto">
                                                {{ json_encode($action->config, JSON_PRETTY_PRINT) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
