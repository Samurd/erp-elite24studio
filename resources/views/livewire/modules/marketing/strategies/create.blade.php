<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ isset($strategy) ? 'Editar Estrategia' : 'Nueva Estrategia' }}
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ isset($strategy) ? 'Actualiza la información de la estrategia' : 'Complete los datos para registrar una nueva estrategia de marketing' }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('marketing.strategies.index') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Nombre -->
                <div class="lg:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Estrategia *</label>
                    <input type="text" id="name" wire:model="form.name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                           placeholder="Ej: Campaña de Verano 2024">
                    @error('form.name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="status_id" wire:model="form.status_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar estado</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('form.status_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Objetivo -->
            <div class="grid grid-cols-1 gap-6 mt-6">
                <div>
                    <label for="objective" class="block text-sm font-medium text-gray-700 mb-2">Objetivo Principal</label>
                    <textarea id="objective" wire:model="form.objective" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                              placeholder="Describir el objetivo principal de la estrategia..."></textarea>
                    @error('form.objective')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Fechas y Responsable -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <!-- Fecha de Inicio -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Inicio</label>
                    <input type="date" id="start_date" wire:model="form.start_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.start_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fecha de Fin -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Fin</label>
                    <input type="date" id="end_date" wire:model="form.end_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.end_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Responsable -->
                <div>
                    <label for="responsible_id" class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                    <select id="responsible_id" wire:model="form.responsible_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar responsable</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('form.responsible_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Público Objetivo y Plataformas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Público Objetivo -->
                <div>
                    <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-2">Público Objetivo</label>
                    <input type="text" id="target_audience" wire:model="form.target_audience"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                           placeholder="Ej: Jóvenes 18-25 años, interés en tecnología">
                    @error('form.target_audience')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Plataformas -->
                <div>
                    <label for="platforms" class="block text-sm font-medium text-gray-700 mb-2">Plataformas</label>
                    <input type="text" id="platforms" wire:model="form.platforms"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                           placeholder="Ej: Facebook, Instagram, LinkedIn, TikTok">
                    @error('form.platforms')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Opciones Adicionales -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Notificar Equipo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Opciones Adicionales</label>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="form.notify_team"
                                   class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Notificar al equipo sobre esta estrategia</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="form.add_to_calendar"
                                   class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Agregar al calendario del equipo</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Observaciones -->
            <div class="grid grid-cols-1 gap-6 mt-6">
                <div>
                    <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea id="observations" wire:model="form.observations" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                              placeholder="Notas adicionales, comentarios importantes, etc..."></textarea>
                    @error('form.observations')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <a href="{{ route('marketing.strategies.index') }}"
                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
                <button type="submit"
                        class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ isset($strategy) ? 'Actualizar' : 'Guardar' }} Estrategia
                </button>
            </div>
        </form>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
</div>
