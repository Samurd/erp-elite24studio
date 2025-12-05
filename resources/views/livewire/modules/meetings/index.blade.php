<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Reuniones</h1>
                <p class="text-gray-600 mt-1">Gestión de reuniones del sistema</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('meetings.create') }}"
                   class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nueva Reunión
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Búsqueda general -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Título, notas u observaciones..."
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

            <!-- Filtro por equipo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Equipo</label>
                <select wire:model.live="team_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <option value="">Todos</option>
                    @foreach($teamOptions as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filtro por meta cumplida -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Cumplida</label>
                <select wire:model.live="goal_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <option value="">Todos</option>
                    <option value="0">No</option>
                    <option value="1">Sí</option>
                </select>
            </div>

            <!-- Fecha desde -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                <input type="date"
                       wire:model.live="date_from"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>

            <!-- Fecha hasta -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                <input type="date"
                       wire:model.live="date_to"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            URL
                        </th>
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Título
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hora
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Equipo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            URL
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Meta
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Responsables
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($meetings as $meeting)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    #{{ $meeting->id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $meeting->title }}
                                </div>
                                @if($meeting->notes)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ Str::limit($meeting->notes, 50) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($meeting->date)
                                    <div class="text-sm text-gray-900">
                                        {{ $meeting->date->format('d/m/Y') }}
                                    </div>
                                    @if($meeting->date->isPast())
                                        <div class="text-xs text-red-600">Pasada</div>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($meeting->start_time && $meeting->end_time)
                                        {{ $meeting->start_time->format('H:i') }} - {{ $meeting->end_time->format('H:i') }}
                                    @elseif($meeting->start_time)
                                        {{ $meeting->start_time->format('H:i') }}
                                    @else
                                        -
                                    @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $meeting->team ? $meeting->team->name : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($meeting->status)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
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
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($meeting->url)
                                    <a href="{{ $meeting->url }}"
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-900 text-sm"
                                       title="Abrir URL de la reunión">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        {{ Str::limit($meeting->url, 30) }}
                                    </a>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($meeting->goal)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Sí
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        No
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($meeting->responsibles && $meeting->responsibles->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($meeting->responsibles as $responsible)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                {{ $responsible->name }}
                </span>
            @endforeach
        </div>
    @else
        <span class="text-sm text-gray-500">-</span>
    @endif

                                
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('meetings.show', $meeting->id) }}"
                                       class="text-green-600 hover:text-green-900" title="Ver">
                                        <i class="fa-solid fa-eye mr-1"></i> Ver
                                    </a>
                                    <a href="{{ route('meetings.edit', $meeting->id) }}"
                                       class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </a>
                                    <button wire:click="delete({{ $meeting->id }})"
                                            wire:confirm="¿Estás seguro de que quieres eliminar esta reunión?"
                                            class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron reuniones
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($meetings->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $meetings->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Mostrando
                            <span class="font-medium">{{ $meetings->firstItem() }}</span>
                            a
                            <span class="font-medium">{{ $meetings->lastItem() }}</span>
                            de
                            <span class="font-medium">{{ $meetings->total() }}</span>
                            resultados
                        </p>
                    </div>
                    <div>
                        {{ $meetings->links() }}
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
