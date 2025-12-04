<div class="p-6 bg-[#1a1a1a] text-white min-h-screen space-y-6 rounded-xl font-sans" x-data="{
        uploading: false,
        dropping: false,
        viewMode: $persist('grid').as('cloud-view-mode'),
        
        // Sistema de notificaciones toast
        notifications: [],
        addNotification(message, type = 'success') {
            this.notifications.push({id: Date.now(), message, type});
            setTimeout(() => { this.notifications.shift() }, 3000);
        }
     }" {{-- Escuchar evento notify desde Livewire --}}
    x-on:notify.window="addNotification($event.detail.message || $event.detail, $event.detail.type || 'success')"
    @dragover.prevent="dropping = true" @dragleave.prevent="dropping = false"
    @drop.prevent="dropping = false; $refs.fileInput.click()">

    {{-- Componente Modal Compartir (Global) --}}
    @livewire('modules.cloud.components.share-dialog')

    {{-- Notificaciones Flotantes --}}
    <div class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none">
        <template x-for="note in notifications" :key="note.id">
            <div x-transition:enter="transform ease-out duration-300 transition"
                x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="pointer-events-auto px-4 py-3 rounded-lg shadow-lg backdrop-blur-sm flex items-center gap-3 border"
                :class="note.type === 'error' ? 'bg-red-500/10 border-red-500/20 text-red-400' : 'bg-green-500/10 border-green-500/20 text-green-400'">

                <svg x-show="note.type !== 'error'" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg x-show="note.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
                <span class="font-medium" x-text="note.message"></span>
            </div>
        </template>
    </div>

    {{-- Header & Toolbar --}}
    <div
        class="flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-0 z-30 bg-[#1a1a1a]/95 backdrop-blur-xl py-2 border-b border-white/5">

        {{-- Breadcrumbs --}}
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar mask-linear-fade">
            <button wire:click="openFolder(null)"
                class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200
                {{ !$currentFolder ? 'bg-white/10 text-white shadow-sm' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                Root
            </button>

            @foreach ($breadcrumbs as $crumb)
                <span class="text-gray-600">/</span>
                <button wire:click="openFolder({{ $crumb->id }})"
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200
                            {{ $loop->last ? 'bg-white/10 text-white shadow-sm' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <span class="truncate max-w-[150px]">{{ $crumb->name }}</span>
                </button>
            @endforeach
        </div>

        {{-- Actions Toolbar --}}
        <div class="flex items-center gap-3">
            {{-- View Toggle --}}
            <div class="flex items-center bg-black/20 rounded-lg p-1 border border-white/5">
                <button @click="viewMode = 'grid'"
                    :class="viewMode === 'grid' ? 'bg-white/10 text-white shadow-sm' : 'text-gray-500 hover:text-gray-300'"
                    class="p-1.5 rounded-md transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                </button>
                <button @click="viewMode = 'list'"
                    :class="viewMode === 'list' ? 'bg-white/10 text-white shadow-sm' : 'text-gray-500 hover:text-gray-300'"
                    class="p-1.5 rounded-md transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <div class="h-6 w-px bg-white/10 mx-1"></div>

            {{-- Create/Upload Actions --}}
            @if (optional($currentFolder)->id ? auth()->user()->can('update', $currentFolder) : auth()->user()->can('cloud.create'))

                {{-- Create Folder --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-sm font-medium transition-all duration-200 text-gray-200">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <span>Nueva Carpeta</span>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        class="absolute top-full right-0 mt-2 w-64 bg-[#252525] border border-white/10 rounded-xl shadow-xl p-3 z-50"
                        style="display: none;" x-transition>
                        <form wire:submit.prevent="createFolder" @submit="open = false">
                            <label class="block text-xs font-medium text-gray-400 mb-1">Nombre</label>
                            <div class="flex gap-2">
                                <input type="text" wire:model.defer="newFolderName" placeholder="Ej. Proyectos"
                                    class="flex-1 bg-black/20 border border-white/10 rounded-lg px-3 py-1.5 text-sm text-white placeholder-gray-600 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none"
                                    autofocus />
                                <button type="submit"
                                    class="px-3 py-1.5 bg-yellow-500/10 text-yellow-500 hover:bg-yellow-500/20 rounded-lg text-sm font-medium transition-colors">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Upload Button --}}
                <button @click="$refs.fileInput.click()"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-medium shadow-lg shadow-blue-500/20 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span>Subir Archivo</span>
                </button>
                <input type="file" x-ref="fileInput" wire:model="uploadingFile" class="hidden" />
            @endif
        </div>
    </div>

    {{-- Drag & Drop Overlay --}}
    <div x-show="dropping"
        class="fixed inset-0 z-50 bg-blue-500/20 backdrop-blur-sm border-4 border-blue-500 border-dashed m-4 rounded-2xl flex items-center justify-center"
        style="display: none;">
        <div class="text-center">
            <svg class="w-20 h-20 text-blue-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <h3 class="text-2xl font-bold text-white">Suelta los archivos aquí</h3>
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading wire:target="uploadingFile" class="fixed bottom-6 right-6 z-50">
        <div class="bg-[#252525] border border-white/10 rounded-xl shadow-2xl p-4 flex items-center gap-4">
            <div class="w-8 h-8 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
            <div>
                <h4 class="text-sm font-medium text-white">Subiendo...</h4>
                <p class="text-xs text-gray-400">Por favor espera</p>
            </div>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="min-h-[500px]">
        @if ($folders->isEmpty() && $files->isEmpty())
            <div class="flex flex-col items-center justify-center h-[400px] text-gray-500">
                <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-300">Carpeta vacía</h3>
                <p class="text-sm mt-1">Sube archivos o crea una carpeta</p>
            </div>
        @else
            {{-- GRID VIEW --}}
            <div x-show="viewMode === 'grid'"
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                {{-- Carpetas --}}
                @foreach ($folders as $folder)
                    <div class="group relative bg-white/5 hover:bg-white/10 border border-white/5 hover:border-white/20 rounded-xl p-4 transition-all duration-200 cursor-pointer flex flex-col items-center text-center"
                        wire:click="openFolder({{ $folder->id }})" wire:key="folder-grid-{{ $folder->id }}">

                        <div class="w-16 h-16 mb-3 text-yellow-500 transition-transform group-hover:scale-110 duration-200">
                            <svg class="w-full h-full drop-shadow-lg" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z" />
                            </svg>
                        </div>

                        {{-- Renombrar --}}
                        @if ($renamingId === $folder->id && $renamingType === 'folder')
                            <input type="text" wire:model.defer="newName" wire:keydown.enter="updateName" @click.stop
                                class="w-full bg-black/40 border border-blue-500 rounded px-2 py-1 text-xs text-center text-white focus:outline-none"
                                autofocus>
                        @else
                            <span class="text-sm font-medium text-gray-200 truncate w-full px-2"
                                title="{{ $folder->name }}">{{ $folder->name }}</span>
                        @endif

                        <span class="text-[10px] text-gray-500 mt-1">{{ $folder->created_at->diffForHumans() }}</span>

                        {{-- Menú Contextual --}}
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
                            <x-cloud-context-menu :item="$folder" type="folder" />
                        </div>
                    </div>
                @endforeach

                {{-- Archivos --}}
                @foreach ($files as $file)
                    <div class="group relative bg-white/5 hover:bg-white/10 border border-white/5 hover:border-white/20 rounded-xl p-4 transition-all duration-200 cursor-pointer flex flex-col items-center text-center"
                        wire:key="file-grid-{{ $file->id }}">

                        {{-- Icono / Preview --}}
                        <div class="w-16 h-16 mb-3 flex items-center justify-center transition-transform group-hover:scale-110 duration-200"
                            {{-- Al hacer click en el icono también descarga --}}
                            onclick="window.location.href='{{ route('files.download', $file) }}'">

                            @if(Str::startsWith($file->mime_type, 'image/'))
                                <img src="{{ $file->url }}" class="w-full h-full object-cover rounded-lg shadow-md" loading="lazy">
                            @else
                                <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            @endif
                        </div>

                        {{-- Lógica de Renombrado vs Nombre --}}
                        @if ($renamingId === $file->id && $renamingType === 'file')
                            <div class="relative w-full flex items-center justify-center">
                                {{-- Input de Renombrado --}}
                                <input type="text" wire:model.defer="newName" wire:keydown.enter="updateName"
                                    wire:keydown.escape="cancelRename" {{-- Cancelar con ESC --}} @click.stop
                                    class="w-full bg-black/40 border border-blue-500 rounded px-2 py-1 text-xs text-center text-white focus:outline-none pr-6"
                                    autofocus>

                                {{-- Botón Cancelar (X) --}}
                                <button wire:click.stop="cancelRename" class="absolute right-1 text-gray-400 hover:text-red-400"
                                    title="Cancelar">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @else
                            {{-- Nombre con enlace de descarga --}}
                            <a href="{{ route('files.download', $file) }}"
                                class="text-sm font-medium text-gray-200 truncate w-full px-2 hover:text-blue-400 transition-colors block"
                                title="Clic para descargar: {{ $file->name }}">
                                {{ $file->name }}
                            </a>
                        @endif

                        <span class="text-[10px] text-gray-500 mt-1">{{ $file->readable_size }}</span>

                        {{-- Menú Contextual (3 puntos) --}}
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
                            <x-cloud-context-menu :item="$file" type="file" />
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- LIST VIEW --}}
            <div x-show="viewMode === 'list'" class="bg-white/5 rounded-xl border border-white/5 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white/5 text-gray-400 font-medium border-b border-white/5">
                        <tr>
                            <th class="px-4 py-3 w-8"></th>
                            <th class="px-4 py-3">Nombre</th>
                            <th class="px-4 py-3">Tamaño</th>
                            <th class="px-4 py-3">Modificado</th>
                            <th class="px-4 py-3">Propietario</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach ($folders as $folder)
                            <tr class="group hover:bg-white/5 transition-colors cursor-pointer"
                                wire:click="openFolder({{ $folder->id }})">
                                <td class="px-4 py-3 text-yellow-500"><svg class="w-5 h-5" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z" />
                                    </svg></td>
                                <td class="px-4 py-3 text-white font-medium">{{ $folder->name }}</td>
                                <td class="px-4 py-3 text-gray-500">-</td>
                                <td class="px-4 py-3 text-gray-500">{{ $folder->updated_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-gray-500 flex items-center gap-2">
                                    @if($folder->user)
                                        <img src="{{ $folder->user->profile_photo_url }}" class="w-5 h-5 rounded-full">
                                        {{ $folder->user->name }}
                                    @else
                                        <span class="text-gray-600 italic">Sistema</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right" @click.stop><x-cloud-context-menu :item="$folder"
                                        type="folder" /></td>
                            </tr>
                        @endforeach

                        @foreach ($files as $file)
                            <tr class="group hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3 text-blue-400"><svg class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg></td>
                                <td class="px-4 py-3 text-white font-medium">
                                    <a href="{{ route('files.download', $file) }}" class="hover:underline">{{ $file->name }}</a>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $file->readable_size }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $file->updated_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-gray-500 flex items-center gap-2">
                                    @if($file->user)
                                        <img src="{{ $file->user->profile_photo_url }}" class="w-5 h-5 rounded-full">
                                        {{ $file->user->name }}
                                    @else
                                        <span class="text-gray-600 italic">Sistema</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right" @click.stop><x-cloud-context-menu :item="$file" type="file" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>