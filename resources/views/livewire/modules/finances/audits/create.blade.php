<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ isset($audit) ? 'Editar Auditoría' : 'Nueva Auditoría' }}
                </h1>
                <p class="text-gray-600 mt-1">Complete la información de la auditoría</p>
            </div>
            <div>
                <a href="{{ route('finances.audits.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Objective -->
                <div class="col-span-1 md:col-span-2">
                    <x-money-input model="form.objective" label="Objetivo (Monto)" placeholder="$0.00" />
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Auditoría <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="form.type_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Seleccione un tipo</option>
                        @foreach($auditTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('form.type_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="form.status_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Seleccione un estado</option>
                        @foreach($auditStatuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('form.status_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Date Register -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Registro <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model="form.date_register"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('form.date_register')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Date Audit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Auditoría <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model="form.date_audit"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('form.date_audit')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Place -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Lugar
                    </label>
                    <input type="text" wire:model="form.place"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Lugar de la auditoría">
                    @error('form.place')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Observations -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Observaciones
                    </label>
                    <textarea wire:model="form.observations" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Observaciones adicionales..."></textarea>
                    @error('form.observations')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                @if(isset($audit))
                    @livewire('modules.cloud.components.model-attachments', [
                        'model' => $audit,
                        'area' => 'finanzas'
                    ])
                @else
                    @livewire('modules.cloud.components.model-attachments-creator', [
                        'modelClass' => \App\Models\Audit::class,
                        'areaSlug' => 'finanzas'
                    ])
                @endif
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                <a href="{{ route('finances.audits.index') }}"
                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ isset($audit) ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
    @livewire('components.notification')

</div>
```