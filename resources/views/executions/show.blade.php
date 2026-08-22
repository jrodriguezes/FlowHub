@extends('layouts.app')

@section('content')
    <div>
        <!-- Header con botón de regreso -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('executions.index') }}" class="p-2 rounded-lg bg-white/5 text-gray-400 hover:text-white hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">Detalle de Ejecución #{{ $execution->id }}</h1>
                    <p class="text-sm text-gray-400 mt-1">Automatización: <span class="text-gray-300 font-medium">{{ $execution->automation->name ?? 'N/A' }}</span></p>
                </div>
            </div>
            
            <div class="mt-4 sm:mt-0">
                @php
                    $statusClass = match($execution->status->value ?? $execution->status) {
                        'successful' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                        'failed' => 'bg-red-500/10 text-red-400 border-red-500/20',
                        'processing' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                        'pending' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                        default => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
                    };
                    $statusLabel = match($execution->status->value ?? $execution->status) {
                        'successful' => 'Completado',
                        'failed' => 'Fallido',
                        'processing' => 'Procesando...',
                        'pending' => 'Pendiente',
                        default => 'Desconocido',
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $statusClass }}">
                    Estado Global: {{ $statusLabel }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Columna Principal: Pasos -->
            <div class="lg:col-span-2 space-y-6">
                <h2 class="text-lg font-semibold text-white">Pasos Ejecutados</h2>
                
                <div class="bg-[#111827] border border-white/5 rounded-xl overflow-hidden shadow-sm p-6">
                    <ul role="list" class="-mb-8">
                        @forelse($execution->steps ?? [] as $index => $step)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-white/10" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-4">
                                        <div>
                                            @php
                                                $stepColor = match($step->status->value ?? $step->status) {
                                                    'successful' => 'bg-emerald-500',
                                                    'failed' => 'bg-red-500',
                                                    'processing' => 'bg-blue-500',
                                                    'pending' => 'bg-amber-500',
                                                    default => 'bg-gray-500',
                                                };
                                            @endphp
                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-4 ring-[#111827] {{ $stepColor }} bg-opacity-20">
                                                @if(($step->status->value ?? $step->status) === 'successful')
                                                    <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                @elseif(($step->status->value ?? $step->status) === 'failed')
                                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                    </svg>
                                                @elseif(($step->status->value ?? $step->status) === 'processing')
                                                    <svg class="h-5 w-5 text-blue-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                @else
                                                    <span class="text-xs font-medium text-amber-400">{{ $index + 1 }}</span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0 flex flex-col pt-1.5 space-y-2">
                                            <div class="text-sm text-gray-300">
                                                Acción: <span class="font-medium text-white">{{ $step->action['type'] ?? 'Desconocida' }}</span>
                                            </div>
                                            


                                            <!-- Detalles Exitosos -->
                                            @if(($step->status->value ?? $step->status) === 'successful' && $step->result_data)
                                                <div class="mt-2 bg-white/5 border border-white/10 rounded p-3">
                                                    <pre class="text-[10px] text-gray-400 overflow-x-auto">@json($step->result_data, JSON_PRETTY_PRINT)</pre>
                                                </div>
                                            @endif
                                            
                                            <div class="text-[10px] text-gray-500">
                                                @if($step->completed_at)
                                                    Completado el {{ \Carbon\Carbon::parse($step->completed_at)->format('d/m/Y H:i:s') }}
                                                @else
                                                    Añadido a la cola el {{ $step->created_at->format('d/m/Y H:i:s') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <p class="text-sm text-gray-400">No hay pasos registrados para esta ejecución.</p>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Columna Lateral: Payload del Webhook -->
            <div class="space-y-6">
                <h2 class="text-lg font-semibold text-white">Datos de Entrada (Payload)</h2>
                <div class="bg-[#111827] border border-white/5 rounded-xl shadow-sm p-4">
                    @if($execution->payload)
                        <pre class="text-xs text-indigo-300 font-mono overflow-x-auto">@json($execution->payload, JSON_PRETTY_PRINT)</pre>
                    @else
                        <p class="text-sm text-gray-400">No hay payload registrado para esta ejecución.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
