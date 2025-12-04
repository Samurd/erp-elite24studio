<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detalles del Proyecto</h1>
                <p class="text-gray-600 mt-1">{{ $project->name }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('projects.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <a href="{{ route('projects.edit', $project->id) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm mb-6" x-data="{ activeTab: 'info' }">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button @click="activeTab = 'info'" :class="activeTab === 'info'
                        ? 'border-yellow-500 text-yellow-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-info-circle mr-2"></i>Información del Proyecto
                </button>
                <button @click="activeTab = 'plans'" :class="activeTab === 'plans'
                        ? 'border-yellow-500 text-yellow-600'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-tasks mr-2"></i>Planes ({{ $project->plans->count() }})
                </button>
            </nav>
        </div>

        <!-- Tab: Información del Proyecto -->
        <div x-show="activeTab === 'info'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <!-- Project Details -->
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Información General</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">ID</label>
                        <p class="text-lg text-gray-900">#{{ $project->id }}</p>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nombre del Proyecto</label>
                        <p class="text-lg text-gray-900">{{ $project->name }}</p>
                    </div>

                    <div class="lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Descripción</label>
                        <p class="text-lg text-gray-900">{{ $project->description ?? 'No especificada' }}</p>
                    </div>

                    <div class="lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Dirección</label>
                        <p class="text-lg text-gray-900">{{ $project->direction ?? 'No especificada' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Contacto/Cliente</label>
                        <p class="text-lg text-gray-900">
                            @if($project->contact)
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    {{ $project->contact->name }}
                                </span>
                            @else
                                <span class="text-gray-500">No asignado</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tipo de Proyecto</label>
                        <p class="text-lg text-gray-900">
                            @if($project->projectType)
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $project->projectType->name }}
                                </span>
                            @else
                                <span class="text-gray-500">No asignado</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                        <p class="text-lg text-gray-900">
                            @if($project->status)
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                                                                        @if($project->status->name == 'Activo') bg-green-100 text-green-800
                                                                        @elseif($project->status->name == 'En Progreso') bg-yellow-100 text-yellow-800
                                                                        @elseif($project->status->name == 'Completado') bg-blue-100 text-blue-800
                                                                        @elseif($project->status->name == 'Pausado') bg-orange-100 text-orange-800
                                                                        @elseif($project->status->name == 'Cancelado') bg-red-100 text-red-800
                                                                        @else bg-gray-100 text-gray-800 @endif
                                                                    ">
                                    {{ $project->status->name }}
                                </span>
                            @else
                                <span class="text-gray-500">No asignado</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Etapa Actual</label>
                        <p class="text-lg text-gray-900">
                            @if($project->currentStage)
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ $project->currentStage->name }}
                                </span>
                            @else
                                <span class="text-gray-500">No asignada</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Responsable</label>
                        <p class="text-lg text-gray-900">
                            @if($project->responsible)
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-teal-100 text-teal-800">
                                    {{ $project->responsible->name }}
                                </span>
                            @else
                                <span class="text-gray-500">No asignado</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Equipo</label>
                        <p class="text-lg text-gray-900">
                            @if($project->team)
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-users mr-2"></i>{{ $project->team->name }}
                                </span>
                            @else
                                <span class="text-gray-500">Equipo no asignado</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Creación</label>
                        <p class="text-lg text-gray-900">
                            {{ $project->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Última Actualización</label>
                        <p class="text-lg text-gray-900">
                            {{ $project->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stages Section -->
            @if($project->stages->isNotEmpty())
                <div class="border-t border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Etapas del Proyecto</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nombre
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Descripción
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Orden
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($project->stages->sortBy('order') as $stage)
                                    <tr
                                        class="hover:bg-gray-50 {{ $project->current_stage_id == $stage->id ? 'bg-purple-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $stage->name }}
                                                @if($project->current_stage_id == $stage->id)
                                                    <span
                                                        class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        Actual
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ $stage->description ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $stage->order }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Files Section -->
            <livewire:modules.cloud.components.model-files :model="$project" />
        </div>

        <!-- Tab: Planes del Proyecto -->
        <div x-show="activeTab === 'plans'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            <!-- Livewire Planner Content Component -->
            @livewire('modules.projects.planner-content', ['projectId' => $project->id])
        </div>
    </div>

    <!-- Modals for Plans and Tasks -->
    @livewire('modules.planner.components.plans.create-or-update')
    @livewire('modules.planner.components.tasks.create-or-update')

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
</div>