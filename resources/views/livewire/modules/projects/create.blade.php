<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ isset($project) ? 'Editar Proyecto' : 'Nuevo Proyecto' }}
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ isset($project) ? 'Actualiza la información del proyecto' : 'Complete los datos para registrar un nuevo proyecto' }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('projects.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Nombre del Proyecto -->
                <div class="lg:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Proyecto</label>
                    <input type="text" id="name" wire:model="form.name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Nombre del proyecto">
                    @error('form.name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Contacto/Cliente -->
                <div>
                    <label for="contact_id"
                        class="block text-sm font-medium text-gray-700 mb-2">Contacto/Cliente</label>
                    <select id="contact_id" wire:model="form.contact_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar contacto</option>
                        @foreach($contacts as $contact)
                            <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                        @endforeach
                    </select>
                    @error('form.contact_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tipo de Proyecto -->
                <div>
                    <label for="project_type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de
                        Proyecto</label>
                    <select id="project_type_id" wire:model="form.project_type_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar tipo</option>
                        @foreach($projectTypeOptions as $projectType)
                            <option value="{{ $projectType->id }}">{{ $projectType->name }}</option>
                        @endforeach
                    </select>
                    @error('form.project_type_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="status_id" wire:model="form.status_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar estado</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('form.status_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                @if(!isset($project))
                    <!-- Etapa Inicial (solo para crear) -->
                    <div>
                        <label for="initial_stage_id" class="block text-sm font-medium text-gray-700 mb-2">Etapa
                            Inicial</label>
                        <select id="initial_stage_id" wire:model="form.initial_stage_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar etapa inicial</option>
                            @foreach($stages as $stage)
                                <option value="{{ $stage->id ?? $stage['id'] }}">{{ $stage->name ?? $stage['name'] }}</option>
                            @endforeach
                        </select>
                        @error('form.initial_stage_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Esta etapa se establecerá como la etapa actual del proyecto
                            después de crearlo</p>
                    </div>
                @else
                    <!-- Etapa Actual (solo para editar) -->
                    <div>
                        <label for="current_stage_id" class="block text-sm font-medium text-gray-700 mb-2">Etapa
                            Actual</label>
                        <select id="current_stage_id" wire:model="form.current_stage_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar etapa</option>
                            @foreach($stages as $stage)
                                <option value="{{ $stage->id ?? $stage['id'] }}">{{ $stage->name ?? $stage['name'] }}</option>
                            @endforeach
                        </select>
                        @error('form.current_stage_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                @endif


                <!-- Responsable -->
                <div>
                    <label for="responsible_id" class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                    <select id="responsible_id" wire:model="form.responsible_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar responsable</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('form.responsible_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Equipo -->
                <div>
                    <label for="team_id" class="block text-sm font-medium text-gray-700 mb-2">Equipo</label>
                    <select id="team_id" wire:model="form.team_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Sin equipo asignado</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    @error('form.team_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Los planes de este proyecto heredarán el equipo seleccionado
                    </p>
                </div>

                <!-- Dirección -->
                <div class="lg:col-span-3">
                    <label for="direction" class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                    <input type="text" id="direction" wire:model="form.direction"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Dirección del proyecto">
                    @error('form.direction')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Descripción -->
            <div class="grid grid-cols-1 gap-6 mt-6">
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea id="description" wire:model="form.description" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Descripción detallada del proyecto..."></textarea>
                    @error('form.description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

                        @if(isset($project))
                            @livewire('modules.cloud.components.model-attachments', [
                                'model' => $project,
                                'area' => 'proyectos'
                            ])
                        @else
                            @livewire('modules.cloud.components.model-attachments-creator', [
                                'modelClass' => \App\Models\Project::class,
                                'areaSlug' => 'proyectos'
                            ])
                        @endif

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <a href="{{ route('projects.index') }}"
                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
                <button type="submit"
                    class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ isset($project) ? 'Actualizar' : 'Guardar' }} Proyecto
                </button>
            </div>
        </form>
    </div>

    <!-- Gestión de Etapas (Separado del formulario principal) -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Gestión de Etapas</h2>
            <button wire:click="toggleStageForm"
                class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Nueva Etapa
            </button>
        </div>

        <!-- Formulario de Nueva Etapa -->
        @if($showStageForm)
            <div class="border border-gray-200 rounded-lg p-4 mb-4">
                <form wire:submit.prevent="saveStage">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="stage_name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la
                                Etapa</label>
                            <input type="text" id="stage_name" wire:model="stage_name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                placeholder="Nombre de la etapa">
                            @error('stage_name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mt-4">
                        <div>
                            <label for="stage_description"
                                class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                            <textarea id="stage_description" wire:model="stage_description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                placeholder="Descripción de la etapa..."></textarea>
                            @error('stage_description')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" wire:click="cancelStage"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="submit"
                            class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>Guardar Etapa
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Lista de Etapas -->
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
                            Descripción
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- Etapas existentes --}}
                    @forelse($existingStages as $stage)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    #{{ $stage->id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $stage->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $stage->description ? Str::limit($stage->description, 50) : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button wire:click="editStage({{ $stage->id }})"
                                        class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </button>
                                    <button wire:click="deleteStage({{ $stage->id }})"
                                        wire:confirm="¿Estás seguro de que quieres eliminar esta etapa existente? Esta acción no se puede deshacer."
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse

                    {{-- Etapas temporales --}}
                    @forelse($temporaryStages as $stage)
                        <tr class="hover:bg-gray-50 bg-yellow-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $stage['id'] }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $stage['name'] }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $stage['description'] ? Str::limit($stage['description'], 50) : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button wire:click="editStage('{{ $stage['id'] }}')"
                                        class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </button>
                                    <button wire:click="deleteStage('{{ $stage['id'] }}')"
                                        wire:confirm="¿Estás seguro de que quieres eliminar esta etapa temporal?"
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                    @endforelse

                    @if($existingStages->count() == 0 && $temporaryStages->count() == 0)
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron etapas. Agrega una nueva etapa para comenzar.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
</div>