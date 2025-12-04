<!-- MODAL CREATE -->

<div x-data="{ open: false, isLoading: false }" x-on:open-task-modal.window="open = true; isLoading = true"
    x-on:close-task-modal.window="open = false" x-on:task-loaded.window="isLoading = false" x-cloak wire:ignore.self>
    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" @click.outside="open = false" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center gap-2 border-b pb-4 mb-4"
                                id="modal-title">
                                <div>
                                    <h2 class="text-lg font-semibold">Tarea</h2>
                                    <p class="text-sm text-gray-500">{{ $isEdit ? 'Editar tarea' : 'Crear tarea' }}
                                        {{ $bucket?->name ? 'en ' . $bucket->name : '' }}
                                    </p>
                                </div>
                            </h3>

                            <div class="mt-4">
                                <!-- Loading State (Alpine + Livewire) -->
                                <div x-show="isLoading" class="w-full text-center py-10">
                                    <svg class="animate-spin h-8 w-8 text-yellow-500 mx-auto"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <p class="mt-2 text-gray-500">Cargando información...</p>
                                </div>

                                <!-- Form Content -->
                                <div x-show="!isLoading" class="overflow-y-auto p-1 space-y-4 text-sm max-h-[70vh]">

                                    <!-- Nombre de la solicitud -->
                                    <div>
                                        <label for="name" class="block font-medium mb-1">Titulo</label>
                                        <input id="name" type="text" wire:model="form.title"
                                            placeholder="Usa un nombre que sea fácil de entender"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-700 focus:border-yellow-700" />

                                        @error('form.title')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Estado -->

                                    <div>
                                        <label for="status_id" class="block font-medium mb-1">Estado</label>
                                        <select id="status_id" wire:model="form.status_id"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                                            <option value="">Selecciona un estado</option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('form.status_id') <span
                                            class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <!-- Prioridad -->

                                    <div>
                                        <label for="priority_id" class="block font-medium mb-1">Prioridad</label>
                                        <select id="priority_id" wire:model="form.priority_id"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                                            <option value="">Selecciona una prioridad</option>
                                            @foreach ($priorities as $priority)
                                                <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('form.priority_id') <span
                                            class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    @if (isset($bucket->plan->team_id))
                                        <div>
                                            <label class="block font-medium mb-1">Asignado</label>

                                            @if(count($this->users) > 0)
                                                <select wire:model="form.assignedUsers" multiple
                                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-700 focus:border-yellow-700 mb-2">
                                                    @foreach($this->users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Puedes seleccionar múltiples usuarios usando Ctrl/Cmd+Click
                                                </p>
                                            @else
                                                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                                                    <p class="text-sm text-yellow-800">
                                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                                        El equipo no tiene miembros asignados. Agrega usuarios al equipo para
                                                        poder asignar tareas.
                                                    </p>
                                                </div>
                                            @endif

                                            @error('form.assignedUsers') <span
                                            class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    @else
                                        <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                            <p class="text-sm text-blue-800 mb-2">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                <strong>No se puede asignar usuarios</strong>
                                            </p>
                                            <p class="text-xs text-blue-700">
                                                @if(isset($bucket->plan->project_id))
                                                    Este plan pertenece a un proyecto sin equipo. Para asignar tareas, edita el
                                                    proyecto y agrégale un
                                                    equipo con usuarios.
                                                @else
                                                    Este es un plan personal. Para asignar tareas a otros usuarios, crea un plan
                                                    asociado a un equipo.
                                                @endif
                                            </p>
                                        </div>
                                    @endif


                                    <!-- start date -->
                                    <div class="mb-3">
                                        <label for="start_date" class="block font-semibold">Fecha de Inicio</label>
                                        <x-input id="start_date" type="date" name="start_date" class="w-full"
                                            wire:model="form.start_date" />

                                        @error('form.start_date') <span
                                        class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- due date -->
                                    <div class="mb-3">
                                        <label for="due_date" class="block font-semibold">Fecha de Vencimiento</label>
                                        <x-input id="due_date" type="date" name="due_date" class="w-full"
                                            wire:model="form.due_date" />

                                        @error('form.due_date') <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <div>
                                        <label for="description" class="block font-medium mb-1">Notas</label>
                                        <textarea id="description" rows="3" wire:model="form.notes"
                                            placeholder="Notas de la tarea"
                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-700 focus:border-yellow-700"></textarea>

                                        @error('form.notes')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-button x-on:click="$wire.save()"
                        class="bg-yellow-700 hover:bg-yellow-800 text-white ml-2 sm:w-auto w-full">
                        {{ $isEdit ? 'Actualizar' : 'Crear' }} </x-button>
                    <x-button @click="open = false"
                        class="bg-gray-600 hover:bg-gray-700 text-white mt-3 sm:mt-0 sm:w-auto w-full"> Cerrar
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</div>