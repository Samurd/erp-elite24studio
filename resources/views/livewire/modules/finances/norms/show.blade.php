<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detalles de la Norma</h1>
                <p class="text-gray-600 mt-1">Información completa y documentos asociados</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('finances.norms.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <a href="{{ route('finances.norms.edit', $norm->id) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
            </div>
        </div>
    </div>

    <!-- Norm Details -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            <i class="fas fa-info-circle text-green-600 mr-2"></i>Información General
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nombre</label>
                <p class="text-lg font-semibold text-gray-900">{{ $norm->name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Creado por</label>
                <p class="text-lg font-semibold text-gray-900">{{ $norm->user->name ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Creación</label>
                <p class="text-gray-700">{{ $norm->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Última Actualización</label>
                <p class="text-gray-700">{{ $norm->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <livewire:modules.cloud.components.model-files :model="$norm" />
</div>