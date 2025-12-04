<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Editar Política</h1>
                <p class="text-gray-600 mt-1">Modificar información de la política</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('policies.show', $policy->id) }}"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-eye mr-2"></i>Ver
                </a>
                <a href="{{ route('policies.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de la Política <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" wire:model="form.name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Ej: Política de Seguridad de la Información">
                        @error('form.name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Política <span class="text-red-500">*</span>
                        </label>
                        <select id="type_id" wire:model="form.type_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar tipo...</option>
                            @foreach($typeOptions as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('form.type_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Estado
                        </label>
                        <select id="status_id" wire:model="form.status_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar estado...</option>
                            @foreach($statusOptions as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                        @error('form.status_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Assigned To -->
                    <div>
                        <label for="assigned_to_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Responsable
                        </label>
                        <select id="assigned_to_id" wire:model="form.assigned_to_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar responsable...</option>
                            @foreach($userOptions as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('form.assigned_to_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Issued At -->
                    <div>
                        <label for="issued_at" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Emisión <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="issued_at" wire:model="form.issued_at"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        @error('form.issued_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reviewed At -->
                    <div>
                        <label for="reviewed_at" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Última Revisión
                        </label>
                        <input type="date" id="reviewed_at" wire:model="form.reviewed_at"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        @error('form.reviewed_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción
                        </label>
                        <textarea id="description" wire:model="form.description" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Descripción detallada de la política..."></textarea>
                        @error('form.description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            @livewire('modules.cloud.components.model-attachments', [
                'model' => $policy,
                'area' => 'politicas'
            ])
  

            <!-- Form Actions -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('policies.show', $policy->id) }}"
                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Actualizar Política
                </button>
            </div>
        </form>
    </div>

    {{-- Componente de notificaciones --}}
    @livewire('components.notification')

    {{-- Modal selector de carpetas --}}

</div>