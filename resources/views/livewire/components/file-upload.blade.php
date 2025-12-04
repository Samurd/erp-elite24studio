<div class="w-full space-y-4">
    {{-- Selector de Carpeta Específica (Opcional) --}}
    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
        <h3 class="font-bold text-lg mb-3 flex items-center">
            <x-fas-folder class="w-5 h-5 mr-2 text-yellow-500" />
            Carpeta de Destino
        </h3>

        <div class="flex items-center justify-between">
            <div class="flex-1">
                @if ($selectedFolderPath)
                    <div class="flex items-center space-x-2">
                        <x-fas-check-circle class="w-5 h-5 text-green-500" />
                        <span class="text-sm font-medium text-gray-700">Carpeta específica:</span>
                        <code class="bg-gray-200 px-2 py-1 rounded text-xs">{{ $selectedFolderPath }}</code>
                    </div>
                @else
                    <div class="flex items-center space-x-2">
                        <x-fas-info-circle class="w-5 h-5 text-blue-500" />
                        <span class="text-sm text-gray-600">
                            Carpeta por defecto: <code class="bg-blue-50 px-2 py-1 rounded">{{ $defaultFolderName }}</code>
                        </span>
                    </div>
                @endif
            </div>

            <div class="flex space-x-2">
                @if ($selectedFolderPath)
                    <button type="button" wire:click="clearSelectedFolder"
                            class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded text-sm transition">
                        <x-fas-times class="w-4 h-4 mr-1" />
                        Limpiar
                    </button>
                @endif
                <button type="button" wire:click="openFolderSelector"
                        class="px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded text-sm transition">
                    <x-fas-folder-open class="w-4 h-4 mr-1" />
                    Seleccionar Carpeta
                </button>
            </div>
        </div>
    </div>

    {{-- Zona de Carga de Archivos --}}
    <div>
        <h3 wire:loading.remove wire:target="tempFiles" class="font-bold text-2xl mb-2">
            Adjuntar Archivos
        </h3>

        <label for="file-upload-{{ $this->getId() }}"
               class="relative flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-md cursor-pointer bg-white hover:bg-gray-50 transition"
               :class="{
                   'border-gray-400': !@entangle('selectedFolderPath'),
                   'border-green-400 bg-green-50': @entangle('selectedFolderPath')
               }">

            {{-- Contenido normal --}}
            <div wire:loading.remove wire:target="tempFiles"
                 class="flex flex-col items-center justify-center pt-5 pb-6">

                @if ($selectedFolderPath)
                    <div class="flex flex-col items-center text-green-600">
                        <i class="fas fa-cloud-upload-alt text-3xl mb-2"></i>
                        <p class="mb-2 text-sm font-semibold">
                            Subir archivos a: <span class="text-green-700">{{ $selectedFolderPath }}</span>
                        </p>
                        <p class="text-xs text-green-500">Los archivos se guardarán en la carpeta seleccionada del Cloud</p>
                    </div>
                @else
                    <div class="flex flex-col items-center text-gray-600">
                        <i class="fas fa-cloud-upload-alt text-3xl mb-2"></i>
                        <p class="mb-2 text-sm">
                            <span class="font-semibold">Haz clic para subir</span> los archivos aquí
                        </p>
                        <p class="text-xs text-gray-500">Se guardarán en la carpeta por defecto: {{ $defaultFolderName }}</p>
                    </div>
                @endif
            </div>

            {{-- Input de carga --}}
            <input id="file-upload-{{ $this->getId() }}" 
                   type="file" 
                   class="hidden" 
                   wire:model="tempFiles"
                   {{ $allowMultiple ? 'multiple' : '' }}
                   {{ $acceptedTypes ? "accept=\"{$acceptedTypes}\"" : '' }}
                   wire:loading.attr="disabled" />

            {{-- Estado de carga --}}
            <div wire:loading wire:target="tempFiles"
                 class="flex flex-col items-center justify-center text-center">
                <svg class="animate-spin h-6 w-6 text-blue-500 mb-2" xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-gray-700">Cargando archivo...</span>
            </div>
        </label>

        @error('tempFiles.*')
            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
    </div>

    {{-- Archivos Temporales (para subir) --}}
    @if (!empty($tempFiles))
        <div class="text-sm bg-gray-100 p-3 rounded-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="font-semibold text-gray-700">Archivos para subir:</span>
                @if ($selectedFolderPath)
                    <div class="flex items-center text-xs text-green-600 bg-green-50 px-2 py-1 rounded">
                        <x-fas-folder class="w-3 h-3 mr-1" />
                        Se guardarán en: {{ $selectedFolderPath }}
                    </div>
                @endif
            </div>

            <div class="space-y-1">
                @foreach ($tempFilesView as $fileData)
                    <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-gray-200">
                        <div class="flex items-center space-x-2">
                            <x-fas-file class="w-4 h-4 text-blue-500" />
                            <span class="text-sm text-gray-700">{{ $fileData['name'] }}</span>
                            <span class="text-xs text-gray-500">({{ number_format($fileData['size'] / 1024, 2) }} KB)</span>
                        </div>
                        <button type="button" wire:click="removeTempFile({{ $fileData['index'] }})"
                                class="text-red-500 hover:text-red-700 transition">
                            <x-fas-times class="w-4 h-4" />
                        </button>
                    </div>
                @endforeach
            </div>

            @if ($model && $model->exists)
                <button type="button" wire:click="uploadFiles"
                        class="mt-3 w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition">
                    <x-fas-upload class="w-4 h-4 mr-1" />
                    Subir {{ count($tempFiles) }} archivo(s)
                </button>
            @else
                <div class="mt-2 text-xs text-blue-600 flex items-center">
                    <x-fas-info-circle class="w-3 h-3 mr-1" />
                    Los archivos se subirán al guardar el registro
                </div>
            @endif
        </div>
    @endif

    {{-- Archivos Ya Guardados --}}
    @if ($existingFiles && $existingFiles->isNotEmpty())
        <div class="text-sm bg-gray-100 p-3 rounded-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="font-semibold text-gray-700">Archivos guardados:</span>
            </div>

            <div class="space-y-1">
                @foreach ($existingFiles as $file)
                    <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-gray-200">
                        <div class="flex items-center space-x-2">
                            <x-fas-file class="w-4 h-4 text-green-500" />
                            <a href="{{ Storage::url($file->path) }}"
                               target="_blank"
                               class="text-sm text-blue-600 hover:text-blue-800 underline">
                                {{ $file->name }}
                            </a>
                            <span class="text-xs text-gray-500">({{ number_format($file->size / 1024, 2) }} KB)</span>
                            @if ($file->folder)
                                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">
                                    <x-fas-folder class="w-3 h-3 mr-1" />
                                    {{ $file->folder->name }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-gray-400">{{ $file->user->name ?? '—' }}</span>
                            <button type="button"
                                    wire:click="deleteStoredFile({{ $file->id }})"
                                    wire:confirm="¿Estás seguro de que quieres eliminar este archivo?"
                                    class="text-red-500 hover:text-red-700 transition"
                                    title="Eliminar archivo">
                                <x-fas-trash class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Botón para Seleccionar Archivo Existente del Cloud --}}
    @if ($model && $model->exists)
        <div class="flex justify-center">
            <button type="button" wire:click="openFileSelector"
                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded text-sm transition">
                <x-fas-link class="w-4 h-4 mr-1" />
                Vincular Archivo Existente del Cloud
            </button>
        </div>
    @endif
</div>
