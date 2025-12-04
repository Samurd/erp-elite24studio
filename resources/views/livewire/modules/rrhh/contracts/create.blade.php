<div>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                {{ isset($isEditing) ? 'Editar Contrato' : 'Nuevo Contrato' }}
            </h1>
            <p class="text-gray-600 mt-1">Complete la información del contrato</p>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">
            <!-- Employee Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Empleado</label>
                <select wire:model="form.employee_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Seleccione un empleado</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">
                            {{ $employee->formatted_full_name }} - {{ $employee->identification_number }}
                        </option>
                    @endforeach
                </select>
                @error('form.employee_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Contract Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Contrato</label>
                    <select wire:model="form.type_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Seleccione un tipo</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('form.type_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Contract Category (Relationship Type) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoría (Tipo de Relación)</label>
                    <select wire:model="form.category_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Seleccione una categoría</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('form.category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Inicio</label>
                    <input type="date" wire:model="form.start_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    @error('form.start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Fin (Opcional)</label>
                    <input type="date" wire:model="form.end_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    @error('form.end_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Amount -->
                <div>
                    <x-money-input model="form.amount" label="Monto / Salario" />
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select wire:model="form.status_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Seleccione un estado</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('form.status_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Schedule -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Horario</label>
                <input type="text" wire:model="form.schedule" placeholder="Ej: Lunes a Viernes 8am - 5pm"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                @error('form.schedule') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            @if(isset($contract))
                @livewire('modules.cloud.components.model-attachments', [
                    'model' => $contract,
                    'area' => 'rrhh'
                ])
            @else
                @livewire('modules.cloud.components.model-attachments-creator', [
                    'modelClass' => \App\Models\Contract::class,
                    'areaSlug' => 'rrhh'
                ])
            @endif

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('rrhh.contracts.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    {{ isset($isEditing) ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</div>