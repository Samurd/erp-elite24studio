<div>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit.prevent="save">

            <!-- Configuración de la Notificación -->
            <div class="mb-8 border-b pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración de la Notificación</h3>

                <!-- Tipo -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Notificación</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Reminder -->
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="type" value="reminder" class="peer sr-only">
                            <div
                                class="p-4 border rounded-lg hover:bg-gray-50 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-all">
                                <div class="font-medium text-gray-900">Recordatorio</div>
                                <div class="text-xs text-gray-500">Días antes de una fecha</div>
                            </div>
                        </label>
                        <!-- Scheduled -->
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="type" value="scheduled" class="peer sr-only">
                            <div
                                class="p-4 border rounded-lg hover:bg-gray-50 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-all">
                                <div class="font-medium text-gray-900">Programada</div>
                                <div class="text-xs text-gray-500">Fecha y hora específica</div>
                            </div>
                        </label>
                        <!-- Recurring -->
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="type" value="recurring" class="peer sr-only">
                            <div
                                class="p-4 border rounded-lg hover:bg-gray-50 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-all">
                                <div class="font-medium text-gray-900">Recurrente</div>
                                <div class="text-xs text-gray-500">Se repite periódicamente</div>
                            </div>
                        </label>
                    </div>
                    @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Campos dinámicos según tipo -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    @if($type === 'reminder')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha del Evento</label>
                            <input type="datetime-local" wire:model="event_date"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                            <p class="text-xs text-gray-500 mt-1">Fecha base para el recordatorio.</p>
                            @error('event_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Días de anticipación</label>
                            <input type="number" wire:model="reminder_days" min="1"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                            <p class="text-xs text-gray-500 mt-1">Días antes de la fecha del evento.</p>
                            @error('reminder_days') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    @if($type === 'scheduled')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha y Hora</label>
                            <input type="datetime-local" wire:model="scheduled_at"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                            @error('scheduled_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    @if($type === 'recurring')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Frecuencia</label>
                            <select wire:model="selected_frequency"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                                @foreach($frequencies as $key => $config)
                                    <option value="{{ $key }}">{{ $config['label'] }}</option>
                                @endforeach
                            </select>
                            @error('selected_frequency') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                <!-- Campos Comunes -->
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Destinatario</label>
                        <select wire:model="user_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                            @foreach($this->users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                        <input type="text" wire:model="title"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                        @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mensaje</label>
                        <textarea wire:model="message" rows="3"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500"></textarea>
                        @error('message') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Las notificaciones se enviarán automáticamente por correo electrónico al
                                    destinatario seleccionado.
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($template)
                        <div class="flex items-center mt-4">
                            <input type="checkbox" wire:model="is_active" id="is_active"
                                class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">Notificación Activa</label>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('notifications.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    {{ $template ? 'Actualizar' : 'Crear' }} Notificación
                </button>
            </div>
        </form>
    </div>
</div>