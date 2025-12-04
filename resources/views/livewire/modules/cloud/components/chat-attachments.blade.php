<div class="relative" x-data="{ open: @entangle('showModal') }">
    <!-- Botón Clip -->
    <button type="button" @click="open = !open"
        class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors relative">
        <x-lucide-paperclip class="w-5 h-5" />

        <!-- Indicador de adjuntos -->
        @if(count($uploads) > 0 || count($pendingLinkFiles) > 0)
            <span class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white bg-red-500"></span>
        @endif
    </button>

    <!-- Popover de Adjuntos -->
    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="absolute bottom-full left-0 mb-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden">

        <!-- Header -->
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-medium text-gray-700">Adjuntar archivos</h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        <!-- Lista de archivos -->
        <div class="max-h-60 overflow-y-auto p-2 space-y-2">

            <!-- Archivos Cloud Seleccionados -->
            @foreach($pendingLinkFiles as $index => $file)
                <div class="flex items-center p-2 bg-blue-50 rounded-md group">
                    <div class="flex-shrink-0 mr-2 text-blue-500">
                        <x-lucide-cloud class="w-4 h-4" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $file->name }}</p>
                        <p class="text-xs text-gray-500">{{ $file->readable_size }}</p>
                    </div>
                    <button wire:click="removeCloudFile({{ $index }})"
                        class="ml-2 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                        <x-lucide-trash-2 class="w-4 h-4" />
                    </button>
                </div>
            @endforeach

            <!-- Archivos Subidos (Temporales) -->
            @foreach($uploads as $index => $upload)
                <div class="flex items-center p-2 bg-gray-50 rounded-md group">
                    <div class="flex-shrink-0 mr-2 text-gray-500">
                        <x-lucide-file class="w-4 h-4" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $upload->getClientOriginalName() }}</p>
                        <p class="text-xs text-gray-500">
                            {{ round($upload->getSize() / 1024, 2) }} KB
                        </p>
                    </div>
                    <button wire:click="deleteUpload({{ $index }})"
                        class="ml-2 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                        <x-lucide-trash-2 class="w-4 h-4" />
                    </button>
                </div>
            @endforeach

            @if(count($uploads) === 0 && count($pendingLinkFiles) === 0)
                <div class="text-center py-4 text-gray-500 text-sm">
                    No hay archivos adjuntos
                </div>
            @endif
        </div>

        <!-- Acciones -->
        <div class="p-3 bg-gray-50 border-t border-gray-200 grid grid-cols-2 gap-2">
            <!-- Botón Subir -->
            <label
                class="flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                <x-lucide-upload class="w-4 h-4 mr-2" />
                Subir
                <input type="file" wire:model="upload" class="hidden" x-ref="fileInput"
                    @change="$dispatch('file-upload-start')" @clear-file-input.window="$refs.fileInput.value = ''">
            </label>

            <!-- Botón Cloud -->
            <button type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-file-selector', { detail: { type: null, id: null, area_id: null } }))"
                class="flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <x-lucide-cloud class="w-4 h-4 mr-2" />
                Cloud
            </button>
        </div>

        <!-- Loading State -->
        <div wire:loading wire:target="upload"
            class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center">
            <div class="flex flex-col items-center">
                <svg class="animate-spin h-6 w-6 text-yellow-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="text-xs text-gray-600">Subiendo...</span>
            </div>
        </div>
    </div>
</div>