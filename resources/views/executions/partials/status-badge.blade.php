@php
    $statusValue = $status->value ?? $status;
    $statusClass = match($statusValue) {
        'successful' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
        'failed' => 'bg-red-500/10 text-red-400 border-red-500/20',
        'processing' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
        'pending' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
        'skipped' => 'bg-purple-500/10 text-purple-300 border-purple-500/20',
        default => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
    };
    $statusLabel = match($statusValue) {
        'successful' => 'Exitoso',
        'failed' => 'Fallido',
        'processing' => 'Procesando',
        'pending' => 'Pendiente',
        'skipped' => 'Omitido',
        default => 'Desconocido',
    };
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClass }}">{{ $statusLabel }}</span>
