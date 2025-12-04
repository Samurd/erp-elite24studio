<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detalle de Visita</h1>
                <p class="text-gray-600 mt-1">Información completa de la visita</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('worksites.visits.edit', [$worksite->id, $visit->id]) }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar Visita
                </a>
                <a href="{{ route('worksites.show', $worksite->id) }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a la Obra
                </a>
            </div>
        </div>
    </div>

    <!-- Visit Information -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Información de la Visita</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- ID de la Visita -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">ID de Visita</label>
                <p class="text-gray-900">#{{ $visit->id }}</p>
            </div>

            <!-- Obra -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Obra</label>
                <p class="text-gray-900">{{ $worksite->name }}</p>
            </div>

            <!-- Fecha de Visita -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Visita</label>
                <p class="text-gray-900">{{ $visit->visit_date ? $visit->visit_date->format('d/m/Y') : '-' }}</p>
            </div>

            <!-- Visitante -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Visitante</label>
                <p class="text-gray-900">{{ $visit->visitor ? $visit->visitor->name : '-' }}</p>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                @if($visit->status)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($visit->status->name == 'Programada') bg-blue-100 text-blue-800
                        @elseif($visit->status->name == 'Realizada') bg-green-100 text-green-800
                        @elseif($visit->status->name == 'Cancelada') bg-red-100 text-red-800
                        @elseif($visit->status->name == 'Postergada') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif
                    ">
                        {{ $visit->status->name }}
                    </span>
                @else
                    <span class="text-gray-500">-</span>
                @endif
            </div>

            <!-- Fecha de Creación -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Creación</label>
                <p class="text-gray-900">{{ $visit->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <!-- Observaciones Generales -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Observaciones Generales</label>
                <p class="text-gray-900">{{ $visit->general_observations ?: '-' }}</p>
            </div>

            <!-- Notas Internas -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Notas Internas</label>
                <p class="text-gray-900">{{ $visit->internal_notes ?: '-' }}</p>
            </div>

            <!-- Última Actualización -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Última Actualización</label>
                <p class="text-gray-900">{{ $visit->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-end space-x-3">
            <a href="{{ route('worksites.visits.edit', [$worksite->id, $visit->id]) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-edit mr-2"></i>Editar Visita
            </a>
            <button wire:click="delete"
                    wire:confirm="¿Estás seguro de que quieres eliminar esta visita?"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-2"></i>Eliminar Visita
            </button>
            <a href="{{ route('worksites.show', $worksite->id) }}"
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Volver a la Obra
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
</div>