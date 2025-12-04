<!-- Modal para crear/editar canal -->
<div x-data="{ open: false }" x-on:open-channel-modal.window="open = true" x-on:close-channel-modal.window="open = false"
     x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto bg-gray-500 bg-opacity-75" style="display: none;">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div x-show="open" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             @click.away="$dispatch('closeChannelModal')" class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl">
            
            <!-- Header del modal -->
            <div class="border-b border-gray-200 bg-white px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $isEditing ? 'Editar Canal' : 'Crear Nuevo Canal' }}
                    </h3>
                    <button type="button" @click="$dispatch('closeChannelModal')"
                            class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Formulario -->
            <form wire:submit="save" class="space-y-4 px-6 py-4">
                <!-- Nombre del canal -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Nombre del Canal <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" id="name" wire:model="name"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm"
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
                        <textarea id="description" wire:model="description" rows="3"
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm"
                                  placeholder="Describe el propósito de este canal..."></textarea>
                    </div>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de canal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Tipo de Canal
                    </label>
                    <div class="space-y-2">
                        <!-- Canal Público -->
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ !$is_private ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300' }}">
                            <input type="radio" name="channel_type" wire:model="is_private" :value="false" class="mr-3">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <i class="fas fa-hashtag text-blue-500 mr-2"></i>
                                    <span class="font-medium text-gray-900">Canal Público</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Todos los miembros del equipo pueden ver y participar</p>
                            </div>
                        </label>

                        <!-- Canal Privado -->
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 {{ $is_private ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300' }}">
                            <input type="radio" name="channel_type" wire:model="is_private" :value="true" class="mr-3">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <i class="fas fa-lock text-gray-500 mr-2"></i>
                                    <span class="font-medium text-gray-900">Canal Privado</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Solo los miembros invitados pueden ver y participar</p>
                            </div>
                        </label>
                    </div>
                    @error('is_private')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" @click="$dispatch('closeChannelModal')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                        {{ $isEditing ? 'Actualizar Canal' : 'Crear Canal' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
