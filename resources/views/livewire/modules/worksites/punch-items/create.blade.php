<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ isset($punchItem) ? 'Editar Punch Item' : 'Nuevo Punch Item' }}
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ isset($punchItem) ? 'Actualiza la información del Punch Item' : 'Complete los datos para registrar un nuevo Punch Item' }}
                </p>
                <div class="mt-2">
                    <span class="text-sm text-gray-500">Obra: </span>
                    <span class="text-sm font-medium text-gray-900">{{ $worksite->name }}</span>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('worksites.show', $worksite->id) }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                <!-- Responsable -->
                <div>
                    <label for="responsible_id" class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                    <select id="responsible_id" wire:model="form.responsible_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar responsable</option>
                        @foreach($responsibles as $responsible)
                            <option value="{{ $responsible->id }}">{{ $responsible->name }}</option>
                        @endforeach
                    </select>
                    @error('form.responsible_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Observaciones -->
                <div class="md:col-span-2">
                    <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">Observaciones *</label>
                    <textarea id="observations" wire:model="form.observations" rows="6"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                              placeholder="Descripción detallada del Punch Item..."></textarea>
                    @error('form.observations')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Información de la obra (solo lectura) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Obra Asociada</label>
                    <input type="text"
                           value="{{ $worksite->name }}"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <a href="{{ route('worksites.show', $worksite->id) }}"
                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
                <button type="submit"
                        class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ isset($punchItem) ? 'Actualizar' : 'Guardar' }} Punch Item
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
