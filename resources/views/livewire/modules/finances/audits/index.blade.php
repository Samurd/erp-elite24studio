<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Auditorías</h1>
                <p class="text-gray-600 mt-1">Gestión de auditorías internas y externas</p>
            </div>
            <div>
                <a href="{{ route('finances.audits.create') }}"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nueva Auditoría
                </a>
            </div>
        </div>
    </div>

    @if(count($auditTypes) > 0)
        <!-- Tabs & Content -->
        <div x-data="{ activeTab: @entangle('activeTab') }">
            <!-- Tabs Navigation -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                    @foreach($auditTypes as $type)
                        <button @click="activeTab = '{{ $type['slug'] }}'; $wire.setActiveTab('{{ $type['slug'] }}')"
                            :class="{ 'border-green-500 text-green-600': activeTab === '{{ $type['slug'] }}', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== '{{ $type['slug'] }}' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                            {{ $type['name'] }}
                        </button>
                    @endforeach
                </nav>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="col-span-full">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Buscar por objetivo o lugar..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
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
                                    Fecha Auditoría
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Objetivo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Lugar
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
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
                            @forelse($audits as $audit)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $audit->date_audit->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ \App\Services\MoneyFormatterService::format($audit->objective) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $audit->place ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($audit->status)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                style="background-color: {{ $audit->status->color }}20; color: {{ $audit->status->color }}">
                                                {{ $audit->status->name }}
                                            </span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                -
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $audit->files->count() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('finances.audits.show', $audit->id) }}"
                                                class="text-green-600 hover:text-green-900" title="Ver">
                                                <i class="fa-solid fa-eye mr-1"></i> Ver
                                            </a>
                                            <a href="{{ route('finances.audits.edit', $audit->id) }}"
                                                class="text-blue-600 hover:text-blue-900" title="Editar">
                                                <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                            </a>
                                            <button wire:click="delete({{ $audit->id }})"
                                                wire:confirm="¿Estás seguro de que quieres eliminar esta auditoría?"
                                                class="text-red-600 hover:text-red-900" title="Eliminar">
                                                <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron auditorías para este tipo
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($audits && $audits->hasPages())
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            {{ $audits->links() }}
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando
                                    <span class="font-medium">{{ $audits->firstItem() }}</span>
                                    a
                                    <span class="font-medium">{{ $audits->lastItem() }}</span>
                                    de
                                    <span class="font-medium">{{ $audits->total() }}</span>
                                    resultados
                                </p>
                            </div>
                            <div>
                                {{ $audits->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-lock text-6xl"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Acceso Restringido</h2>
            <p class="text-gray-600">No tienes permisos para ver ningún tipo de auditoría.</p>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 mt-4">
            {{ session('success') }}
        </div>
    @endif
</div>