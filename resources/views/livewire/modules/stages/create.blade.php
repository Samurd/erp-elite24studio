<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ isset($stage) ? 'Editar Etapa' : 'Nueva Etapa' }}
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ isset($stage) ? 'Actualiza la información de la etapa' : 'Complete los datos para registrar una nueva etapa' }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('stages.index') }}"
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
                <!-- Nombre de la Etapa -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Etapa</label>
                    <input type="text" id="name" wire:model="form.name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                           placeholder="Nombre de la etapa">
                    @error('form.name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Descripción -->
            <div class="grid grid-cols-1 gap-6 mt-6">
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea id="description" wire:model="form.description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                              placeholder="Descripción detallada de la etapa..."></textarea>
                    @error('form.description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <a href="{{ route('stages.index') }}"
                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
                <button type="submit"
                        class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ isset($stage) ? 'Actualizar' : 'Guardar' }} Etapa
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