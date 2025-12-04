<div class="w-full">
    <h3 class="font-bold text-2xl mb-4">Gestión de Archivos</h3>

    {{-- SECCIÓN 1: SUBIR ARCHIVOS NUEVOS --}}
    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <h4 class="font-bold text-lg mb-3 flex items-center text-blue-900">
            <x-fas-upload class="w-5 h-5 mr-2" />
            Subir Archivos Nuevos
        </h4>

        <p class="text-sm text-blue-800 mb-3">
            Los archivos nuevos se guardarán en:
            <code class="bg-blue-100 px-2 py-1 rounded font-semibold">
                {{ $selectedFolderId ? 'Carpeta específica seleccionada' : $defaultFolder . ' (carpeta por defecto)' }}
            </code>
        </p>

        {{-- Selector de Carpeta SOLO para archivos nuevos --}}
        <div class="mb-3 p-3 bg-white rounded border border-blue-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700 mb-1">
                        <x-fas-folder class="w-4 h-4 mr-1 text-yellow-500 inline" />
                        Carpeta de destino para archivos nuevos:
                    </p>
                    @if ($selectedFolderId)
                        <p class="text-xs text-green-600 flex items-center">
                            <x-fas-check-circle class="w-3 h-3 mr-1" />
                            Carpeta específica seleccionada
                        </p>
                    @else
                        <p class="text-xs text-gray-600">
                            Se usará la carpeta por defecto: <code
                                class="bg-gray-100 px-1 rounded">{{ $defaultFolder }}</code>
                        </p>
                    @endif
                </div>

                <div class="flex space-x-2">
                    @if ($selectedFolderId)
                        <button type="button" wire:click="$set('selectedFolderId', null)"
                            class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white rounded text-xs transition">
                            <x-fas-times class="w-3 h-3 mr-1" />
                            Usar Default
                        </button>
                    @endif
                    <button type="button" wire:click="openFolderSelector"
                        class="px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white rounded text-xs transition">
                        <x-fas-folder-open class="w-3 h-3 mr-1" />
                        Elegir Carpeta Específica
                    </button>
                </div>
            </div>
        </div>

        {{-- Zona de carga de archivos --}}
        <label for="file-upload-{{ $defaultFolder }}"
            class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-md cursor-pointer bg-white hover:bg-blue-50 transition"
            :class="{ 'border-blue-400 bg-blue-50': @entangle('selectedFolderId'), 'border-gray-300': !@entangle('selectedFolderId') }">

            <div wire:loading.remove wire:target="tempFiles"
                class="flex flex-col items-center justify-center pt-5 pb-6">
                <x-fas-cloud-upload-alt class="w-8 h-8 text-blue-500 mb-2" />
                <p class="mb-1 text-sm text-gray-700">
                    <span class="font-semibold">Click para subir</span> o arrastra un archivo aquí
                </p>
                <p class="text-xs text-gray-500">Puedes agregar múltiples archivos uno por uno</p>
            </div>

            <input id="file-upload-{{ $defaultFolder }}" type="file" class="hidden" wire:model="tempFiles"
                wire:loading.attr="disabled" />

            <div wire:loading wire:target="tempFiles" class="flex flex-col items-center justify-center text-center">
                <svg class="animate-spin h-6 w-6 text-blue-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-gray-700 text-sm">Cargando archivo...</span>
            </div>
        </label>

        @error('tempFiles.*')
            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror

        {{-- Archivos temporales cargados --}}
        @if (!empty($tempFiles))
            <div class="mt-3 text-sm bg-white p-3 rounded border border-blue-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-blue-900">Archivos nuevos a subir:</span>
                    <span class="text-xs text-blue-600">{{ count($tempFiles) }} archivo(s)</span>
                </div>

                <div class="space-y-1">
                    @foreach ($tempFiles as $index => $file)
                        <div class="flex items-center justify-between bg-blue-50 rounded px-3 py-2 border border-blue-100">
                            <div class="flex items-center space-x-2">
                                <x-fas-file class="w-4 h-4 text-blue-600" />
                                <span class="text-sm text-gray-700">{{ $file->getClientOriginalName() }}</span>
                                <span class="text-xs text-gray-500">({{ number_format($file->getSize() / 1024, 2) }} KB)</span>
                            </div>
                            <button type="button" wire:click="$set('tempFiles.{{ $index }}', null)"
                                class="text-red-500 hover:text-red-700 transition">
                                <x-fas-times class="w-4 h-4" />
                            </button>
                        </div>
                    @endforeach
                </div>

                <div class="mt-2 text-xs text-blue-700 flex items-center">
                    <x-fas-info-circle class="w-3 h-3 mr-1" />
                    Se subirán a: {{ $selectedFolderId ? 'carpeta específica' : $defaultFolder . ' (default)' }}
                </div>
            </div>
        @endif
    </div>

    {{-- SECCIÓN 2: VINCULAR ARCHIVOS EXISTENTES DEL CLOUD --}}
    <div class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
        <h4 class="font-bold text-lg mb-3 flex items-center text-purple-900">
            <x-fas-link class="w-5 h-5 mr-2" />
            Vincular Archivos Existentes del Cloud
        </h4>

        <p class="text-sm text-purple-800 mb-3">
            Vincula archivos que ya existen en el Cloud sin duplicarlos. Los archivos mantienen su ubicación original.
        </p>

        <div class="flex justify-center">
            <button type="button" wire:click="openFileSelector"
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded text-sm transition flex items-center">
                <x-fas-search class="w-4 h-4 mr-2" />
                Buscar y Vincular Archivo del Cloud
            </button>
        </div>

        {{-- Archivos vinculados (existentes del Cloud) --}}
        @if (!empty($linkedFileIds))
            @php
                $linkedFiles = \App\Models\File::whereIn('id', $linkedFileIds)->with('folder')->get();
            @endphp

            <div class="mt-3 text-sm bg-white p-3 rounded border border-purple-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-semibold text-purple-900">Archivos a vincular:</span>
                    <span class="text-xs text-purple-600">{{ count($linkedFileIds) }} archivo(s)</span>
                </div>

                <div class="space-y-1">
                    @foreach ($linkedFiles as $file)
                        <div class="flex items-center justify-between bg-purple-50 rounded px-3 py-2 border border-purple-100">
                            <div class="flex items-center space-x-2">
                                <x-fas-link class="w-4 h-4 text-purple-600" />
                                <span class="text-sm text-gray-700">{{ $file->name }}</span>
                                <span class="text-xs text-gray-500">({{ number_format($file->size / 1024, 2) }} KB)</span>
                                @if ($file->folder)
                                    <span class="text-xs text-purple-600 bg-purple-100 px-2 py-0.5 rounded">
                                        <x-fas-folder class="w-3 h-3 mr-1" />
                                        {{ $file->folder->path }}
                                    </span>
                                @endif
                            </div>
                            <button type="button" wire:click="removeLinkedFile({{ $file->id }})"
                                class="text-red-500 hover:text-red-700 transition">
                                <x-fas-times class="w-4 h-4" />
                            </button>
                        </div>
                    @endforeach
                </div>

                <div class="mt-2 text-xs text-purple-700 flex items-center">
                    <x-fas-info-circle class="w-3 h-3 mr-1" />
                    Estos archivos se vincularán sin moverlos de su ubicación actual
                </div>
            </div>
        @endif
    </div>

    {{-- SECCIÓN 3: ARCHIVOS YA GUARDADOS (solo en edición) --}}
    @if ($isEdit && $this->existingFiles->isNotEmpty())
        <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
            <h4 class="font-bold text-lg mb-3 flex items-center text-green-900">
                <x-fas-check-circle class="w-5 h-5 mr-2" />
                Archivos Ya Vinculados a Este Registro
            </h4>

            <div class="space-y-1">
                @foreach ($this->existingFiles as $file)
                    <div class="flex items-center justify-between bg-white rounded px-3 py-2 border border-green-200">
                        <div class="flex items-center space-x-2">
                            <x-fas-file class="w-4 h-4 text-green-600" />
                            <a href="{{ Storage::url($file->path) }}" target="_blank"
                                class="text-sm text-blue-600 hover:text-blue-800 underline">
                                {{ $file->name }}
                            </a>
                            <span class="text-xs text-gray-500">({{ number_format($file->size / 1024, 2) }} KB)</span>
                            @if ($file->folder)
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                    <x-fas-folder class="w-3 h-3 mr-1" />
                                    {{ $file->folder->path }}
                                </span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-400">{{ $file->user->name ?? '—' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>