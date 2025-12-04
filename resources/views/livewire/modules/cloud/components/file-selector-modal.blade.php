<div x-data="{
        show: false,
        contextType: null,
        contextId: null,
        areaId: null,
        currentFolderId: null,
        selectedFileId: null,
        allFolders: @js($allFolders),
        allFiles: @js($allFiles),
        breadcrumbs: [],

        get folders() {
            return this.allFolders.filter(f => f.parent_id === this.currentFolderId);
        },

        get files() {
            return this.allFiles.filter(f => f.folder_id === this.currentFolderId);
        },

        navigate(folderId) {
            this.currentFolderId = folderId;
            this.selectedFileId = null;
            this.updateBreadcrumbs();
        },

        updateBreadcrumbs() {
            const crumbs = [];
            let currentId = this.currentFolderId;

            while (currentId) {
                const folder = this.allFolders.find(f => f.id === currentId);
                if (folder) {
                    crumbs.unshift({ id: folder.id, name: folder.name });
                    currentId = folder.parent_id;
                } else {
                    break;
                }
            }

            this.breadcrumbs = crumbs;
        },

        selectFile(fileId) {
            this.selectedFileId = (this.selectedFileId === fileId) ? null : fileId;
        }
     }" x-on:open-file-selector.window="
        show = true;
        contextType = $event.detail.type;
        contextId = $event.detail.id;
        areaId = $event.detail.area_id;
        currentFolderId = null;
        selectedFileId = null;
        breadcrumbs = [];
        $wire.call('setContext', contextType, contextId, areaId);
     " @close-file-selector-modal.window="show = false">

    <div x-show="show" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" style="display: none;">

        <!-- Backdrop -->
        <div x-show="show" class="fixed inset-0 transform transition-all" @click="show = false"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal Container -->
        <div x-show="show"
            class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-4xl sm:mx-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.stop>

            <!-- Header -->
            <div class="px-6 py-4 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between text-gray-800">
                    <span class="text-lg font-medium">{{ __('Seleccionar archivo del Cloud') }}</span>
                    <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-4">
                <!-- Breadcrumbs -->
                <div class="mb-4 pb-2 border-b flex items-center text-sm">
                    <button @click="navigate(null)" class="text-blue-600 font-bold hover:underline flex items-center">
                        <i class="fas fa-cloud mr-1"></i> Inicio
                    </button>
                    <template x-for="crumb in breadcrumbs" :key="crumb.id">
                        <div class="flex items-center">
                            <span class="mx-2 text-gray-400">/</span>
                            <button @click="navigate(crumb.id)" class="text-blue-600 hover:underline"
                                x-text="crumb.name"></button>
                        </div>
                    </template>
                </div>

                <!-- Files Grid -->
                <div class="h-96 overflow-y-auto pr-2 custom-scrollbar">
                    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">

                        {{-- Carpetas --}}
                        <template x-for="folder in folders" :key="folder.id">
                            <div @click="navigate(folder.id)"
                                class="flex flex-col items-center justify-center p-3 border rounded-lg cursor-pointer hover:bg-yellow-50 transition shadow-sm h-32 group">
                                <i
                                    class="fas fa-folder text-yellow-400 text-4xl mb-2 group-hover:scale-110 transition-transform"></i>
                                <span
                                    class="text-xs text-center font-medium text-gray-700 truncate w-full px-1 select-none"
                                    x-text="folder.name">
                                </span>
                            </div>
                        </template>

                        {{-- Archivos --}}
                        <template x-for="file in files" :key="file.id">
                            <div @click="selectFile(file.id)"
                                class="flex flex-col items-center justify-center p-3 border rounded-lg cursor-pointer transition shadow-sm h-32 relative group"
                                :class="selectedFileId == file.id ? 'ring-2 ring-blue-500 bg-blue-50' : 'hover:bg-gray-50'">

                                {{-- Icono / Preview --}}
                                <template x-if="file.mime_type && file.mime_type.startsWith('image/')">
                                    <img :src="file.url" class="w-full h-16 object-cover rounded mb-2">
                                </template>
                                <template x-if="!file.mime_type || !file.mime_type.startsWith('image/')">
                                    <i
                                        class="fas fa-file-alt text-gray-400 text-3xl mb-2 group-hover:text-gray-600"></i>
                                </template>

                                <span
                                    class="text-xs text-center font-medium text-gray-700 truncate w-full px-1 select-none"
                                    x-text="file.name">
                                </span>
                                <template x-if="file.folder">
                                    <span class="text-[10px] text-gray-400 truncate w-full text-center px-1">
                                        <i class="fas fa-folder text-yellow-400 mr-1"></i> <span
                                            x-text="file.folder.name"></span>
                                    </span>
                                </template>

                                {{-- Check de seleccionado --}}
                                <template x-if="selectedFileId == file.id">
                                    <div class="absolute top-2 right-2 text-blue-600">
                                        <i class="fas fa-check-circle text-lg bg-white rounded-full"></i>
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Empty State --}}
                        <template x-if="folders.length === 0 && files.length === 0">
                            <div class="col-span-full flex flex-col items-center justify-center h-64 text-gray-400">
                                <i class="fas fa-folder-open text-4xl mb-3 opacity-50"></i>
                                <p>Esta carpeta está vacía.</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-100 text-right">
                <x-secondary-button @click="show = false" type="button">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-button class="ml-3" @click="$wire.call('confirmSelection', selectedFileId).then(() => show = false)"
                    x-bind:disabled="!selectedFileId">
                    {{ __('Vincular Archivo') }}
                </x-button>
            </div>
        </div>
    </div>
</div>