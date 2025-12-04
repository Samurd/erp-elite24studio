<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Editar Cambio</h1>
                <p class="text-gray-600 mt-1">Modificar información del cambio</p>
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
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- ID del Cambio (solo lectura) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ID del Cambio</label>
                    <input type="text"
                           value="#{{ $change->id }}"
                           readonly
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                </div>

                <!-- Fecha del Cambio -->
                <div>
                    <label for="change_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha del Cambio <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           id="change_date"
                           wire:model="form.change_date"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.change_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tipo de Cambio -->
                <div>
                    <label for="change_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Cambio <span class="text-red-500">*</span>
                    </label>
                    <select id="change_type_id" wire:model="form.change_type_id"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar tipo</option>
                        @foreach($changeTypeOptions as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('form.change_type_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Impacto en Presupuesto -->
                <div>
                    <label for="budget_impact_id" class="block text-sm font-medium text-gray-700 mb-2">Impacto en Presupuesto</label>
                    <select id="budget_impact_id" wire:model="form.budget_impact_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar impacto</option>
                        @foreach($budgetImpactOptions as $impact)
                            <option value="{{ $impact->id }}">{{ $impact->name }}</option>
                        @endforeach
                    </select>
                    @error('form.budget_impact_id')
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

                <!-- Aprobado por -->
                <div>
                    <label for="approved_by" class="block text-sm font-medium text-gray-700 mb-2">Aprobado por</label>
                    <select id="approved_by" wire:model="form.approved_by"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar aprobador</option>
                        @foreach($responsibles as $responsible)
                            <option value="{{ $responsible->id }}">{{ $responsible->name }}</option>
                        @endforeach
                    </select>
                    @error('form.approved_by')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Solicitado por -->
                <div class="md:col-span-2">
                    <label for="requested_by" class="block text-sm font-medium text-gray-700 mb-2">Solicitado por</label>
                    <input type="text"
                           id="requested_by"
                           wire:model="form.requested_by"
                           placeholder="Nombre de quien solicita el cambio..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.requested_by')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" wire:model="form.description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                              placeholder="Descripción detallada del cambio..."
                              required></textarea>
                    @error('form.description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Notas Internas -->
                <div class="md:col-span-2">
                    <label for="internal_notes" class="block text-sm font-medium text-gray-700 mb-2">Notas Internas</label>
                    <textarea id="internal_notes" wire:model="form.internal_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                              placeholder="Notas internas sobre el cambio..."></textarea>
                    @error('form.internal_notes')
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

                <!-- Información de creación (solo lectura) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Creación</label>
                    <input type="text"
                           value="{{ $change->created_at->format('d/m/Y H:i') }}"
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
                    <i class="fas fa-save mr-2"></i>Actualizar Cambio
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
