<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detalles de Reunión</h1>
                <p class="text-gray-600 mt-1">Información completa de la reunión</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('meetings.edit', $meeting->id) }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('meetings.index') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Meeting Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información General</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                        <p class="text-gray-900">#{{ $meeting->id }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                        <p class="text-gray-900">{{ $meeting->title }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <p class="text-gray-900">
                            @if($meeting->date)
                                {{ $meeting->date->format('d/m/Y') }}
                            @else
                                No definida
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                        <p class="text-gray-900">
                            @if($meeting->start_time && $meeting->end_time)
                                {{ $meeting->start_time->format('H:i') }} - {{ $meeting->end_time->format('H:i') }}
                            @elseif($meeting->start_time)
                                {{ $meeting->start_time->format('H:i') }}
                            @else
                                No definida
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Equipo</label>
                        <p class="text-gray-900">
                            {{ $meeting->team ? $meeting->team->name : 'No asignado' }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        @if($meeting->status)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL de la Reunión</label>
                        @if($meeting->url)
                            <a href="{{ $meeting->url }}" 
                               target="_blank" 
                               class="text-blue-600 hover:text-blue-900 text-sm flex items-center"
                               title="Abrir URL de la reunión">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                {{ $meeting->url }}
                            </a>
                        @else
                            <p class="text-gray-500 text-sm">No definida</p>
                        @endif
                    </div>
                                @if($meeting->status->name == 'Programada') bg-blue-100 text-blue-800
                                @elseif($meeting->status->name == 'Realizada') bg-green-100 text-green-800
                                @elseif($meeting->status->name == 'Cancelada') bg-red-100 text-red-800
                                @elseif($meeting->status->name == 'Postergada') bg-yellow-100 text-yellow-800
                                @elseif($meeting->status->name == 'En Progreso') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif
                            ">
                                {{ $meeting->status->name }}
                            </span>
                        @else
                            <span class="text-sm text-gray-500">No definido</span>
                        @endif
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Cumplida</label>
                        @if($meeting->goal)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Sí
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                No
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            @if($meeting->notes)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Notas Previas</h2>
                <div class="prose max-w-none">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $meeting->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Observations Section -->
            @if($meeting->observations)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Observaciones Finales</h2>
                <div class="prose max-w-none">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $meeting->observations }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Responsibles -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Responsables</h2>
                @if($meeting->responsibles && $meeting->responsibles->count() > 0)
                    <div class="space-y-2">
                        @foreach($meeting->responsibles as $responsible)
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">
                                            {{ strtoupper(substr($responsible->name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $responsible->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $responsible->email }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No hay responsables asignados</p>
                @endif
            </div>

            <!-- Metadata -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información de Registro</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Creación</label>
                        <p class="text-gray-900">{{ $meeting->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Última Actualización</label>
                        <p class="text-gray-900">{{ $meeting->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
