<x-dialog-modal wire:model="showModal" position="center">
    <x-slot name="title">
        <div class="flex border-b p-4">
            <div class="flex items-center">
                <x-fas-folder class="w-5 h-5 mr-2 text-yellow-500" />
                <h3 class="text-xl font-bold">Seleccionar Carpeta</h3>
            </div>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="p-4 bg-gray-50 min-h-[400px]">
            <!-- Breadcrumbs -->
            <div class="flex items-center space-x-2 mb-4">
                @if ($currentFolder)
                    <button wire:click="goBack"
                        class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 text-sm flex items-center">
                        <x-fas-arrow-left class="w-4 h-4 mr-1" />
                        Volver
                    </button>
                @endif

                <div
                    class="flex items-center space-x-2 border border-gray-300 rounded-lg p-2 overflow-x-auto no-scrollbar bg-white">
                    <button wire:click="openFolder(null)"
                        class="text-blue-600 hover:underline flex-shrink-0 font-medium">
                        Root
                    </button>

                    @if (!empty($breadcrumbs))
                        <span class="text-gray-500 flex-shrink-0">/</span>
                    @endif

                    @foreach ($breadcrumbs as $crumb)
                        <button wire:click="openFolder({{ $crumb->id }})"
                            class="text-blue-600 hover:underline truncate max-w-[120px] sm:max-w-[200px] text-left"
                            title="{{ $crumb->name }}">
                            {{ $crumb->name }}
                        </button>

                        @if (!$loop->last)
                            <span class="text-gray-500 flex-shrink-0">/</span>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Explorador de carpetas -->
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="divide-y divide-gray-200">
                    {{-- Carpetas --}}
                    @forelse ($folders as $folder)
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 transition group">
                            {{-- Área clickeable para navegar --}}
                            <div class="flex items-center space-x-3 flex-1 cursor-pointer"
                                wire:click="openFolder({{ $folder->id }})">
                                <x-fas-folder class="w-5 h-5 text-yellow-500" />
                                <div>
                                    <span class="font-medium text-gray-900">{{ $folder->name }}</span>
                                    <p class="text-sm text-gray-500">Carpeta</p>
                                </div>
                            </div>

                            {{-- Botón de seleccionar (solo en modo folder) --}}
                            <div class="flex items-center space-x-2">
                                <img src="{{ $folder->user->profile_photo_url ?? '' }}"
                                    class="w-6 h-6 rounded-full object-cover" alt="">
                                <span class="text-sm text-gray-400">{{ $folder->user->name ?? '—' }}</span>

                                @if ($selectionMode === 'folder')
                                    <button type="button" wire:click.stop="selectFolder({{ $folder->id }})"
                                        class="ml-2 px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-xs rounded transition opacity-0 group-hover:opacity-100">
                                        Seleccionar
                                    </button>
                                @else
                                    <x-fas-chevron-right class="w-4 h-4 text-gray-400 group-hover:text-gray-600" />
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <x-fas-folder-open class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                            <p>No hay carpetas disponibles</p>
                        </div>
                    @endforelse

                    <!-- Archivos (si se permite selección de archivos) -->
                    @if ($allowFileSelection && !empty($files))
                        <div class="border-t border-gray-200 pt-2">
                            <p class="px-3 text-sm font-medium text-gray-700 mb-2">Archivos</p>
                            @foreach ($files as $file)
                                <div class="flex items-center justify-between p-3 hover:bg-gray-50 transition cursor-pointer group"
                                    wire:click="selectFile({{ $file->id }})">
                                    <div class="flex items-center space-x-3">
                                        <x-fas-file class="w-5 h-5 text-blue-500" />
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $file->name }}</span>
                                            <p class="text-sm text-gray-500">{{ $file->readable_size }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <img src="{{ $file->user->profile_photo_url }}"
                                            class="w-6 h-6 rounded-full object-cover" alt="{{ $file->user->name }}">
                                        <span class="text-sm text-gray-400">{{ $file->user->name }}</span>
                                        <x-fas-chevron-right class="w-4 h-4 text-gray-400 group-hover:text-gray-600" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Instrucciones -->
            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <x-fas-info-circle class="w-5 h-5 text-blue-500 mt-0.5 mr-2 flex-shrink-0" />
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Instrucciones:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Navega por las carpetas usando los breadcrumbs o haciendo clic en las carpetas</li>
                            <li>Selecciona una carpeta haciendo clic sobre ella</li>
                            @if ($allowFileSelection)
                                <li>También puedes seleccionar archivos si es necesario</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <div class="flex justify-between items-center px-4 py-3 bg-gray-50 border-t">
            <div class="text-sm text-gray-600">
                @if ($selectedPath)
                    <span class="font-medium">Seleccionado:</span>
                    <code class="bg-gray-200 px-2 py-1 rounded text-xs">{{ $selectedPath }}</code>
                @else
                    <span class="text-gray-400">No hay selección</span>
                @endif
            </div>
            <div class="space-x-2">
                <button wire:click="closeModal"
                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                    Cancelar
                </button>
            </div>
        </div>
    </x-slot>
</x-dialog-modal>