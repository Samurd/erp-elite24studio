<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Certificados</h1>
                <p class="text-gray-600 mt-1">Gestión de certificados del sistema</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('certificates.create') }}"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nuevo Certificado
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
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nombre o descripción..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>

            <!-- Filtro por tipo de certificado -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Certificado</label>
                <select wire:model.live="type_filter"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <option value="">Todos</option>
                    @foreach($typeOptions as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
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

            <!-- Filtro por asignado a -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Asignado a</label>
                <select wire:model.live="assigned_to_filter"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    <option value="">Todos</option>
                    @foreach($userOptions as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha desde -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Emisión Desde</label>
                <input type="date" wire:model.live="date_from"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>

            <!-- Fecha hasta -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Emisión Hasta</label>
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
                            Tipo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha Emisión
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha Vencimiento
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Asignado a
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
                    @forelse($certificates as $certificate)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    #{{ $certificate->id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $certificate->name }}
                                </div>
                                @if($certificate->description)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ Str::limit($certificate->description, 50) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($certificate->type)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $certificate->type->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($certificate->issued_at)
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($certificate->issued_at)->format('d/m/Y') }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($certificate->expires_at)
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($certificate->expires_at)->format('d/m/Y') }}
                                    </div>
                                    @if(\Carbon\Carbon::parse($certificate->expires_at)->isPast())
                                        <div class="text-xs text-red-600">Vencido</div>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($certificate->status)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                        @if($certificate->status->name == 'Activo') bg-green-100 text-green-800
                                                                        @elseif($certificate->status->name == 'Inactivo') bg-red-100 text-red-800
                                                                        @elseif($certificate->status->name == 'Pendiente') bg-yellow-100 text-yellow-800
                                                                        @elseif($certificate->status->name == 'En Proceso') bg-blue-100 text-blue-800
                                                                        @elseif($certificate->status->name == 'Vencido') bg-gray-100 text-gray-800
                                                                        @elseif($certificate->status->name == 'Prorrogado') bg-teal-100 text-teal-800
                                                                        @else bg-gray-100 text-gray-800 @endif
                                                                    ">
                                        {{ $certificate->status->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($certificate->assignedTo)
                                    <div class="text-sm text-gray-900">
                                        {{ $certificate->assignedTo->name }}
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($certificate->files->count() > 0)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $certificate->files->count() }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('certificates.show', $certificate->id) }}">
                                        <button class="text-blue-600 hover:text-blue-900" title="Ver más">
                                            <i class="fa-solid fa-eye mr-1"></i> Ver
                                        </button>
                                    </a>

                                    @canArea('update', 'certificados')
                                    <a href="{{ route('certificates.edit', $certificate->id) }}"
                                        class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </a>
                                    @endCanArea

                                    @canArea('delete', 'certificados')
                                    <button wire:click="delete({{ $certificate->id }})"
                                        wire:confirm="¿Estás seguro de que quieres eliminar este certificado?"
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                    @endCanArea
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron certificados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($certificates->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $certificates->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Mostrando
                            <span class="font-medium">{{ $certificates->firstItem() }}</span>
                            a
                            <span class="font-medium">{{ $certificates->lastItem() }}</span>
                            de
                            <span class="font-medium">{{ $certificates->total() }}</span>
                            resultados
                        </p>
                    </div>
                    <div>
                        {{ $certificates->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    @livewire("modules.certificates.components.delete-modal")
    <livewire:modules.certificates.components.info-modal />

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
</div>