<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ $record ? 'Editar Registro' : 'Nuevo Registro' }}
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ $record ? 'Modifica un registro existente' : 'Agrega un nuevo registro al KPI' }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('kpis.show', $kpi->id) }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- KPI Information -->
                <div class="md:col-span-2">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-800 mb-2">KPI: {{ $kpi->indicator_name }}</h3>
                        <p class="text-sm text-gray-600">Código: {{ $kpi->protocol_code }}</p>
                        <p class="text-sm text-gray-600">Valor Objetivo: {{ $kpi->target_value ?? 'No definido' }}</p>
                    </div>
                </div>

                <!-- Fecha de Registro -->
                <div>
                    <label for="record_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Registro</label>
                    <input type="date" id="record_date" wire:model="form.record_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('form.record_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Valor -->
                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700 mb-2">Valor</label>
                    <input type="number" id="value" wire:model="form.value" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="Ej: 95.5">
                    @error('form.value')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Observación -->
                <div class="md:col-span-2">
                    <label for="observation" class="block text-sm font-medium text-gray-700 mb-2">Observación</label>
                    <textarea id="observation" wire:model="form.observation" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Agrega comentarios o detalles sobre este registro..."></textarea>
                    @error('form.observation')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Archivos -->
            <div class="md:col-span-2 mt-6">
                <div class="mb-4 p-4 bg-gray-50 rounded-lg border">
                    <h3 class="font-bold text-lg mb-3 flex items-center">
                        <i class="fas fa-paperclip w-5 h-5 mr-2 text-blue-500"></i>
                        Adjuntar Archivos
                    </h3>

                    <!-- Selector de Carpeta -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1">
                            @if ($selectedFolderPath)
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-check-circle w-4 h-4 text-green-500"></i>
                                    <span class="text-sm font-medium text-gray-700">Carpeta seleccionada:</span>
                                    <code class="bg-gray-200 px-2 py-1 rounded text-xs">{{ $selectedFolderPath }}</code>
                                </div>
                            @else
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-info-circle w-4 h-4 text-blue-500"></i>
                                    <span class="text-sm text-gray-600">Usar carpeta por defecto: <code>kpi_files</code></span>
                                </div>
                            @endif
                        </div>

                        <div class="flex space-x-2">
                            @if ($selectedFolderPath)
                                <button type="button" wire:click="clearSelectedFolder"
                                        class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded text-sm transition">
                                    <i class="fas fa-times w-4 h-4 mr-1"></i>
                                    Limpiar
                                </button>
                            @endif
                            <button type="button" wire:click="openFolderSelector"
                                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition">
                                <i class="fas fa-folder-open w-4 h-4 mr-1"></i>
                                Seleccionar Carpeta
                            </button>
                        </div>
                    </div>

                    <!-- Upload de Archivos -->
                    <label for="file-upload"
                        class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-md cursor-pointer bg-white hover:bg-gray-50 transition"
                        :class="{
                            'border-gray-400': !$selectedFolderPath,
                            'border-green-400 bg-green-50': $selectedFolderPath
                        }">

                        <!-- Contenido normal -->
                        <div wire:loading.remove wire:target="form.files"
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
                                    <p class="text-xs text-gray-500">Se guardarán en la carpeta por defecto: kpi_files</p>
                                </div>
                            @endif
                        </div>

                        <!-- Input de carga -->
                        <input id="file-upload" type="file" class="hidden" wire:model="form.files"
                            wire:loading.attr="disabled"/>

                        <!-- Estado de carga -->
                        <div wire:loading wire:target="form.files"
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

                    <!-- Lista de archivos temporales -->
                    @if ($form->files)
                        <div class="mt-2 text-sm bg-gray-100 p-3 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-700">Archivos para subir:</span>
                                @if ($selectedFolderPath)
                                    <div class="flex items-center text-xs text-green-600 bg-green-50 px-2 py-1 rounded">
                                        <i class="fas fa-folder w-3 h-3 mr-1"></i>
                                        Se guardarán en: {{ $selectedFolderPath }}
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-1">
                                @foreach ($form->files as $index => $file)
                                    <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-gray-200">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-file w-4 h-4 text-blue-500"></i>
                                            <span class="text-sm text-gray-700">{{ $file->getClientOriginalName() }}</span>
                                            <span class="text-xs text-gray-500">({{ number_format($file->getSize() / 1024, 2) }} KB)</span>
                                        </div>
                                        <button type="button" wire:click="removeTempFile({{ $index }})"
                                                class="text-red-500 hover:text-red-700 transition">
                                            <x-fas-times class="w-4 h-4" />
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            @if ($selectedFolderPath)
                                <div class="mt-2 text-xs text-green-600 flex items-center">
                                    <i class="fas fa-check-circle w-3 h-3 mr-1"></i>
                                    Los {{ count($form->files) }} archivo(s) se guardarán en la carpeta seleccionada del Cloud
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Archivos ya subidos -->
                    @if ($files_db && $files_db->isNotEmpty())
                        <div class="mt-4 text-sm bg-gray-100 p-3 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-700">Archivos ya subidos:</span>
                            </div>

                            <div class="space-y-1">
                                @foreach ($files_db as $file)
                                    <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-gray-200">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-file w-4 h-4 text-green-500"></i>
                                            <a href="{{ Storage::url($file->path) }}"
                                               target="_blank"
                                               class="text-sm text-blue-600 hover:text-blue-800 underline">
                                                {{ $file->name }}
                                            </a>
                                            <span class="text-xs text-gray-500">({{ number_format($file->size / 1024, 2) }} KB)</span>
                                            @if ($file->folder)
                                                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">
                                                    <i class="fas fa-folder w-3 h-3 mr-1"></i>
                                                    {{ $file->folder->name }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-400">{{ $file->user->name ?? '—' }}</span>
                                            <button type="button"
                                                    wire:click="removeStoredFile({{ $file->id }})"
                                                    wire:confirm="¿Estás seguro de que quieres eliminar este archivo?"
                                                    class="text-red-500 hover:text-red-700 transition"
                                                    title="Eliminar archivo">
                                                <i class="fas fa-trash w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @error('form.files')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <a href="{{ route('kpis.show', $kpi->id) }}"
                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
                <button type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ $record ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
</div>
