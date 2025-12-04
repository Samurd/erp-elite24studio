<div class="mt-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Notificaciones y Recordatorios</h3>
                <p class="text-sm text-gray-500 mt-1">Gestiona los recordatorios y notificaciones automáticas</p>
            </div>
            <button wire:click="openModal" type="button"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nueva Notificación
            </button>
        </div>

        <!-- Templates List -->
        <div class="p-6">
            @if($templates->count() > 0)
                <div class="space-y-3">
                    @foreach($templates as $template)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        @if($template->is_active)
                                            <div class="w-3 h-3 bg-green-500 rounded-full" title="Activo"></div>
                                        @else
                                            <div class="w-3 h-3 bg-gray-400 rounded-full" title="Pausado"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $template->title }}</h4>
                                            @php
                                                $typeColors = [
                                                    'recurring' => 'bg-purple-100 text-purple-800',
                                                    'reminder' => 'bg-blue-100 text-blue-800',
                                                    'scheduled' => 'bg-yellow-100 text-yellow-800',
                                                    'now' => 'bg-green-100 text-green-800',
                                                ];
                                                $typeLabels = [
                                                    'recurring' => 'Recurrente',
                                                    'reminder' => 'Recordatorio',
                                                    'scheduled' => 'Programada',
                                                    'now' => 'Inmediata',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $typeColors[$template->type] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $typeLabels[$template->type] ?? ucfirst($template->type) }}
                                            </span>
                                            @if($template->type === 'recurring' && optional($template->notifiable)->frequency)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $template->notifiable->frequency->name }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $template->message }}</p>
                                        
                                        <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                            @if($template->type === 'reminder')
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $template->reminder_days }} días antes
                                                </span>
                                            @endif
                                            
                                            @if($template->send_email)
                                                <span class="flex items-center text-blue-600">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Email {{ isset($template->data['custom_email']) ? '('.$template->data['custom_email'].')' : '' }}
                                                </span>
                                            @endif

                                            @if($template->next_send_at)
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Próximo envío: {{ \Carbon\Carbon::parse($template->next_send_at)->format('d/m/Y H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2 ml-4">
                                @if($template->type === 'recurring')
                                    <button wire:click="toggle({{ $template->id }})" type="button"
                                        class="p-2 rounded-lg transition {{ $template->is_active ? 'text-yellow-600 hover:bg-yellow-50' : 'text-green-600 hover:bg-green-50' }}"
                                        title="{{ $template->is_active ? 'Pausar' : 'Reanudar' }}">
                                        @if($template->is_active)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @endif
                                    </button>
                                @endif
                                <button wire:click="delete({{ $template->id }})" wire:confirm="¿Estás seguro de eliminar esta notificación?" type="button"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Eliminar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="mt-4 text-gray-500">No hay notificaciones configuradas</p>
                    <p class="text-sm text-gray-400 mt-1">Crea recordatorios o notificaciones automáticas</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Configurar Notificación
                                </h3>
                                
                                <div class="mt-4 space-y-4">
                                    <!-- Type Selector -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tipo de Notificación</label>
                                        <select wire:model.live="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            @if(empty($allowedTypes) || in_array('now', $allowedTypes))
                                                <option value="now">⚡ Enviar Ahora (Inmediata)</option>
                                            @endif
                                            @if(empty($allowedTypes) || in_array('reminder', $allowedTypes))
                                                <option value="reminder">📅 Recordatorio (Antes de fecha)</option>
                                            @endif
                                            @if(empty($allowedTypes) || in_array('scheduled', $allowedTypes))
                                                <option value="scheduled">🕒 Programada (Fecha específica)</option>
                                            @endif
                                            @if(empty($allowedTypes) || in_array('recurring', $allowedTypes))
                                                <option value="recurring">🔄 Recurrente (Periódica)</option>
                                            @endif
                                        </select>
                                    </div>

                                    <!-- Common Fields -->
                                    <!-- Title and Message are now handled automatically by the model -->

                                    <!-- Type Specific Fields -->
                                    @if($type === 'scheduled')
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Fecha</label>
                                                <input type="date" wire:model="scheduledDate" min="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                @error('scheduledDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Hora</label>
                                                <input type="time" wire:model="scheduledTime" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                @error('scheduledTime') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    @elseif($type === 'recurring')
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Frecuencia</label>
                                            <select wire:model="recurringInterval" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                @foreach($allowedFrequencies as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @elseif($type === 'reminder')
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Días antes del evento</label>
                                            <input type="number" wire:model="reminderDays" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <p class="mt-1 text-xs text-gray-500">Se enviará X días antes de la fecha del evento.</p>
                                        </div>
                                    @endif

                                    <!-- Email Option -->
                                    <div class="border-t pt-4 mt-4">
                                        <div class="flex items-center">
                                            <input type="checkbox" wire:model.live="sendEmail" id="sendEmail" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="sendEmail" class="ml-2 block text-sm text-gray-900">Enviar también por correo electrónico</label>
                                        </div>

                                        @if($sendEmail)
                                            <div class="mt-3 pl-6">
                                                <label class="block text-sm font-medium text-gray-700">Email personalizado (Opcional)</label>
                                                <input type="email" wire:model="customEmail" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="cliente@ejemplo.com">
                                                <p class="mt-1 text-xs text-gray-500">Si se deja vacío, se enviará al email del usuario asociado.</p>
                                                @error('customEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @error('general') 
                                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                                            <span class="block sm:inline">{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="save" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ $type === 'now' ? 'Enviar Notificación' : 'Guardar Configuración' }}
                        </button>
                        <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
