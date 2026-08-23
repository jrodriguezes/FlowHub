@extends('layouts.app')

@section('content')
    <div>
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('executions.index') }}" class="p-2 rounded-lg bg-white/5 text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">Detalle de ejecución #{{ $execution->id }}</h1>
                    <p class="text-sm text-gray-400 mt-1">Automatización: <span class="text-gray-300 font-medium">{{ $execution->automation->name ?? 'N/A' }}</span></p>
                </div>
            </div>
            <div>@include('executions.partials.status-badge', ['status' => $execution->status])</div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <h2 class="text-lg font-semibold text-white">Pasos ejecutados</h2>
                <div class="bg-[#111827] border border-white/5 rounded-xl p-6">
                    <ul role="list" class="-mb-8">
                        @forelse($execution->steps as $index => $step)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-white/10"></span>
                                    @endif
                                    <div class="relative flex space-x-4">
                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-4 ring-[#111827] bg-white/5 text-xs text-gray-300">{{ $index + 1 }}</span>
                                        <div class="flex-1 space-y-2">
                                            <div class="flex items-center justify-between gap-3">
                                                <p class="text-sm text-white">Acción: <span class="font-medium">{{ $step->action->type ?? 'Desconocida' }}</span></p>
                                                @include('executions.partials.status-badge', ['status' => $step->status])
                                            </div>
                                            <p class="text-xs text-gray-500">Intentos: {{ $step->attempts }} · Posición: {{ $step->position }}</p>
                                            @if($step->output_payload)
                                                <div class="bg-white/5 border border-white/10 rounded p-3">
                                                    <p class="text-[10px] uppercase tracking-wide text-gray-500 mb-1">Salida</p>
                                                    <pre class="text-[10px] text-gray-400 overflow-x-auto">@json($step->output_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</pre>
                                                </div>
                                            @endif
                                            @if($step->error_details)
                                                <div class="bg-red-500/10 border border-red-500/20 rounded p-3">
                                                    <p class="text-[10px] uppercase tracking-wide text-red-300 mb-1">Error</p>
                                                    <p class="text-sm text-red-200">{{ $step->error_details['message'] ?? 'Error desconocido' }}</p>
                                                </div>
                                            @endif
                                            <p class="text-[10px] text-gray-500">
                                                @if($step->completed_at)
                                                    Completado: {{ $step->completed_at->format('d/m/Y H:i:s') }}
                                                @elseif($step->started_at)
                                                    Iniciado: {{ $step->started_at->format('d/m/Y H:i:s') }}
                                                @else
                                                    Creado: {{ $step->created_at->format('d/m/Y H:i:s') }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-gray-400">No hay pasos registrados.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-[#111827] border border-white/5 rounded-xl p-4 space-y-3">
                    <h2 class="text-lg font-semibold text-white">Resumen</h2>
                    <dl class="text-sm space-y-2">
                        <div><dt class="text-gray-500">Clave de evento</dt><dd class="text-gray-300 font-mono text-xs break-all">{{ $execution->event_key ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Inicio</dt><dd class="text-gray-300">{{ ($execution->started_at ?? $execution->created_at)?->format('d/m/Y H:i:s') }}</dd></div>
                        <div><dt class="text-gray-500">Fin</dt><dd class="text-gray-300">{{ $execution->completed_at?->format('d/m/Y H:i:s') ?? '—' }}</dd></div>
                    </dl>
                </div>

                <div class="bg-[#111827] border border-white/5 rounded-xl p-4">
                    <h2 class="text-lg font-semibold text-white mb-3">Datos de entrada</h2>
                    @if($sanitizedInput)
                        <pre class="text-xs text-indigo-300 font-mono overflow-x-auto">@json($sanitizedInput, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</pre>
                    @else
                        <p class="text-sm text-gray-400">Sin payload registrado.</p>
                    @endif
                </div>

                @if($execution->error_details)
                    <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4">
                        <h2 class="text-lg font-semibold text-red-200 mb-2">Error de ejecución</h2>
                        <p class="text-sm text-red-100">{{ $execution->error_details['message'] ?? 'Error desconocido' }}</p>
                    </div>
                @endif

                @if($sanitizedOutput)
                    <div class="bg-[#111827] border border-white/5 rounded-xl p-4">
                        <h2 class="text-lg font-semibold text-white mb-3">Resultado</h2>
                        <pre class="text-xs text-emerald-300 font-mono overflow-x-auto">@json($sanitizedOutput, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</pre>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
