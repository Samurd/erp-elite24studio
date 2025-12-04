<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Suscripciones</h1>
                <p class="text-gray-600 mt-1">Gestión de suscripciones y subcontratos</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('subs.create') }}"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nueva Suscripción
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
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nombre..."
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

            <!-- Filtro por fecha desde -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio Desde</label>
                <input type="date" wire:model.live="date_from"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>

            <!-- Filtro por fecha hasta -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio Hasta</label>
                <input type="date" wire:model.live="date_to"
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
                            Nombre
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Frecuencia
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Monto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fechas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Archivos
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Notificacion
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subs as $sub)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    #{{ $sub->id }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $sub->name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $sub->type }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($sub->frequency)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $sub->frequency->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \App\Services\MoneyFormatterService::format($sub->amount) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    Inicio:
                                    {{ $sub->start_date ? \Carbon\Carbon::parse($sub->start_date)->format('d/m/Y') : '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    Renovación:
                                    {{ $sub->renewal_date ? \Carbon\Carbon::parse($sub->renewal_date)->format('d/m/Y') : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($sub->status)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $sub->status->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($sub->files()->count() > 0)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $sub->files()->count() }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $sub->notificationTemplates()->count() > 0 ? 'Si' : 'No' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if($sub->notificationTemplates()->count() === 0)
                                        @if ($sub->renewal_date && $sub->user_id)
                                            <button wire:click="{{ $sub->createAutomaticNotification() }}"
                                                class="text-blue-600 hover:text-blue-900" title="Crear notificación">
                                                <i class="fa-solid fa-plus mr-1"></i> Crear notificación
                                            </button>

                                        @else
                                            <span class="text-sm text-gray-500">Completa los campos de fecha de renovación y
                                                usuario</span>
                                        @endif
                                    @endif

                                    <a href="{{ route('subs.edit', $sub->id) }}" class="text-blue-600 hover:text-blue-900"
                                        title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </a>

                                    <button wire:click="delete({{ $sub->id }})"
                                        wire:confirm="¿Estás seguro de que quieres eliminar esta suscripción?"
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron suscripciones
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($subs->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $subs->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Mostrando
                            <span class="font-medium">{{ $subs->firstItem() }}</span>
                            a
                            <span class="font-medium">{{ $subs->lastItem() }}</span>
                            de
                            <span class="font-medium">{{ $subs->total() }}</span>
                            resultados
                        </p>
                    </div>
                    <div>
                        {{ $subs->links() }}
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