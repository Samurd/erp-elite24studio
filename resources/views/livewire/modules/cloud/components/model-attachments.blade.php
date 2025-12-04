<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Archivos Adjuntos</h3>

        @if(auth()->user()->can($areaSlug . '.update'))
            <button type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-file-selector', { detail: { type: {{ \Illuminate\Support\Js::from($modelClass) }}, id: {{ $modelId }}, area_id: {{ $areaId }} } }))"
                class="text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded hover:bg-gray-200 border flex items-center gap-2">
                <i class="fas fa-link"></i>Seleccionar del Cloud
            </button>
        @endif
    </div>

    @if(auth()->user()->can($areaSlug . '.update'))

        <div x-data="{ isDropping: false, uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true"
            x-on:livewire-upload-finish="uploading = false; progress = 0" x-on:livewire-upload-error="uploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress"
            x-on:clear-file-input.window="document.getElementById('fileInput_{{ $this->getId() }}').value = ''"
            class="relative mb-6">

            <input type="file" x-ref="fileInput" wire:model="upload" id="fileInput_{{ $this->getId() }}" class="hidden">

            <div class="border-2 border-dashed rounded-lg p-6 text-center transition-colors cursor-pointer"
                :class="isDropping ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400'"
                @dragover.prevent="isDropping = true" @dragleave.prevent="isDropping = false"
                @drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }))"
                @click="$refs.fileInput.click()">

                <div x-show="!uploading">
                    <p class="text-gray-500 text-sm">
                        <i class="fas fa-plus-circle text-2xl mb-2 text-blue-400 block"></i>
                        Haz clic o arrastra para agregar
                    </p>
                </div>

                <div x-show="uploading" class="w-full" x-cloak>
                    <div class="bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            </div>
        </div>

        @if(count($uploads) > 0)
            <div class="mb-6 bg-blue-50 rounded-lg border border-blue-100 p-4">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-bold text-blue-800">
                        <i class="fas fa-layer-group mr-1"></i> Por Guardar ({{ count($uploads) }})
                    </h4>
                    <button type="button" wire:click="save"
                        class="bg-blue-600 text-white px-3 py-1 rounded text-xs font-bold shadow hover:bg-blue-700 transition">
                        Guardar Todos
                    </button>
                </div>

                <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                    @foreach($uploads as $index => $up)
                        <div class="flex items-center justify-between bg-white p-2 rounded border shadow-sm">
                            <div class="flex items-center overflow-hidden">
                                <span class="text-sm text-gray-700 truncate w-48" title="{{ $up->getClientOriginalName() }}">
                                    {{ $up->getClientOriginalName() }}
                                </span>
                            </div>
                            <div class="flex items-center text-xs space-x-2">
                                <span class="text-gray-400">{{ number_format($up->getSize() / 1024, 1) }} KB</span>

                                <button type="button" wire:click="removeUpload({{ $index }})"
                                    class="text-red-400 hover:text-red-600 p-1 hover:bg-red-50 rounded transition"
                                    title="Quitar de la lista">
                                    <x-clarity-remove-line class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @endif

    <div class="space-y-2">
        @forelse($files as $file)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded border hover:shadow-sm transition group">

                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="text-gray-400 text-xl group-hover:text-blue-500 transition">
                        <x-clarity-file-solid class="w-5 h-5" />
                    </div>
                    <div class="truncate">
                        {{-- Usamos route('files.view') para abrir en nueva pestaña --}}
                        <a href="{{ route('files.view', $file) }}" target="_blank"
                            class="text-sm font-medium text-gray-700 hover:text-blue-600 truncate block">
                            {{ $file->name }}
                        </a>
                        <span class="text-xs text-gray-400">{{ $file->readable_size }}</span>
                    </div>
                </div>

                <div class="flex items-center space-x-1">
                    <a href="{{ route('files.download', $file) }}"
                        class="text-gray-400 hover:text-blue-600 p-2 rounded hover:bg-blue-50 transition" title="Descargar">
                        <x-clarity-download-cloud-line class="w-5 h-5" />
                    </a>

                    @if(auth()->user()->can($areaSlug . '.update'))

                        {{-- BOTÓN DESVINCULAR --}}
                        <button type="button" wire:click="detach({{ $file->id }})"
                            class="text-gray-400 hover:text-orange-500 p-2 rounded hover:bg-orange-50 transition"
                            title="Desvincular (Quitar de este registro)">
                            <x-lucide-unlink class="w-5 h-5" />
                        </button>

                        {{-- BOTÓN ELIMINAR FÍSICO --}}
                        @if($file->user_id === auth()->id() || auth()->user()->can('cloud.delete'))
                            <button type="button" wire:click="deleteFile({{ $file->id }})"
                                wire:confirm="¿Estás seguro? Esto eliminará el archivo del servidor permanentemente y desaparecerá de todos los registros vinculados."
                                class="text-gray-400 hover:text-red-600 p-2 rounded hover:bg-red-50 transition"
                                title="Eliminar definitivamente">
                                <x-fluentui-delete-48-o class="w-5 h-5" />
                            </button>
                        @endif

                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-gray-400 py-4 text-sm border border-dashed border-gray-200 rounded">
                No hay archivos adjuntos.
            </div>
        @endforelse
    </div>
</div>