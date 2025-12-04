<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Vacantes</h1>
                <p class="text-gray-600 mt-1">Administración de vacantes y postulantes</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('rrhh.vacancies.create') }}"
                   class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nueva Vacante
                </a>
                <a href="{{ route('rrhh.applicants.create') }}"
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>Nuevo Postulante
                </a>
            </div>
        </div>
    </div>

    <!-- Tabs Principales -->
    <div class="bg-white rounded-lg shadow-sm mb-6" x-data="{ activeTab: 'vacancies' }">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button @click="activeTab = 'vacancies'"
                    :class="activeTab === 'vacancies'
                        ?
                        'border-purple-500 text-purple-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-briefcase mr-2"></i>Vacantes
                </button>
                <button @click="activeTab = 'applicants'"
                    :class="activeTab === 'applicants'
                        ?
                        'border-purple-500 text-purple-600' :
                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-users mr-2"></i>Postulantes
                </button>
            </nav>
        </div>

        <!-- Tab de Vacantes -->
        <div x-show="activeTab === 'vacancies'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <!-- Filtros de Vacantes -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Búsqueda general -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                        <input type="text" wire:model.live.debounce.300ms="vacancySearch"
                            placeholder="Título, área, descripción..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <!-- Filtro por tipo de contrato -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Contrato</label>
                        <select wire:model.live="contractTypeFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Todos</option>
                            @foreach ($contractTypes as $contractType)
                                <option value="{{ $contractType->id }}">{{ $contractType->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro por estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select wire:model.live="statusFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Todos</option>
                            @foreach ($vacancyStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Registros por página -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Registros por página</label>
                        <select wire:model.live="vacancyPerPage"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    <!-- Botón limpiar filtros -->
                    <div class="flex items-end">
                        <button wire:click="clearVacancyFilters"
                            class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-times mr-2"></i>Limpiar Filtros
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla de Vacantes -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Vacante
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Área
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo de Contrato
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha de Publicación
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    N° Postulantes
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Responsable
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($vacancies as $vacancy)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $vacancy->title }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $vacancy->area ?? 'No especificada' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if ($vacancy->contractType)
                                                {{ $vacancy->contractType->name }}
                                            @else
                                                No especificado
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if ($vacancy->published_at)
                                                {{ Carbon\Carbon::parse($vacancy->published_at)->format('d/m/Y') }}
                                            @else
                                                No publicada
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $vacancy->applicants_count ?? 0 }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if ($vacancy->status)
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $vacancy->status->name === 'Activa'
                                                    ? 'bg-green-100 text-green-800'
                                                    : ($vacancy->status->name === 'Cerrada'
                                                        ? 'bg-red-100 text-red-800'
                                                        : 'bg-gray-100 text-gray-800') }}">
                                                    {{ $vacancy->status->name }}
                                                </span>
                                            @else
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Sin estado
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if ($vacancy->user)
                                                {{ $vacancy->user->name }}
                                            @else
                                                Sin responsable
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button class="text-purple-600 hover:text-purple-900" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('rrhh.vacancies.edit', $vacancy->id) }}"
                                                class="text-blue-600 hover:text-blue-900" title="Editar">
                                                <i class="fa-solid fa-pen-to-square mr-2"></i> Editar
                                            </a>
                                            <button class="text-red-600 hover:text-red-900" title="Eliminar">
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron vacantes
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if ($vacancies->hasPages())
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            {{ $vacancies->links() }}
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando
                                    <span class="font-medium">{{ $vacancies->firstItem() }}</span>
                                    a
                                    <span class="font-medium">{{ $vacancies->lastItem() }}</span>
                                    de
                                    <span class="font-medium">{{ $vacancies->total() }}</span>
                                    resultados
                                </p>
                            </div>
                            <div>
                                {{ $vacancies->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab de Postulantes -->
        <div x-show="activeTab === 'applicants'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="flex gap-6">
                <!-- Sidebar Tabs de Vacantes -->
                <div class="w-64 bg-white rounded-lg shadow-sm p-4 h-fit">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-gray-800">Vacantes</h3>
                        <span class="text-sm text-gray-500">
                            {{ $totalApplicants }} total
                        </span>
                    </div>
                    <div class="space-y-2">
                        <!-- Tab "Todos" -->
                        <button wire:click="setVacancyFilter('')"
                            class="w-full text-left px-3 py-2 rounded-lg transition-colors {{ $vacancyFilter === '' ? 'bg-purple-100 text-purple-700 font-medium' : 'hover:bg-gray-100 text-gray-700' }}">
                            <i class="fas fa-users mr-2"></i>Todas las vacantes
                            <span class="float-right text-sm text-gray-500">
                                {{ $totalApplicants }}
                            </span>
                        </button>

                        <!-- Vacante Tabs -->
                        @foreach ($vacanciesForSidebar as $vacancy)
                            <button wire:click="setVacancyFilter({{ $vacancy->id }})"
                                class="w-full text-left px-3 py-2 rounded-lg transition-colors {{ $vacancyFilter == $vacancy->id ? 'bg-purple-100 text-purple-700 font-medium' : 'hover:bg-gray-100 text-gray-700' }}">
                                <i class="fas fa-briefcase mr-2"></i>{{ $vacancy->title }}
                                <span class="float-right text-sm text-gray-500">
                                    {{ $vacancy->applicants_count ?? 0 }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    <!-- Filtros de Postulantes -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Búsqueda general -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                                <input type="text" wire:model.live.debounce.300ms="applicantSearch"
                                    placeholder="Nombre, email, notas..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>

                            <!-- Filtro por estado -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                <select wire:model.live="applicantStatusFilter"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">Todos</option>
                                    @foreach ($applicantStatuses as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtro por vacante -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vacante</label>
                                <select wire:model.live="vacancyFilter"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">Todas</option>
                                    @foreach ($vacanciesForSidebar as $vacancy)
                                        <option value="{{ $vacancy->id }}">{{ $vacancy->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Registros por página -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Registros por
                                    página</label>
                                <select wire:model.live="applicantPerPage"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>

                            <!-- Botón limpiar filtros -->
                            <div class="flex items-end">
                                <button wire:click="clearApplicantFilters"
                                    class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                                    <i class="fas fa-times mr-2"></i>Limpiar Filtros
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex w-full justify-end mb-2">
                        <a href="{{ route('rrhh.applicants.create') }}"
                            class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Nuevo Postulante
                        </a>
                    </div>

                    <!-- Tabla de Postulantes -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Postulante
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Contacto
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Vacante
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($applicants as $applicant)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div
                                                            class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                            <span class="text-purple-600 font-medium">
                                                                {{ substr($applicant->full_name, 0, 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $applicant->full_name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $applicant->email }}</div>
                                                @if ($applicant->notes)
                                                    <div class="text-sm text-gray-500 truncate max-w-xs">
                                                        {{ $applicant->notes }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @if ($applicant->vacancy)
                                                        {{ $applicant->vacancy->title }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @if ($applicant->status)
                                                        <span
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $applicant->status->name === 'Aprobado'
                                                            ? 'bg-green-100 text-green-800'
                                                            : ($applicant->status->name === 'Rechazado'
                                                                ? 'bg-red-100 text-red-800'
                                                                : 'bg-yellow-100 text-yellow-800') }}">
                                                            {{ $applicant->status->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button class="text-purple-600 hover:text-purple-900"
                                                        title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="{{ route('rrhh.applicants.edit', $applicant->id) }}"
                                                       class="text-blue-600 hover:text-blue-900" title="Editar">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    <button class="text-red-600 hover:text-red-900" title="Eliminar">
                                                        Eliminar
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                                No se encontraron postulantes
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        @if ($applicants->hasPages())
                            <div
                                class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    {{ $applicants->links() }}
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Mostrando
                                            <span class="font-medium">{{ $applicants->firstItem() }}</span>
                                            a
                                            <span class="font-medium">{{ $applicants->lastItem() }}</span>
                                            de
                                            <span class="font-medium">{{ $applicants->total() }}</span>
                                            resultados
                                        </p>
                                    </div>
                                    <div>
                                        {{ $applicants->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
