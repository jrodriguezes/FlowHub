<div class="space-y-8" x-data="automationForm({ triggerType: '{{ old('trigger.type', $automation->trigger->type ?? 'github_push') }}' })" @load-automation.window="loadData($event.detail)">
    <!-- Información General -->
    <div class="bg-[#111827] border border-white/5 rounded-xl p-6">
        <h2 class="text-lg font-medium text-white mb-4">Información General</h2>
        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-400">Nombre de la automatización <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $automation->name ?? '') }}" required
                    class="mt-1 block w-full bg-[#0B0F19] border border-white/10 rounded-lg shadow-sm py-2 px-3 text-gray-200 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-400">Descripción (Opcional)</label>
                <textarea name="description" id="description" rows="2"
                    class="mt-1 block w-full bg-[#0B0F19] border border-white/10 rounded-lg shadow-sm py-2 px-3 text-gray-200 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('description', $automation->description ?? '') }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $automation->is_active ?? true) ? 'checked' : '' }}
                    class="h-4 w-4 bg-[#0B0F19] border-white/10 rounded text-indigo-500 focus:ring-indigo-500 focus:ring-offset-[#111827]">
                <label for="is_active" class="ml-2 block text-sm text-gray-400">
                    Activar esta automatización inmediatamente
                </label>
            </div>
        </div>
    </div>

    <!-- Trigger (Disparador) -->
    <div class="bg-[#111827] border border-white/5 rounded-xl p-6">
        <h2 class="text-lg font-medium text-white mb-1">Disparador (Trigger) <span class="text-red-500">*</span></h2>
        <p class="text-sm text-gray-500 mb-4">El evento que iniciará este flujo.</p>
        
        <div>
            <select name="trigger[type]" x-model="triggerType" class="mt-1 block w-full bg-[#0B0F19] border border-white/10 rounded-lg shadow-sm py-2 px-3 text-gray-200 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">Selecciona un disparador...</option>
                <option value="github_issue">GitHub - Nuevo Issue (issues.opened)</option>
                <option value="schedule">Programado (Cron)</option>
            </select>
        </div>

        <div x-cloak x-show="triggerType === 'schedule'" x-transition class="mt-4 pt-4 border-t border-white/5 space-y-4">
            <div>
                <label for="cron_expression" class="block text-sm font-medium text-gray-400">Expresión Cron</label>
                <input type="text" name="trigger[cron_expression]" id="cron_expression" value="{{ old('trigger.cron_expression', $automation->trigger->cron_expression ?? '* * * * *') }}" placeholder="* * * * *"
                    class="mt-1 block w-full bg-[#0B0F19] border border-white/10 rounded-lg py-2 px-3 text-gray-200 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-mono">
                <div class="mt-2 text-xs text-gray-400 bg-white/5 rounded p-3 border border-white/5">
                    <p class="mb-2 font-medium text-gray-300">Formato cron estándar (Minuto, Hora, Día, Mes, Día de la semana)</p>
                    <ul class="space-y-1 text-gray-500">
                        <li><code class="text-indigo-400 bg-indigo-500/10 px-1 py-0.5 rounded">* * * * *</code> : Cada minuto (No recomendado para procesos pesados)</li>
                        <li><code class="text-indigo-400 bg-indigo-500/10 px-1 py-0.5 rounded">*/5 * * * *</code> : Cada 5 minutos</li>
                        <li><code class="text-indigo-400 bg-indigo-500/10 px-1 py-0.5 rounded">0 * * * *</code> : Cada hora (en el minuto 0 exacto)</li>
                        <li><code class="text-indigo-400 bg-indigo-500/10 px-1 py-0.5 rounded">0 9 * * *</code> : Todos los días a las 9:00 AM</li>
                        <li><code class="text-indigo-400 bg-indigo-500/10 px-1 py-0.5 rounded">0 0 * * 1</code> : Todos los Lunes a la medianoche</li>
                    </ul>
                    <a href="https://crontab.guru/" target="_blank" class="inline-block mt-2 text-indigo-400 hover:text-indigo-300 transition-colors">¿Necesitas ayuda? Crea tu cron aquí &rarr;</a>
                </div>
            </div>
            <div>
                <label for="timezone" class="block text-sm font-medium text-gray-400">Zona horaria</label>
                <select name="trigger[timezone]" id="timezone" class="mt-1 block w-full bg-[#0B0F19] border border-white/10 rounded-lg py-2 px-3 text-gray-200 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @php $tz = old('trigger.timezone', $automation->trigger->timezone ?? 'America/Costa_Rica'); @endphp
                    <option value="America/Costa_Rica" {{ $tz === 'America/Costa_Rica' ? 'selected' : '' }}>América/Costa Rica</option>
                    <option value="UTC" {{ $tz === 'UTC' ? 'selected' : '' }}>UTC (Tiempo Universal)</option>
                    <option value="America/Mexico_City" {{ $tz === 'America/Mexico_City' ? 'selected' : '' }}>América/Mexico City</option>
                    <option value="America/Bogota" {{ $tz === 'America/Bogota' ? 'selected' : '' }}>América/Bogotá</option>
                    <option value="America/Argentina/Buenos_Aires" {{ $tz === 'America/Argentina/Buenos_Aires' ? 'selected' : '' }}>América/Buenos Aires</option>
                    <option value="America/New_York" {{ $tz === 'America/New_York' ? 'selected' : '' }}>América/New York</option>
                    <option value="Europe/Madrid" {{ $tz === 'Europe/Madrid' ? 'selected' : '' }}>Europa/Madrid</option>
                </select>
                <p class="mt-2 text-xs text-gray-500">Selecciona tu zona horaria local.</p>
            </div>
        </div>
    </div>

    <!-- Condiciones -->
    <div x-show="triggerType === 'github_issue'" x-cloak class="bg-[#111827] border border-white/5 rounded-xl p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-lg font-medium text-white mb-1">Condiciones (Opcional)</h2>
                <p class="text-sm text-gray-500">Solo continuará si se cumplen estas reglas.</p>
            </div>
            <button type="button" @click="addCondition()" class="inline-flex items-center px-3 py-1.5 border border-white/10 text-xs font-medium rounded text-gray-300 bg-white/5 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#111827] focus:ring-indigo-500 transition-colors">
                + Agregar
            </button>
        </div>

        <div class="space-y-3">
            <template x-for="(condition, index) in conditions" :key="condition.id">
                <div class="flex items-center space-x-2 bg-[#0B0F19] p-2 rounded-lg border border-white/5">
                    <input type="text" list="available_fields" :name="`conditions[${index}][field]`" x-model="condition.field" placeholder="Selecciona o escribe el campo..." class="flex-1 min-w-0 bg-transparent border-0 py-1.5 px-3 text-sm text-gray-200 placeholder-gray-600 focus:ring-0">
                    <select :name="`conditions[${index}][operator]`" x-model="condition.operator" class="w-32 bg-[#111827] border border-white/10 rounded py-1.5 px-2 text-sm text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="equals">Igual a</option>
                        <option value="not_equals">Diferente de</option>
                        <option value="contains">Contiene</option>
                        <option value="exists">Existe</option>
                    </select>
                    <div class="flex-1 min-w-0" x-show="condition.operator !== 'exists'">
                        <input type="text" :name="`conditions[${index}][value]`" x-model="condition.value" :required="condition.operator !== 'exists'" placeholder="Valor (ej. urgente)" class="w-full bg-transparent border-0 py-1.5 px-3 text-sm text-gray-200 placeholder-gray-600 focus:ring-0">
                    </div>
                    <button type="button" @click="removeCondition(condition.id)" class="p-1.5 text-gray-500 hover:text-red-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </template>
            <div x-show="conditions.length === 0" class="text-sm text-gray-500 text-center py-4 border border-dashed border-white/10 rounded-lg">
                No hay condiciones. El flujo se ejecutará siempre.
            </div>

            <!-- Lista de sugerencias para los campos -->
            <datalist id="available_fields">
                <option value="trigger.issue.title">Título del Issue</option>
                <option value="trigger.issue.body">Cuerpo del Issue</option>
                <option value="trigger.issue.state">Estado (open/closed)</option>
                <option value="trigger.issue.user.login">Usuario que creó el issue</option>
                <option value="trigger.repository.name">Nombre del Repositorio</option>
                <option value="trigger.repository.full_name">Nombre completo del repo</option>
            </datalist>
        </div>
    </div>

    <!-- Acciones -->
    <div class="bg-[#111827] border border-white/5 rounded-xl p-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-lg font-medium text-white mb-1">Acciones <span class="text-red-500">*</span></h2>
                <p class="text-sm text-gray-500 mb-2">Lo que ocurrirá cuando se dispare la automatización.</p>
            </div>
            <button type="button" @click="addAction()" class="inline-flex items-center px-3 py-1.5 border border-white/10 text-xs font-medium rounded text-indigo-400 bg-indigo-500/10 hover:bg-indigo-500/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#111827] focus:ring-indigo-500 transition-colors">
                + Agregar Acción
            </button>
        </div>

        <div class="mb-4 text-xs text-gray-400 bg-indigo-500/5 rounded p-3 border border-indigo-500/10">
            <p class="mb-2 font-medium text-indigo-300">💡 Variables dinámicas (Interpolación)</p>
            <p class="mb-2 text-gray-400">Puedes insertar datos del evento origen (ej. GitHub) escribiendo las siguientes variables en los campos de texto:</p>
            <ul class="space-y-1 text-gray-500">
                <li><code class="text-indigo-400 bg-indigo-500/10 px-1 py-0.5 rounded">${trigger.issue.title}</code> : Título del issue original</li>
                <li><code class="text-indigo-400 bg-indigo-500/10 px-1 py-0.5 rounded">${trigger.issue.body}</code> : Descripción larga del issue</li>
                <li><code class="text-indigo-400 bg-indigo-500/10 px-1 py-0.5 rounded">${trigger.issue.state}</code> : Estado (open o closed)</li>
                <li><code class="text-indigo-400 bg-indigo-500/10 px-1 py-0.5 rounded">${trigger.issue.user.login}</code> : Usuario que creó el issue</li>
                <li><code class="text-indigo-400 bg-indigo-500/10 px-1 py-0.5 rounded">${trigger.repository.name}</code> : Nombre del repositorio</li>
            </ul>
        </div>

        <div class="space-y-3">
            <template x-for="(action, index) in actions" :key="action.id">
                <div class="flex items-start space-x-3 bg-[#0B0F19] p-4 rounded-lg border border-white/5">
                    <div class="mt-1 flex-shrink-0 w-6 h-6 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-xs text-gray-500" x-text="index + 1"></div>
                    <div class="flex-1 space-y-3">
                        <select :name="`actions[${index}][type]`" x-model="action.type" class="block w-full bg-[#111827] border border-white/10 rounded py-2 px-3 text-sm text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Selecciona una acción...</option>
                            <option value="github.create_issue">GitHub - Crear incidencia</option>
                            <option value="google.send_email">Google - Enviar Correo (Gmail)</option>
                            <option value="google.create_calendar_event">Google - Crear Evento (Calendar)</option>
                        </select>
                        
                        <div x-show="action.type !== ''" class="mt-2">
                            <label class="block text-xs font-medium text-gray-400 mb-1">Cuenta/Conexión a utilizar</label>
                            <select :name="`actions[${index}][service_connection_id]`" x-model="action.service_connection_id" class="block w-full bg-[#111827] border border-white/10 rounded py-2 px-3 text-sm text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">(Sin conexión / API Pública)</option>
                                @foreach($connections ?? [] as $conn)
                                    <option value="{{ $conn->id }}" x-show="action.type.startsWith('{{ $conn->provider }}.')" :disabled="!action.type.startsWith('{{ $conn->provider }}.')">
                                        {{ ucfirst($conn->provider) }} - {{ $conn->external_id ?? 'Cuenta conectada' }}
                                    </option>
                                @endforeach
                            </select>
                            <p x-show="action.type === 'github.create_issue'" class="text-[10px] text-gray-500 mt-1">Usa una conexión de GitHub activa.</p>
                        </div>

                        <div x-show="action.type === 'github.create_issue'" x-transition class="mt-2 bg-[#111827] border border-white/5 rounded p-3 space-y-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Repositorio (owner/repo)</label>
                                <input type="text" :name="`actions[${index}][config][repository]`" x-model="action.config.repository" placeholder="octocat/Hello-World" class="block w-full bg-[#0B0F19] border border-white/10 rounded py-1.5 px-2 text-sm text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Título</label>
                                <input type="text" :name="`actions[${index}][config][title]`" x-model="action.config.title" placeholder="Nuevo issue: {{ '${trigger.issue.title}' }}" class="block w-full bg-[#0B0F19] border border-white/10 rounded py-1.5 px-2 text-sm text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Descripción</label>
                                <textarea :name="`actions[${index}][config][body]`" x-model="action.config.body" rows="2" placeholder="Detalle del issue..." class="block w-full bg-[#0B0F19] border border-white/10 rounded py-1.5 px-2 text-sm text-gray-200 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>
                            <p class="text-[10px] text-gray-500">Puedes interpolar variables como {{ '${trigger.issue.title}' }}.</p>
                        </div>

                        <!-- Formulario para Gmail (google.send_email) -->
                        <div x-show="action.type === 'google.send_email'" x-transition class="mt-2 bg-[#111827] border border-white/5 rounded p-3 space-y-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Destinatario (Para)</label>
                                <input type="email" :name="`actions[${index}][config][to]`" x-model="action.config.to" placeholder="ejemplo@correo.com" class="block w-full bg-[#0B0F19] border border-white/10 rounded py-1.5 px-2 text-sm text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Asunto</label>
                                <input type="text" :name="`actions[${index}][config][subject]`" x-model="action.config.subject" placeholder="Nuevo reporte de {{ '${trigger.repository.name}' }}" class="block w-full bg-[#0B0F19] border border-white/10 rounded py-1.5 px-2 text-sm text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Cuerpo del correo</label>
                                <textarea :name="`actions[${index}][config][body]`" x-model="action.config.body" rows="3" placeholder="Escribe el mensaje..." class="block w-full bg-[#0B0F19] border border-white/10 rounded py-1.5 px-2 text-sm text-gray-200 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            </div>
                            <p class="text-[10px] text-gray-500">Puedes usar interpolación como {{ '${trigger.issue.body}' }} en todos los campos.</p>
                        </div>

                        <!-- Formulario para Google Calendar (google.create_calendar_event) -->
                        <div x-show="action.type === 'google.create_calendar_event'" x-transition class="mt-2 bg-[#111827] border border-white/5 rounded p-3 space-y-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Título del evento</label>
                                <input type="text" :name="`actions[${index}][config][summary]`" x-model="action.config.summary" placeholder="Reunión de emergencia" class="block w-full bg-[#0B0F19] border border-white/10 rounded py-1.5 px-2 text-sm text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">Fecha/Hora de Inicio</label>
                                    <div class="date-input-wrap relative">
                                        <input type="datetime-local" :name="`actions[${index}][config][start]`" x-model="action.config.start" class="date-input-dark block w-full bg-[#0B0F19] border border-white/10 rounded py-1.5 px-2 text-sm text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1">Fecha/Hora de Fin</label>
                                    <div class="date-input-wrap relative">
                                        <input type="datetime-local" :name="`actions[${index}][config][end]`" x-model="action.config.end" class="date-input-dark block w-full bg-[#0B0F19] border border-white/10 rounded py-1.5 px-2 text-sm text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Zona Horaria (Opcional)</label>
                                <select :name="`actions[${index}][config][timezone]`" x-model="action.config.timezone" class="block w-full bg-[#0B0F19] border border-white/10 rounded py-1.5 px-2 text-sm text-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">(Opcional) Usar zona predeterminada</option>
                                    <option value="America/Costa_Rica">América/Costa Rica</option>
                                    <option value="UTC">UTC (Tiempo Universal)</option>
                                    <option value="America/Mexico_City">América/Mexico City</option>
                                    <option value="America/Bogota">América/Bogotá</option>
                                    <option value="America/Argentina/Buenos_Aires">América/Buenos Aires</option>
                                    <option value="America/New_York">América/New York</option>
                                    <option value="Europe/Madrid">Europa/Madrid</option>
                                </select>
                            </div>
                        </div>
                        
                        <div x-show="action.type !== '' && action.type !== 'github.create_issue' && action.type !== 'google.send_email' && action.type !== 'google.create_calendar_event'" x-transition class="mt-2 bg-[#111827] border border-white/5 rounded p-3">
                            <label class="block text-xs font-medium text-gray-400 mb-1">Configuración / Mensaje</label>
                            <textarea :name="`actions[${index}][config][message]`" x-model="action.config.message" rows="2" placeholder="Ej: Hubo un nuevo push en el repositorio..." class="block w-full bg-[#0B0F19] border border-white/10 rounded py-1.5 px-2 text-sm text-gray-200 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                            <p class="text-[10px] text-gray-500 mt-1">Puedes usar variables como {{ '${trigger.branch}' }}</p>
                        </div>
                    </div>
                    <button type="button" @click="removeAction(action.id)" class="mt-1 p-1.5 text-gray-500 hover:text-red-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </template>
            <div x-show="actions.length === 0" class="text-sm text-gray-500 text-center py-4 border border-dashed border-red-500/30 rounded-lg">
                Debes agregar al menos una acción.
            </div>
        </div>
    </div>
</div>

