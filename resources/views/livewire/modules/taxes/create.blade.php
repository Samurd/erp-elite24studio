<div>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                {{ isset($taxRecord) ? 'Editar Impuesto' : 'Nuevo Impuesto' }}
            </h1>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Entidad -->
                <div class="md:col-span-2">
                    <label for="entity" class="block text-sm font-medium text-gray-700 mb-2">Entidad</label>
                    <input type="text" id="entity" wire:model="form.entity"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.entity')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tipo -->
                <div>
                    <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                    <select id="type_id" wire:model="form.type_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccione un tipo</option>
                        @foreach($typeOptions as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('form.type_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="status_id" wire:model="form.status_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccione un estado</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('form.status_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fecha -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                    <input type="date" id="date" wire:model="form.date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Base -->
                <div>
                    <x-money-input model="form.base" label="Base Gravable" placeholder="$0" />
                </div>

                <!-- Porcentaje -->
                <div>
                    <label for="porcentage" class="block text-sm font-medium text-gray-700 mb-2">Porcentaje (%)</label>
                    <input type="number" id="porcentage" wire:model="form.porcentage" min="0" max="100"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.porcentage')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Monto -->
                <div>
                    <x-money-input model="form.amount" label="Monto Retenido/Pagado" placeholder="$0" />
                </div>

                <!-- Observaciones -->
                <div class="md:col-span-2">
                    <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea id="observations" wire:model="form.observations" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"></textarea>
                    @error('form.observations')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @if(isset($taxRecord))
                @livewire('modules.cloud.components.model-attachments', [
                    'model' => $taxRecord,
                    'area' => 'finanzas'
                ])
            @else
                @livewire('modules.cloud.components.model-attachments-creator', [
                    'modelClass' => \App\Models\TaxRecord::class,
                    'areaSlug' => 'finanzas'
                ])
            @endif

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('finances.taxes.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    {{ isset($taxRecord) ? 'Actualizar Impuesto' : 'Guardar Impuesto' }}
                </button>
            </div>
        </form>
    </div>
    @livewire('components.notification')

</div>