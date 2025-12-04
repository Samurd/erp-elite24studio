<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ isset($alliance) ? 'Editar Alianza' : 'Nueva Alianza' }}
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ isset($alliance) ? 'Actualiza la información de la alianza' : 'Complete los datos para registrar una nueva alianza' }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('donations.alliances.index') }}"
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
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                    <input type="text" id="name" wire:model="form.name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Nombre de la alianza">
                    @error('form.name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tipo de Alianza -->
                <div>
                    <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Alianza</label>
                    <select id="type_id" wire:model="form.type_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar tipo</option>
                        @foreach($typeOptions as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('form.type_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fecha de Inicio -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Inicio</label>
                    <input type="date" id="start_date" wire:model="form.start_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.start_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Vigencia (Meses) -->
                <div>
                    <label for="validity" class="block text-sm font-medium text-gray-700 mb-2">Vigencia (Meses)</label>
                    <input type="number" id="validity" wire:model="form.validity" min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Ej: 12">
                    @error('form.validity')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Certificado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">¿Certificado?</label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="certified" value="1" wire:model="form.certified"
                                class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Sí</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="certified" value="0" wire:model="form.certified"
                                class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">No</span>
                        </label>
                    </div>
                    @error('form.certified')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @if(isset($alliance))
                @livewire('modules.cloud.components.model-attachments', [
                    'model' => $alliance,
                    'area' => 'donaciones'
                ])
            @else
                @livewire('modules.cloud.components.model-attachments-creator', [
                    'modelClass' => \App\Models\Alliance::class,
                    'areaSlug' => 'donaciones'
                ])
            @endif

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <a href="{{ route('donations.alliances.index') }}"
                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
                <button type="submit"
                    class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ isset($alliance) ? 'Actualizar' : 'Guardar' }} Alianza
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