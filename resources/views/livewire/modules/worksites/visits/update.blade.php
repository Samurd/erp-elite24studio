<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Editar Visita</h1>
                <p class="text-gray-600 mt-1">Modificar información de la visita</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('worksites.show', $worksite->id) }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a la Obra
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Información de la Obra (solo lectura) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Obra</label>
                    <input type="text"
                           value="{{ $worksite->name }}"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                </div>

                <!-- ID de la Visita (solo lectura) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ID de Visita</label>
                    <input type="text"
                           value="#{{ $visit->id }}"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                </div>

                <!-- Fecha de Visita -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Visita <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           wire:model="form.visit_date"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.visit_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Visitante -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Visita realizada por
                    </label>
                    <select wire:model="form.performed_by"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar visitante...</option>
                        @foreach($visitorOptions as $visitor)
                            <option value="{{ $visitor->id }}">{{ $visitor->name }}</option>
                        @endforeach
                    </select>
                    @error('form.performed_by')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select wire:model="form.status_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar estado...</option>
                        @foreach($visitStatusOptions as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('form.status_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Observaciones Generales -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones Generales</label>
                    <textarea wire:model="form.general_observations"
                              rows="2"
                              placeholder="Observaciones generales sobre la visita..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"></textarea>
                    @error('form.general_observations')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notas Internas -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notas Internas</label>
                    <textarea wire:model="form.internal_notes"
                              rows="2"
                              placeholder="Notas internas sobre la visita..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"></textarea>
                    @error('form.internal_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Información de creación (solo lectura) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Creación</label>
                    <input type="text"
                           value="{{ $visit->created_at->format('d/m/Y H:i') }}"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Última Actualización</label>
                    <input type="text"
                           value="{{ $visit->updated_at->format('d/m/Y H:i') }}"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('worksites.show', $worksite->id) }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Actualizar Visita
                </button>
            </div>
        </form>
    </div>
</div>
