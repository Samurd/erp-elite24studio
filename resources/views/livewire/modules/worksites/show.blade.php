<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detalle de Obra</h1>
                <p class="text-gray-600 mt-1">Información completa de la obra</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('worksites.edit', $worksite->id) }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar Obra
                </a>
                <a href="{{ route('worksites.index') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Worksite Information -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Información de la Obra</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Nombre -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nombre</label>
                <p class="text-gray-900">{{ $worksite->name }}</p>
            </div>

            <!-- Proyecto -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Proyecto</label>
                <p class="text-gray-900">{{ $worksite->project ? $worksite->project->name : '-' }}</p>
            </div>

            <!-- Tipo -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tipo</label>
                @if($worksite->type)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $worksite->type->name }}
                    </span>
                @else
                    <span class="text-gray-500">-</span>
                @endif
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                @if($worksite->status)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($worksite->status->name == 'Activa') bg-green-100 text-green-800
                        @elseif($worksite->status->name == 'En Progreso') bg-yellow-100 text-yellow-800
                        @elseif($worksite->status->name == 'Pausada') bg-orange-100 text-orange-800
                        @elseif($worksite->status->name == 'Finalizada') bg-blue-100 text-blue-800
                        @elseif($worksite->status->name == 'Cancelada') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif
                    ">
                        {{ $worksite->status->name }}
                    </span>
                @else
                    <span class="text-gray-500">-</span>
                @endif
            </div>

            <!-- Responsable -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Responsable</label>
                <p class="text-gray-900">{{ $worksite->responsible ? $worksite->responsible->name : '-' }}</p>
            </div>

            <!-- Dirección -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Dirección</label>
                <p class="text-gray-900">{{ $worksite->address ?: '-' }}</p>
            </div>

            <!-- Fecha de Inicio -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Inicio</label>
                <p class="text-gray-900">{{ $worksite->start_date ? $worksite->start_date->format('d/m/Y') : '-' }}</p>
            </div>

            <!-- Fecha de Fin -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Fin</label>
                <p class="text-gray-900">{{ $worksite->end_date ? $worksite->end_date->format('d/m/Y') : '-' }}</p>
            </div>

            <!-- Fecha de Creación -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Creación</label>
                <p class="text-gray-900">{{ $worksite->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Punch Items Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Punch List</h2>
            <a href="{{ route('worksites.punch-items.create', $worksite->id) }}"
               class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Nuevo Punch Item
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Búsqueda general -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Observaciones..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Filtro por estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select wire:model.live="status_filter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por responsable -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                    <select wire:model.live="responsible_filter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        @foreach($responsibles as $responsible)
                            <option value="{{ $responsible->id }}">{{ $responsible->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Registros por página -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Registros por página</label>
                    <select wire:model.live="perPage"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <!-- Botón limpiar filtros -->
                <div class="flex items-end">
                    <button wire:click="clearFilters"
                            class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Punch Items Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Observaciones
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Responsable
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Archivos
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha Creación
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($punchItems as $punchItem)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    #{{ $punchItem->id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $punchItem->observations }}">
                                    {{ $punchItem->observations ?: '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $punchItem->responsible ? $punchItem->responsible->name : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $punchItem->files->count() }} archivo(s)
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $punchItem->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('worksites.punch-items.show', [$worksite->id, $punchItem->id]) }}"
                                       class="text-blue-600 hover:text-blue-900" title="Ver">
                                        <i class="fa-solid fa-eye mr-1"></i> Ver
                                    </a>
                                    <a href="{{ route('worksites.punch-items.edit', [$worksite->id, $punchItem->id]) }}"
                                       class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </a>
                                    <button wire:click="delete({{ $punchItem->id }})"
                                            wire:confirm="¿Estás seguro de que quieres eliminar este Punch Item?"
                                            class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron Punch Items para esta obra
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($punchItems->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $punchItems->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Mostrando
                            <span class="font-medium">{{ $punchItems->firstItem() }}</span>
                            a
                            <span class="font-medium">{{ $punchItems->lastItem() }}</span>
                            de
                            <span class="font-medium">{{ $punchItems->total() }}</span>
                            resultados
                        </p>
                    </div>
                    <div>
                        {{ $punchItems->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Changes Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Cambios</h2>
            <a href="{{ route('worksites.changes.create', $worksite->id) }}"
               class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Nuevo Cambio
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Búsqueda general -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="change_search"
                           placeholder="Descripción..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Filtro por tipo de cambio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Cambio</label>
                    <select wire:model.live="change_type_filter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        @foreach($changeTypeOptions as $changeType)
                            <option value="{{ $changeType->id }}">{{ $changeType->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select wire:model.live="change_status_filter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        @foreach($changeStatusOptions as $changeStatus)
                            <option value="{{ $changeStatus->id }}">{{ $changeStatus->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Registros por página -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Registros por página</label>
                    <select wire:model.live="change_perPage"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <!-- Botón limpiar filtros -->
                <div class="flex items-end">
                    <button wire:click="clearChangeFilters"
                            class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Changes Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Solicitado por
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Descripción
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Impacto Presupuesto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($changes as $change)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    #{{ $change->id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $change->change_date ? $change->change_date->format('d/m/Y') : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($change->type)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ $change->type->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $change->requested_by ?: '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $change->description }}">
                                    {{ $change->description ?: '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($change->budgetImpact)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                        {{ $change->budgetImpact->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($change->status)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($change->status->name == 'Pendiente') bg-gray-100 text-gray-800
                                        @elseif($change->status->name == 'Aprobado') bg-green-100 text-green-800
                                        @elseif($change->status->name == 'Rechazado') bg-red-100 text-red-800
                                        @elseif($change->status->name == 'En Revisión') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800 @endif
                                    ">
                                        {{ $change->status->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('worksites.changes.show', [$worksite->id, $change->id]) }}"
                                       class="text-blue-600 hover:text-blue-900" title="Ver">
                                        <i class="fa-solid fa-eye mr-1"></i> Ver
                                    </a>
                                    <a href="{{ route('worksites.changes.edit', [$worksite->id, $change->id]) }}"
                                       class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </a>
                                    <button wire:click="deleteChange({{ $change->id }})"
                                            wire:confirm="¿Estás seguro de que quieres eliminar este cambio?"
                                            class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron cambios para esta obra
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($changes->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $changes->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Mostrando
                            <span class="font-medium">{{ $changes->firstItem() }}</span>
                            a
                            <span class="font-medium">{{ $changes->lastItem() }}</span>
                            de
                            <span class="font-medium">{{ $changes->total() }}</span>
                            resultados
                        </p>
                    </div>
                    <div>
                        {{ $changes->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Visits Section -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Visitas</h2>
            <a href="{{ route('worksites.visits.create', $worksite->id) }}"
               class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Nueva Visita
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Búsqueda general -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                    <input type="text"
                           wire:model.live.debounce.300ms="visit_search"
                           placeholder="Notas..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Filtro por estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select wire:model.live="visit_status_filter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        @foreach($visitStatusOptions as $visitStatus)
                            <option value="{{ $visitStatus->id }}">{{ $visitStatus->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por visitante -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Visitante</label>
                    <select wire:model.live="visit_visitor_filter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        @foreach($visitors as $visitor)
                            <option value="{{ $visitor->id }}">{{ $visitor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Registros por página -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Registros por página</label>
                    <select wire:model.live="visit_perPage"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <!-- Botón limpiar filtros -->
                <div class="flex items-end">
                    <button wire:click="clearVisitFilters"
                            class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Visits Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Visitante
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Realizado por
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Notas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Archivos
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($visits as $visit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    #{{ $visit->id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $visit->visit_date ? $visit->visit_date->format('d/m/Y') : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $visit->visitor ? $visit->visitor->name : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $visit->performer ? $visit->performer->name : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $visit->general_observations }}">
                                    {{ $visit->general_observations ?: '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $visit->files->count() }} archivo(s)
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('worksites.visits.show', [$worksite->id, $visit->id]) }}"
                                       class="text-blue-600 hover:text-blue-900" title="Ver">
                                        <i class="fa-solid fa-eye mr-1"></i> Ver
                                    </a>
                                    <a href="{{ route('worksites.visits.edit', [$worksite->id, $visit->id]) }}"
                                       class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </a>
                                    <button wire:click="deleteVisit({{ $visit->id }})"
                                            wire:confirm="¿Estás seguro de que quieres eliminar esta visita?"
                                            class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron visitas para esta obra
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($visits->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $visits->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Mostrando
                            <span class="font-medium">{{ $visits->firstItem() }}</span>
                            a
                            <span class="font-medium">{{ $visits->lastItem() }}</span>
                            de
                            <span class="font-medium">{{ $visits->total() }}</span>
                            resultados
                        </p>
                    </div>
                    <div>
                        {{ $visits->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
</div>
