<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detalles del Evento</h1>
                <p class="text-gray-600 mt-1">Información completa del evento y sus ítems</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('marketing.events.index') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <a href="{{ route('marketing.events.edit', $event) }}"
                   class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
            </div>
        </div>
    </div>

    <!-- Event Details -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Evento</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Nombre -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nombre del Evento</label>
                <p class="text-gray-800 font-medium">{{ $event->name }}</p>
            </div>

            <!-- Tipo -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tipo de Evento</label>
                <p class="text-gray-800 font-medium">{{ $event->type->name ?? 'N/A' }}</p>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                <p class="text-gray-800 font-medium">{{ $event->status->name ?? 'N/A' }}</p>
            </div>

            <!-- Fecha -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha del Evento</label>
                <p class="text-gray-800 font-medium">{{ $event->event_date->format('d/m/Y') }}</p>
            </div>

            <!-- Ubicación -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Ubicación</label>
                <p class="text-gray-800 font-medium">{{ $event->location }}</p>
            </div>

            <!-- Responsable -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Responsable</label>
                <p class="text-gray-800 font-medium">{{ $event->responsible->name ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Observaciones -->
        @if($event->observations)
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-500 mb-1">Observaciones</label>
                <p class="text-gray-800">{{ $event->observations }}</p>
            </div>
        @endif
    </div>

    <!-- Event Items -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Ítems del Evento</h2>
            <a href="{{ route('marketing.events.items.create', $event->id) }}"
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Agregar Ítem
            </a>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Search -->
            <div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search"
                       placeholder="Buscar por descripción..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>

            <!-- Unit Filter -->
            <div>
                <select wire:model.live="unitFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <option value="">Todas las unidades</option>
                    @foreach($unitOptions as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Per Page -->
            <div>
                <select wire:model.live="perPage"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <option value="10">10 por página</option>
                    <option value="25">25 por página</option>
                    <option value="50">50 por página</option>
                    <option value="100">100 por página</option>
                </select>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Descripción
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cantidad
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Unidad
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Precio Unitario
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Precio Total
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->unit->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ App\Services\MoneyFormatterService::format($item->unit_price) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ App\Services\MoneyFormatterService::format($item->total_price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('marketing.events.items.edit', [$event->id, $item->id]) }}"
                                   class="text-yellow-600 hover:text-yellow-900 mr-3" title="Editar">
                                    Editar
                                </a>
                                <button wire:click="delete({{ $item->id }})"
                                        wire:confirm="¿Estás seguro de que quieres eliminar este ítem?"
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                No se encontraron ítems para este evento
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $items->links() }}
        </div>
    </div>
</div>
