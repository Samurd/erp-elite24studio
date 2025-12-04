<div class="bg-white rounded-lg shadow p-6 border border-gray-100">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Adjuntar Archivos</h3>

        {{-- Botón para seleccionar del Cloud --}}
        @if(auth()->user()->can($areaSlug . '.view'))
            <button type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-file-selector', { detail: { type: {{ \Illuminate\Support\Js::from($modelClass) }}, id: null, area_id: {{ $areaId }} } }))"
                class="text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded hover:bg-gray-200 border flex items-center gap-2 transition">
                <i class="fas fa-link text-blue-500"></i> Seleccionar de Cloud
            </button>
        @endif
    </div>

    {{-- 1. ZONA DE UPLOAD (Drag & Drop) --}}
    @if(auth()->user()->can($areaSlug . '.create'))
        <div x-data="{ isDropping: false, uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true"
            x-on:livewire-upload-finish="uploading = false; progress = 0" x-on:livewire-upload-error="uploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress"
            x-on:clear-file-input.window="document.getElementById('creatorFileInput-{{ $this->getId() }}').value = ''"
            class="relative mb-6">

            <input type="file" x-ref="fileInput" wire:model="upload" id="creatorFileInput-{{ $this->getId() }}"
                class="hidden">

            <div class="border-2 border-dashed rounded-lg p-6 text-center transition-colors cursor-pointer bg-gray-50 hover:bg-gray-100"
                :class="isDropping ? 'border-blue-500 bg-blue-50' : 'border-gray-300'" @dragover.prevent="isDropping = true"
                @dragleave.prevent="isDropping = false"
                @drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }))"
                @click="$refs.fileInput.click()">

                <div x-show="!uploading">
                    <p class="text-gray-500 text-sm">
                        <i class="fas fa-cloud-upload-alt text-2xl mb-2 text-blue-400 block"></i>
                        Haz clic o arrastra para agregar
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Se vincularán al crear el registro</p>
                </div>

                {{-- Barra de Progreso --}}
                <div x-show="uploading" class="w-full" x-cloak>
                    <p class="text-xs text-blue-600 font-bold mb-1 text-left">Subiendo temporal...</p>
                    <div class="bg-blue-100 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- 2. LISTAS DE PENDIENTES --}}

    <div class="space-y-3">

        {{-- A. Archivos Nuevos (Uploads) --}}
        @if(count($uploads) > 0)
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase mb-2">Nuevos Archivos:</p>
                <div class="space-y-2">
                    @foreach($uploads as $index => $up)
                        <div class="flex items-center justify-between bg-blue-50 p-2 rounded border border-blue-100"
                            wire:key="upload-{{ $index }}">
                            <div class="flex items-center overflow-hidden">
                                <i class="fas fa-file-medical text-blue-400 mr-2"></i>
                                <span class="text-sm text-blue-900 truncate w-48" title="{{ $up->getClientOriginalName() }}">
                                    {{ $up->getClientOriginalName() }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-blue-600">{{ number_format($up->getSize() / 1024, 1) }} KB</span>
                                <button type="button" wire:click="deleteUpload({{ $index }})"
                                    class="text-red-400 hover:text-red-600 p-1 rounded hover:bg-red-50">
                                    <x-clarity-remove-line class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- B. Archivos del Cloud (Links) --}}
        @if(count($pendingLinkFiles) > 0)
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase mb-2 mt-4">Vincular desde Cloud:</p>
                <div class="space-y-2">
                    @foreach($pendingLinkFiles as $index => $file)
                        <div class="flex items-center justify-between bg-purple-50 p-2 rounded border border-purple-100"
                            wire:key="link-{{ $file->id }}">
                            <div class="flex items-center overflow-hidden">
                                <x-lucide-unlink class="w-5 h-5" />
                                <span class="text-sm text-purple-900 truncate w-48" title="{{ $file->name }}">
                                    {{ $file->name }}
                                </span>
                            </div>
                            <button type="button" wire:click="removeCloudFile({{ $index }})"
                                class="text-red-400 hover:text-red-600 p-1 rounded hover:bg-red-50">
                                <x-clarity-remove-line class="w-5 h-5" />
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Estado Vacío --}}
        @if(empty($uploads) && empty($pendingLinkFiles))
            <div class="text-center text-gray-400 text-xs py-4 italic border-t border-gray-100">
                No hay archivos seleccionados.
            </div>
        @endif
    </div>
</div>