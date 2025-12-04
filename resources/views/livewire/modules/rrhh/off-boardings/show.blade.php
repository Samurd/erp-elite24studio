<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detalle de OffBoarding</h1>
                <p class="text-gray-600 mt-1">Información completa del proceso de salida</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('rrhh.offboardings.edit', $offboarding->id) }}"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('rrhh.offboardings.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- OffBoarding Information -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Información del OffBoarding</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Empleado</label>
                <p class="text-gray-900 font-medium">{{ $offboarding->employee?->full_name ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Proyecto</label>
                <p class="text-gray-900">{{ $offboarding->project?->name ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Salida</label>
                <p class="text-gray-900">
                    {{ $offboarding->exit_date ? \Carbon\Carbon::parse($offboarding->exit_date)->format('d/m/Y') : '-' }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                @if($offboarding->status)
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        {{ $offboarding->status->name }}
                    </span>
                @else
                    <p class="text-gray-500">-</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Responsable del Proceso</label>
                <p class="text-gray-900">{{ $offboarding->responsible?->name ?? '-' }}</p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Motivo de Salida</label>
                <p class="text-gray-900">{{ $offboarding->reason ?? '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Tasks Section -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Tareas del Proceso</h2>
            <div class="text-sm text-gray-600">
                @php
                    $completedCount = $offboarding->tasks->where('completed', true)->count();
                    $totalCount = $offboarding->tasks->count();
                @endphp
                <span class="font-semibold">{{ $completedCount }}/{{ $totalCount }}</span> completadas
            </div>
        </div>

        <!-- Add New Task Form -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Agregar Nueva Tarea</h3>
            <form wire:submit.prevent="addTask">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <input type="text" wire:model="newTaskContent" 
                            placeholder="Descripción de la tarea..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('newTaskContent') border-red-500 @enderror">
                        @error('newTaskContent')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <select wire:model="newTaskTeamId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 @error('newTaskTeamId') border-red-500 @enderror">
                            <option value="">Equipo (opcional)</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('newTaskTeamId')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Agregar Tarea
                    </button>
                </div>
            </form>
        </div>

        <!-- Tasks List -->
        <div class="space-y-3">
            @forelse($offboarding->tasks as $task)
                <div class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <!-- Checkbox -->
                    <div class="flex-shrink-0 pt-1">
                        <input type="checkbox" 
                            wire:click="toggleTask({{ $task->id }})"
                            {{ $task->completed ? 'checked' : '' }}
                            class="w-5 h-5 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500 cursor-pointer">
                    </div>

                    <!-- Task Content -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 {{ $task->completed ? 'line-through text-gray-500' : '' }}">
                            {{ $task->content }}
                        </p>
                        
                        <div class="flex items-center space-x-4 mt-2">
                            @if($task->team)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-users mr-1"></i>{{ $task->team->name }}
                                </span>
                            @endif

                            @if($task->completed)
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Completada por {{ $task->completedBy?->name ?? 'Usuario' }}
                                    el {{ $task->completed_at ? \Carbon\Carbon::parse($task->completed_at)->format('d/m/Y H:i') : '' }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>Pendiente
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Delete Button -->
                    <div class="flex-shrink-0">
                        <button wire:click="deleteTask({{ $task->id }})"
                            wire:confirm="¿Estás seguro de eliminar esta tarea?"
                            class="text-red-600 hover:text-red-900 p-2" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-tasks text-4xl mb-3"></i>
                    <p>No hay tareas registradas</p>
                    <p class="text-sm">Agrega la primera tarea usando el formulario de arriba</p>
                </div>
            @endforelse
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mt-4">
            {{ session('success') }}
        </div>
    @endif
</div>
