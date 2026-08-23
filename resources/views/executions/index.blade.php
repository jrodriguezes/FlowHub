@extends('layouts.app')

@section('content')
    <div>
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Historial de Ejecuciones</h1>
                <p class="text-sm text-gray-400 mt-1">Consulta el estado, fechas, entradas y resultados de tus automatizaciones.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('executions.index') }}" class="mb-6 bg-[#111827] border border-white/5 rounded-xl p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label for="status" class="block text-xs font-medium text-gray-400 mb-1">Estado</label>
                <select name="status" id="status" class="w-full bg-[#0B0F19] border border-white/10 rounded-lg py-2 px-3 text-sm text-gray-200">
                    <option value="">Todos</option>
                    @foreach(['pending' => 'Pendiente', 'processing' => 'Procesando', 'successful' => 'Exitoso', 'failed' => 'Fallido', 'skipped' => 'Omitido'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="automation_id" class="block text-xs font-medium text-gray-400 mb-1">Automatización</label>
                <select name="automation_id" id="automation_id" class="w-full bg-[#0B0F19] border border-white/10 rounded-lg py-2 px-3 text-sm text-gray-200">
                    <option value="">Todas</option>
                    @foreach($automations as $automation)
                        <option value="{{ $automation->id }}" @selected((string) request('automation_id') === (string) $automation->id)>{{ $automation->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="from" class="block text-xs font-medium text-gray-400 mb-1">Desde</label>
                <input type="date" name="from" id="from" value="{{ request('from') }}" class="w-full bg-[#0B0F19] border border-white/10 rounded-lg py-2 px-3 text-sm text-gray-200">
            </div>
            <div>
                <label for="to" class="block text-xs font-medium text-gray-400 mb-1">Hasta</label>
                <input type="date" name="to" id="to" value="{{ request('to') }}" class="w-full bg-[#0B0F19] border border-white/10 rounded-lg py-2 px-3 text-sm text-gray-200">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-lg text-sm hover:bg-indigo-500/30 transition-colors">Filtrar</button>
                <a href="{{ route('executions.index') }}" class="px-4 py-2 text-sm text-gray-400 hover:text-white transition-colors">Limpiar</a>
            </div>
        </form>

        <div class="bg-[#111827] border border-white/5 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/5">
                    <thead class="bg-[#0B0F19]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Automatización</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Inicio</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Fin</th>
                            <th class="px-6 py-4 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 bg-[#111827]">
                        @forelse($executions as $execution)
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-mono">#{{ $execution->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">{{ $execution->automation->name ?? 'Automatización eliminada' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">@include('executions.partials.status-badge', ['status' => $execution->status])</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ ($execution->started_at ?? $execution->created_at)?->format('d/m/Y H:i:s') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ $execution->completed_at?->format('d/m/Y H:i:s') ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('executions.show', $execution) }}" class="text-indigo-400 hover:text-indigo-300 transition-colors">Ver detalle</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">No hay ejecuciones con los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($executions->hasPages())
                <div class="px-6 py-4 border-t border-white/5 bg-[#0B0F19]">{{ $executions->links() }}</div>
            @endif
        </div>
    </div>
@endsection
