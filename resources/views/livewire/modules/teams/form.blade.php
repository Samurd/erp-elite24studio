<!-- Layout estilo Slack/Microsoft Teams -->
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar izquierda -->
    <div class="w-64 bg-gray-900 text-white flex flex-col">
        <!-- Header -->
        <div class="p-4 border-b border-gray-800">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold flex items-center">
                    <i class="fas fa-users mr-2"></i>
                    {{ $isEditing ? 'Editar Equipo' : 'Nuevo Equipo' }}
                </h1>
            </div>
        </div>

        <!-- Información -->
        <div class="p-4 border-b border-gray-800">
            <div class="text-sm text-gray-400">
                <p class="mb-2">
                    {{ $isEditing ? 'Actualiza la información del equipo' : 'Crea un nuevo equipo para colaborar' }}
                </p>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-info-circle"></i>
                    <span>Los equipos públicos son visibles para todos</span>
                </div>
            </div>
        </div>

        <!-- Acciones rápidas -->
        <div class="p-4">
            <a href="{{ route('teams.index') }}"
                class="flex items-center space-x-2 text-gray-300 hover:text-white transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Volver a equipos</span>
            </a>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="flex-1 overflow-hidden">
        <!-- Header superior -->
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $isEditing ? 'Editar Equipo' : 'Crear Nuevo Equipo' }}
                    </h2>
                    <p class="text-gray-600 mt-1">
                        {{ $isEditing ? 'Modifica los datos del equipo' : 'Completa el formulario para crear un nuevo equipo' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="p-6 overflow-y-auto" style="height: calc(100vh - 88px);">
            <form wire:submit.prevent="save">
                <!-- Información básica -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Información del Equipo</h3>
                            <p class="text-sm text-gray-600">Datos básicos del equipo</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre del equipo -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre del Equipo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" wire:model="name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                placeholder="Ej: Equipo de Desarrollo, Marketing, etc.">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción
                            </label>
                            <textarea id="description" wire:model="description" rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent resize-none"
                                placeholder="Describe el propósito y actividades del equipo..."></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo de equipo -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Tipo de Equipo
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Opción Público -->
                                <label class="relative cursor-pointer">
                                    <input type="radio" wire:model.live="isPublic" value="1" class="peer sr-only">
                                    <div
                                        class="p-4 border-2 rounded-lg transition-all peer-checked:border-yellow-600 peer-checked:bg-yellow-50 hover:bg-gray-50">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <div
                                                class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-globe text-green-600"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h5 class="font-semibold text-gray-900">Público</h5>
                                            </div>
                                            <div
                                                class="w-5 h-5 border-2 rounded-full peer-checked:border-yellow-600 peer-checked:bg-yellow-600 flex items-center justify-center">
                                                <i
                                                    class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-600">
                                            Visible para todos los usuarios de la plataforma
                                        </p>
                                    </div>
                                </label>

                                <!-- Opción Privado -->
                                <label class="relative cursor-pointer">
                                    <input type="radio" wire:model.live="isPublic" value="0" class="peer sr-only">
                                    <div
                                        class="p-4 border-2 rounded-lg transition-all peer-checked:border-yellow-600 peer-checked:bg-yellow-50 hover:bg-gray-50">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <div
                                                class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-lock text-gray-600"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h5 class="font-semibold text-gray-900">Privado</h5>
                                            </div>
                                            <div
                                                class="w-5 h-5 border-2 rounded-full peer-checked:border-yellow-600 peer-checked:bg-yellow-600 flex items-center justify-center">
                                                <i
                                                    class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-600">
                                            Solo visible para los miembros del equipo
                                        </p>
                                    </div>
                                </label>
                            </div>
                            @error('isPublic')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Miembros del equipo (Solo para equipos privados) -->
                <div x-data="{ showMembers: @entangle('isPublic').live }"
                    x-show="showMembers == 0 || showMembers == '0' || showMembers === false"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-4"
                    class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center mb-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user-friends text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Miembros del Equipo</h3>
                            <p class="text-sm text-gray-600">Selecciona los miembros que formarán parte del equipo
                                privado</p>
                        </div>
                    </div>

                    <div class="space-y-3 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-4">
                        <!-- Mensaje informativo -->
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium">El creador del equipo se agrega automáticamente</p>
                                    <p>Tú {{ Auth::user()->name }} serás miembro de este equipo por defecto.</p>
                                </div>
                            </div>
                        </div>

                        @forelse($users as $user)
                            <label
                                class="flex items-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                <input type="checkbox" wire:model="selectedMembers" value="{{ $user->id }}"
                                    class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                                <div class="ml-3 flex items-center flex-1">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-yellow-600 font-medium text-sm">
                                            {{ substr($user->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-users text-4xl mb-3"></i>
                                <p>No hay usuarios disponibles</p>
                            </div>
                        @endforelse
                    </div>

                    @if($users->isNotEmpty())
                        <div class="mt-3 text-sm text-gray-600">
                            <span class="font-medium">{{ count($selectedMembers) }}</span>
                            {{ count($selectedMembers) == 1 ? 'miembro seleccionado' : 'miembros seleccionados' }}
                            de {{ $users->count() }} disponibles
                        </div>
                    @endif

                    @error('selectedMembers')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('teams.index') }}"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        Cancelar
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-medium flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        {{ $isEditing ? 'Actualizar Equipo' : 'Crear Equipo' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>