@extends('layouts.app')

@section('content')
    <div>
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Historial de Ejecuciones</h1>
                <p class="text-sm text-gray-400 mt-1">Monitorea el estado de tus automatizaciones en tiempo real.</p>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-[#111827] border border-white/5 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/5">
                    <thead class="bg-[#0B0F19]">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                ID Ejecución
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Automatización
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Estado
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 bg-[#111827]">
                        @forelse($executions as $execution)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-mono">
                                    #{{ $execution->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-white">
                                        {{ $execution->automation->name ?? 'Automatización Eliminada' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $execution->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('executions.show', $execution->id) }}" class="text-indigo-400 hover:text-indigo-300 mr-3 transition-colors">
                                        Ver Detalles
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-white mb-1">Sin ejecuciones</h3>
                                    <p class="text-sm text-gray-400">Las automatizaciones que se disparen aparecerán aquí.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($executions, 'links') && $executions->hasPages())
                <div class="px-6 py-4 border-t border-white/5 bg-[#0B0F19]">
                    {{ $executions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
