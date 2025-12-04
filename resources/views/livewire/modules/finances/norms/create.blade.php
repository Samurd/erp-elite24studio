<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ isset($norm) ? 'Editar Norma' : 'Crear Norma' }}
                </h1>
                <p class="text-gray-600 mt-1">Complete la información de la norma</p>
            </div>
            <div>
                <a href="{{ route('finances.norms.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="form.name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Ej: Norma ISO 9001">
                    @error('form.name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                @if(isset($norm))
                    @livewire('modules.cloud.components.model-attachments', [
                        'model' => $norm,
                        'area' => 'finanzas'
                    ])

                @else
                    @livewire('modules.cloud.components.model-attachments-creator', [
                        'modelClass' => \App\Models\Norm::class,
                        'areaSlug' => 'finanzas'
                    ])
                @endif

                <!-- Actions -->
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                    <a href="{{ route('finances.norms.index') }}"
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>{{ isset($norm) ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
        </form>
    </div>

</div>