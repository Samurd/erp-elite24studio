<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $isEditing ? 'Editar Canal' : 'Crear Nuevo Canal' }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Equipo: {{ $team->name }}
                    </p>
                </div>
                <a href="{{ route('teams.show', $team->id) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver al Equipo
                </a>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white shadow rounded-lg" x-data="{ isPrivate: @entangle('is_private') }">
            <form wire:submit="save" class="space-y-6 p-6">
                <!-- Nombre del canal -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nombre del Canal <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" id="name" wire:model="name"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                            placeholder="Ej: General, Proyectos, Marketing..." required>
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">
                        Descripción
                    </label>
                    <div class="mt-1">
                        <textarea id="description" wire:model="description" rows="4"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm"
                            placeholder="Describe el propósito de este canal..."></textarea>
                    </div>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de canal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        Tipo de Canal
                    </label>
                    <div class="space-y-3">
                        <!-- Canal Público -->
                        <label
                            class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-all"
                            :class="{ 'border-purple-500 bg-purple-50 ring-2 ring-purple-200': isPrivate == 0, 'border-gray-300': isPrivate != 0 }">
                            <input type="radio" x-model="isPrivate" value="0"
                                class="text-purple-600 focus:ring-purple-500 mr-4">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-hashtag text-blue-600"></i>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-900">Canal Público</span>
                                        <p class="text-sm text-gray-500 mt-1">Todos los miembros del equipo pueden ver y
                                            participar</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- Canal Privado -->
                        <label
                            class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-all"
                            :class="{ 'border-purple-500 bg-purple-50 ring-2 ring-purple-200': isPrivate == 1, 'border-gray-300': isPrivate != 1 }">
                            <input type="radio" x-model="isPrivate" value="1"
                                class="text-purple-600 focus:ring-purple-500 mr-4">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-lock text-gray-600"></i>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-900">Canal Privado</span>
                                        <p class="text-sm text-gray-500 mt-1">Solo los miembros invitados pueden ver y
                                            participar</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('is_private')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Selección de miembros (solo para canales privados) -->
                <div x-show="isPrivate == 1" x-transition class="border-t pt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        Miembros del Canal <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500 font-normal ml-2">
                            ({{ count($selectedMembers) }} seleccionados)
                        </span>
                    </label>
                    <p class="text-sm text-gray-500 mb-4">
                        Selecciona los miembros del equipo que tendrán acceso a este canal privado.
                    </p>

                    <!-- Nota sobre el creador -->
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-100 rounded-lg flex items-center">
                        <div
                            class="w-8 h-8 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold mr-3 flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Tú ({{ auth()->user()->name }})</p>
                            <p class="text-xs text-gray-500">Incluido automáticamente como creador/administrador</p>
                        </div>
                        <i class="fas fa-check-circle text-blue-500"></i>
                    </div>

                    <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg">
                        @forelse($teamMembers as $member)
                            <label
                                class="flex items-center p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                <input type="checkbox" wire:model.live="selectedMembers" value="{{ $member->id }}"
                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 mr-3">
                                <div class="flex items-center flex-1">
                                    <div
                                        class="w-8 h-8 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold mr-3">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $member->email }}</p>
                                    </div>
                                </div>
                                @if(in_array($member->id, $selectedMembers))
                                    <i class="fas fa-check-circle text-purple-600"></i>
                                @endif
                            </label>
                        @empty
                            <div class="p-4 text-center text-sm text-gray-500">
                                No hay miembros en el equipo
                            </div>
                        @endforelse
                    </div>
                    @error('selectedMembers')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($is_private && count($selectedMembers) === 0)
                        <p class="mt-2 text-sm text-amber-600">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Debes seleccionar al menos un miembro para el canal privado
                        </p>
                    @endif
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <button type="button" wire:click="cancel"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        {{ $isEditing ? 'Actualizar Canal' : 'Crear Canal' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>