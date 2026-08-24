@extends('layouts.app')

@section('content')
    <div x-data="automationManager('{{ route('automations.store') }}')">
        <!-- Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Automatizaciones</h1>
                <p class="text-sm text-gray-400 mt-1">Gestiona los flujos de trabajo y reglas automáticas.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button @click="openCreate()"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg shadow-sm shadow-indigo-500/20 transition-all focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#0B0F19] focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nueva Automatización
                </button>
            </div>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($automations ?? [] as $automation)
                <div
                    class="bg-[#111827] border border-white/5 rounded-xl p-5 hover:border-indigo-500/30 transition-colors group relative overflow-hidden">
                    <!-- Active Indicator -->
                    <div class="absolute top-0 right-0 p-4">
                        @if($automation->is_active)
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                Activo
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-500/10 text-gray-400 border border-gray-500/20">
                                Inactivo
                            </span>
                        @endif
                    </div>

                    <div class="mb-4 pr-16">
                        <h3 class="text-lg font-semibold text-gray-100 break-words pr-2">
                            <a href="{{ route('automations.show', $automation->id ?? 1) }}"
                                class="hover:text-indigo-400 focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                {{ $automation->name }}
                            </a>
                        </h3>
                        <p class="text-sm text-gray-400 mt-1 break-words">
                            {{ $automation->description ?? 'Sin descripción proporcionada para este flujo.' }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-white/5 mt-auto relative z-10">
                        <div class="text-xs text-gray-500">
                            Actualizado {{ ($automation->updated_at ?? now())->diffForHumans() }}
                        </div>
                        <div class="flex space-x-2">
                            <!-- Botón Activar/Desactivar -->
                            <form action="{{ route('automations.toggle', $automation) }}" method="POST" 
                            onsubmit="return confirm('¿Seguro que deseas {{ $automation->is_active ? 'desactivar' : 'activar' }} esta automatización?')" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="p-1.5 rounded-md transition-colors {{ $automation->is_active ? 'text-emerald-400 bg-white/5 hover:bg-emerald-500/10' : 'text-gray-400 bg-white/5 hover:bg-gray-500/10 hover:text-white' }}"
                                    title="{{ $automation->is_active ? 'Desactivar' : 'Activar' }}">
                                    @if($automation->is_active)
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                </button>
                            </form>
                            
                            <!-- Botón Editar -->
                            <button type="button"
                                @click="openEdit({{ Js::from($automation) }})"
                                class="p-1.5 text-gray-400 hover:text-indigo-400 bg-white/5 hover:bg-indigo-500/10 rounded-md transition-colors"
                                title="Editar">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <!-- Botón Eliminar -->
                            <form action="{{ route('automations.destroy', $automation->id) }}" method="POST"
                                class="inline-block"
                                onsubmit="return confirm('¿Seguro que deseas eliminar esta automatización permanentemente?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-1.5 text-gray-400 hover:text-red-400 bg-white/5 hover:bg-red-500/10 rounded-md transition-colors"
                                    title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full flex flex-col items-center justify-center p-12 bg-[#111827] border border-white/5 rounded-xl border-dashed">
                    <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mb-4 text-gray-400">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-1">No hay automatizaciones</h3>
                    <p class="text-sm text-gray-400 text-center max-w-sm mb-6">Comienza creando tu primera regla automática para
                        conectar tus servicios.</p>
                    <button @click="openCreate()"
                        class="inline-flex items-center px-4 py-2 bg-white/5 hover:bg-white/10 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Crear ahora
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Slide-over panel (Alpine.js) -->
        <div x-cloak x-show="showPanel" class="relative z-50" aria-labelledby="slide-over-title" role="dialog"
            aria-modal="true">
            <!-- Overlay -->
            <div x-show="showPanel" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">

                        <!-- Panel -->
                        <div x-show="showPanel" @click.away="showPanel = false"
                            x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                            x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                            x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                            class="pointer-events-auto w-screen max-w-2xl">

                            <div class="flex h-full flex-col bg-[#0B0F19] border-l border-white/10 shadow-2xl">

                                <!-- Cabecera del Panel -->
                                <div class="px-6 py-6 border-b border-white/5 flex items-center justify-between">
                                    <div>
                                        <h2 class="text-xl font-bold text-white tracking-tight" id="slide-over-title"
                                            x-text="isEdit ? 'Editar Automatización' : 'Crear Automatización'"></h2>
                                        <p class="text-sm text-gray-400 mt-1"
                                            x-text="isEdit ? 'Modifica los detalles de tu regla.' : 'Configura una nueva regla para tus servicios.'">
                                        </p>
                                    </div>
                                    <button type="button" @click="showPanel = false"
                                        class="rounded-md bg-transparent text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <span class="sr-only">Cerrar panel</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Formulario Principal -->
                                <form id="automation-form" :action="formAction" method="POST" @submit.prevent="submitForm"
                                    class="flex-1 overflow-y-auto flex flex-col">
                                    @csrf
                                    <template x-if="isEdit">
                                        <input type="hidden" name="_method" value="PUT">
                                    </template>

                                    <div class="flex-1 px-6 py-6 overflow-y-auto">
                                        <!-- Validation Errors -->
                                        <template x-if="errors.length > 0">
                                            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-6">
                                                <ul class="list-disc pl-5 space-y-1 text-sm">
                                                    <template x-for="error in errors">
                                                        <li x-text="error"></li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </template>

                                        @include('automations._form')
                                    </div>

                                    <!-- Botones de acción fijos abajo -->
                                    <div class="px-6 py-4 border-t border-white/5 bg-[#111827] flex justify-end space-x-3">
                                        <button type="button" @click="showPanel = false"
                                            class="px-4 py-2 bg-transparent border border-white/10 hover:bg-white/5 text-white text-sm font-medium rounded-lg transition-colors">
                                            Cancelar
                                        </button>
                                        <button type="submit"
                                            :disabled="isSubmitting"
                                            :class="{'opacity-50 cursor-not-allowed': isSubmitting}"
                                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg shadow-sm shadow-indigo-500/20 transition-all focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#111827] focus:ring-indigo-500">
                                            <span x-text="isSubmitting ? 'Guardando...' : (isEdit ? 'Guardar Cambios' : 'Crear Automatización')"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection