<div>
    <!-- Department Management Modal -->
    @livewire('components.department-management-modal')
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Empleados</h1>
                <p class="text-gray-600 mt-1">Gestión de empleados del sistema</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('rrhh.employees.create') }}"
                    class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nuevo Empleado
                </a>
            </div>
        </div>
    </div>

    <!-- Department Tabs Sidebar -->
    <div class="flex gap-6">
        <!-- Sidebar Tabs -->
        <div class="w-64 bg-white rounded-lg shadow-sm p-4 h-fit">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-800">Departamentos</h3>
                <button wire:click="openDepartmentModal"
                    class="bg-purple-600 text-white px-3 py-1 rounded-md hover:bg-purple-700 transition-colors text-sm">
                    <i class="fas fa-plus mr-1"></i>Nuevo
                </button>
            </div>
            <div class="space-y-2">
                <!-- Tab "Todos" -->
                <button wire:click="setDepartmentFilter('')"
                    class="w-full text-left px-3 py-2 rounded-lg transition-colors {{ $departmentFilter === '' ? 'bg-purple-100 text-purple-700 font-medium' : 'hover:bg-gray-100 text-gray-700' }}">
                    <i class="fas fa-users mr-2"></i>Todos
                    <span class="float-right text-sm text-gray-500">
                        {{ $totalEmployees }}
                    </span>
                </button>

                <!-- Department Tabs with Dropdowns -->
                @foreach($departments as $department)
                    <div class="relative" x-data="{ departmentDropdownOpen: false }">
                        <div class="flex items-center">
                            <button wire:click="setDepartmentFilter({{ $department->id }})"
                                class="flex-1 text-left px-3 py-2 rounded-l-lg transition-colors {{ $departmentFilter == $department->id ? 'bg-purple-100 text-purple-700 font-medium' : 'hover:bg-gray-100 text-gray-700' }}">
                                <i class="fas fa-building mr-2"></i>{{ $department->name }}
                                <span class="float-right text-sm text-gray-500">
                                    {{ $department->employees_count ?? 0 }}
                                </span>
                            </button>
                            <button @click="departmentDropdownOpen = !departmentDropdownOpen"
                                class="px-2 py-2 rounded-r-lg border-l border-gray-200 {{ $departmentFilter == $department->id ? 'bg-purple-100 text-purple-700' : 'hover:bg-gray-100 text-gray-700' }}">
                                <x-clarity-menu-line class="w-4" />
                            </button>
                        </div>

                        <!-- Dropdown Menu -->
                        <div x-show="departmentDropdownOpen" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="absolute left-0 mt-1 w-48 bg-white rounded-lg shadow-lg z-10 border border-gray-200"
                            @click.away="departmentDropdownOpen = false">
                            <div class="py-1">
                                <button wire:click="editDepartment({{ $department->id }}); departmentDropdownOpen = false"
                                    class="w-full text-left px-3 py-2 hover:bg-gray-100 text-gray-700 flex items-center">
                                    <i class="fas fa-edit mr-2 text-blue-600"></i>Editar Departamento
                                </button>
                                <button wire:click="deleteDepartment({{ $department->id }}); departmentDropdownOpen = false"
                                    class="w-full text-left px-3 py-2 hover:bg-gray-100 text-red-600 flex items-center">
                                    <i class="fas fa-trash mr-2"></i>Eliminar Departamento
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            @if (session()->has('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Búsqueda general -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Nombre, email, cédula, teléfono..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <!-- Filtro por cargo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cargo</label>
                        <input type="text" wire:model.live.debounce.300ms="jobTitleFilter"
                            placeholder="Filtrar por cargo..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <!-- Filtro por género -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Género</label>
                        <select wire:model.live="genderFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Todos</option>
                            @foreach($genders as $gender)
                                <option value="{{ $gender->id }}">{{ $gender->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro por tipo de educación -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Educación</label>
                        <select wire:model.live="educationTypeFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Todos</option>
                            @foreach($educationTypes as $educationType)
                                <option value="{{ $educationType->id }}">{{ $educationType->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro por estado civil -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado Civil</label>
                        <select wire:model.live="maritalStatusFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Todos</option>
                            @foreach($maritalStatuses as $maritalStatus)
                                <option value="{{ $maritalStatus->id }}">{{ $maritalStatus->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro por departamento -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Departamento</label>
                        <select wire:model.live="departmentFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Todos</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Registros por página -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Registros por página</label>
                        <select wire:model.live="perPage"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
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

            <!-- Tabla de empleados -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Empleado
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Contacto
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Información Laboral
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Personal
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($employees as $employee)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                    <span class="text-purple-600 font-medium">
                                                        {{ substr($employee->full_name, 0, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $employee->formatted_full_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    ID: {{ $employee->identification_number }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $employee->work_email }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->mobile_phone }}</div>
                                        @if($employee->personal_email)
                                            <div class="text-sm text-gray-500">{{ $employee->personal_email }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $employee->job_title }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->work_schedule }}</div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs">
                                            {{ $employee->work_address }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($employee->gender)
                                                {{ $employee->gender->name }}
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            @if($employee->marital_status)
                                                {{ $employee->marital_status->name }}
                                            @endif
                                        </div>
                                        @if($employee->birth_date)
                                            <div class="text-sm text-gray-500">
                                                {{ $employee->birth_date->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button class="text-purple-600 hover:text-purple-900" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('rrhh.employees.edit', $employee->id) }}"
                                                class="text-blue-600 hover:text-blue-900" title="Editar">
                                                <i class="fa-solid fa-pen-to-square mr-2"></i> Editar
                                            </a>
                                            <button wire:click="delete({{ $employee->id }})"
                                                wire:confirm="¿Estás seguro de que deseas eliminar este empleado?"
                                                class="text-red-600 hover:text-red-900" title="Eliminar">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron empleados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($employees->hasPages())
                        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                            <div class="flex-1 flex justify-between sm:hidden">
                                {{ $employees->links() }}
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Mostrando
                                        <span class="font-medium">{{ $employees->firstItem() }}</span>
                                        a
                                        <span class="font-medium">{{ $employees->lastItem() }}</span>
                                        de
                                        <span class="font-medium">{{ $employees->total() }}</span>
                                        resultados
                                    </p>
                                </div>
                                <div>
                                    {{ $employees->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
        </div>
    </div>


</div>