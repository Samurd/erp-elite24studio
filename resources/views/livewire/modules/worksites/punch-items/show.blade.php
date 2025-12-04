<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detalle de Punch Item</h1>
                <p class="text-gray-600 mt-1">Información completa del Punch Item</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('worksites.punch-items.edit', [$worksite->id, $punchItem->id]) }}"
                   class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar Punch Item
                </a>
                <a href="{{ route('worksites.show', $worksite->id) }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a la Obra
                </a>
            </div>
        </div>
    </div>

    <!-- Punch Item Information -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Punch Item</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- ID del Punch Item -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">ID del Punch Item</label>
                <p class="text-gray-900">#{{ $punchItem->id }}</p>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                @if($punchItem->status)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($punchItem->status->name == 'Pendiente') bg-gray-100 text-gray-800
                        @elseif($punchItem->status->name == 'En Progreso') bg-yellow-100 text-yellow-800
                        @elseif($punchItem->status->name == 'Completado') bg-green-100 text-green-800
                        @elseif($punchItem->status->name == 'Cancelado') bg-red-100 text-red-800
                        @else bg-blue-100 text-blue-800 @endif
                    ">
                        {{ $punchItem->status->name }}
                    </span>
                @else
                    <span class="text-gray-500">-</span>
                @endif
            </div>

            <!-- Responsable -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Responsable</label>
                <p class="text-gray-900">{{ $punchItem->responsible ? $punchItem->responsible->name : '-' }}</p>
            </div>

            <!-- Observaciones -->
            <div class="md:col-span-2 lg:col-span-3">
                <label class="block text-sm font-medium text-gray-500 mb-1">Observaciones</label>
                <p class="text-gray-900">{{ $punchItem->observations ?: '-' }}</p>
            </div>

            <!-- Fecha de Creación -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Creación</label>
                <p class="text-gray-900">{{ $punchItem->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <!-- Última Actualización -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Última Actualización</label>
                <p class="text-gray-900">{{ $punchItem->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Files Section -->
    @if($punchItem->files->count() > 0)
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Archivos Adjuntos</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($punchItem->files as $file)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <i class="fas fa-file text-gray-400 mr-2"></i>
                                <span class="text-sm font-medium text-gray-900 truncate">{{ $file->name }}</span>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($file->created_at)->format('d/m/Y H:i') }}
                        </div>
                        <a href="{{ asset('storage/' . $file->path) }}" 
                           target="_blank"
                           class="mt-2 inline-block text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-download mr-1"></i> Descargar
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
</div>