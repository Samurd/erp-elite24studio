<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ShareModal from './ShareModal.vue';
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    folders: Array,
    files: Array,
    currentFolder: Object,
    breadcrumbs: Array,
    canCreate: Boolean,
});

const viewMode = ref(localStorage.getItem('cloud-view-mode') || 'grid');
const uploading = ref(false);
const dropping = ref(false);
const fileInput = ref(null);

// Create Folder Modal
const showCreateFolderModal = ref(false);
const newFolderName = ref('');

// Rename Modal / Logic
const renamingItem = ref(null); // { id, type, name }

// Share Modal
const showShareModal = ref(false);
const sharingItem = ref(null);

// Context Menu
const contextMenu = ref({ visible: false, x: 0, y: 0, item: null, type: null });

const setViewMode = (mode) => {
    viewMode.value = mode;
    localStorage.setItem('cloud-view-mode', mode);
};

const openFolder = (folderId) => {
    router.visit(route('cloud.index', { folder: folderId }));
};

const createFolder = () => {
    if (!newFolderName.value) return;
    router.post(route('cloud.folder.create'), {
        name: newFolderName.value,
        parent_id: props.currentFolder?.id
    }, {
        onSuccess: () => {
            showCreateFolderModal.value = false;
            newFolderName.value = '';
        }
    });
};

const handleFileUpload = (event) => {
    const files = event.target.files;
    if (files.length > 0) {
        uploadFiles(files);
        // Reset input
        event.target.value = ''; 
    }
};

const handleDrop = (event) => {
    dropping.value = false;
    const files = event.dataTransfer.files;
    if (files.length > 0) {
        uploadFiles(files);
    }
};

const uploadFiles = (files) => {
    uploading.value = true;
    const formData = new FormData();
    // Currently backend handles single file per request in UploadFileAction typically, 
    // but we can iterate or adjust backend. ImplementationPlan assumed standard upload.
    // For simplicity, let's upload the first one or iterate if needed.
    // Let's assume one by one or single file input for now based on previous Livewire code.
    // The previous livewire code: 'uploadingFile' => 'required|file'. It handles one by one.
    
    // We will upload the first one for now, or we can loop.
    formData.append('file', files[0]);
    if (props.currentFolder?.id) {
        formData.append('folder_id', props.currentFolder.id);
    }

    router.post(route('cloud.file.upload'), formData, {
        onSuccess: () => {
            uploading.value = false;
        },
        onError: () => {
            uploading.value = false;
        },
        forceFormData: true,
    });
};

// Context Menu Logic
const showContextMenu = (event, item, type) => {
    event.preventDefault();
    contextMenu.value = {
        visible: true,
        x: event.clientX,
        y: event.clientY,
        item,
        type
    };
};

const closeContextMenu = () => {
    contextMenu.value.visible = false;
};

// Actions
const downloadFile = (file) => {
    window.location.href = route('files.download', file.id);
};

const startRename = (item, type) => {
    renamingItem.value = { id: item.id, type, name: item.name };
    closeContextMenu();
};

const submitRename = () => {
    if (!renamingItem.value || !renamingItem.value.name) return;
    router.put(route('cloud.rename', renamingItem.value.id), {
        name: renamingItem.value.name,
        type: renamingItem.value.type
    }, {
        onSuccess: () => {
            renamingItem.value = null;
        }
    });
};

const deleteItem = (item, type) => {
    if (!confirm('¿Estás seguro de eliminar este elemento?')) return;
    closeContextMenu();
    router.delete(route('cloud.delete', item.id), {
        data: { type }
    });
};

onMounted(() => {
    document.addEventListener('click', closeContextMenu);
});
onUnmounted(() => {
    document.removeEventListener('click', closeContextMenu);
});

const startShare = (item, type) => {
    sharingItem.value = { id: item.id, type, name: item.name };
    showShareModal.value = true;
    closeContextMenu();
};

const isImage = (mime) => mime?.startsWith('image/');

</script>

<template>
    <AppLayout title="Nube">
        <!-- Share Modal -->
        <ShareModal :show="showShareModal" :item="sharingItem" @close="showShareModal = false" />
        
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nube
            </h2>
        </template>

        <!-- Drop Zone Overlay -->
        <div v-if="dropping" class="fixed inset-0 z-50 bg-blue-500/20 backdrop-blur-sm border-4 border-blue-500 border-dashed m-4 rounded-2xl flex items-center justify-center pointer-events-none">
             <div class="text-center">
                 <i class="fas fa-cloud-upload-alt text-6xl text-white mb-4"></i>
                <h3 class="text-2xl font-bold text-white">Suelta los archivos aquí</h3>
            </div>
        </div>

        <div class="py-12 bg-[#1a1a1a] min-h-screen text-white" 
             @dragover.prevent="dropping = true" 
             @dragleave.prevent="dropping = false" 
             @drop.prevent="handleDrop">
            
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Toolbar -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-0 z-30 bg-[#1a1a1a]/95 backdrop-blur-xl py-4 border-b border-white/5 mb-6">
                    
                    <!-- Breadcrumbs -->
                    <div class="flex items-center gap-2 overflow-x-auto">
                        <button @click="openFolder(null)" 
                            class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                            :class="!currentFolder ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white'">
                            <i class="fas fa-home"></i> Root
                        </button>
                        
                        <template v-for="(crumb, index) in breadcrumbs" :key="crumb.id">
                            <span class="text-gray-600">/</span>
                            <button @click="openFolder(crumb.id)"
                                class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                                :class="(index === breadcrumbs.length - 1) ? 'bg-white/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white'">
                                <span class="truncate max-w-[150px]">{{ crumb.name }}</span>
                            </button>
                        </template>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <!-- View Toggle -->
                        <div class="flex items-center bg-black/20 rounded-lg p-1 border border-white/5">
                            <button @click="setViewMode('grid')" 
                                :class="viewMode === 'grid' ? 'bg-white/10 text-white' : 'text-gray-500 hover:text-gray-300'"
                                class="p-1.5 rounded-md transition-colors">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button @click="setViewMode('list')"
                                :class="viewMode === 'list' ? 'bg-white/10 text-white' : 'text-gray-500 hover:text-gray-300'"
                                class="p-1.5 rounded-md transition-colors">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>

                        <div class="h-6 w-px bg-white/10 mx-1"></div>

                        <div v-if="canCreate" class="flex items-center gap-3">
                             <!-- Create Folder -->
                            <div class="relative">
                                <button @click="showCreateFolderModal = !showCreateFolderModal" 
                                    class="flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-sm font-medium transition-colors text-gray-200">
                                    <i class="fas fa-folder-plus text-yellow-500"></i>
                                    <span>Nueva Carpeta</span>
                                </button>
                                
                                <!-- Dropdown Modal for Folder Name -->
                                <div v-if="showCreateFolderModal" class="absolute top-full right-0 mt-2 w-64 bg-[#252525] border border-white/10 rounded-xl shadow-xl p-3 z-50">
                                    <form @submit.prevent="createFolder">
                                        <label class="block text-xs font-medium text-gray-400 mb-1">Nombre</label>
                                        <div class="flex gap-2">
                                            <input type="text" v-model="newFolderName" placeholder="Ej. Proyectos" 
                                                class="flex-1 bg-black/20 border border-white/10 rounded-lg px-3 py-1.5 text-sm text-white placeholder-gray-600 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none"
                                                autofocus />
                                            <button type="submit" class="px-3 py-1.5 bg-yellow-500/10 text-yellow-500 hover:bg-yellow-500/20 rounded-lg text-sm font-medium transition-colors">Crear</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Upload -->
                            <button @click="fileInput.click()" 
                                class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-medium shadow-lg shadow-blue-500/20 transition-colors">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Subir Archivo</span>
                            </button>
                            <input type="file" ref="fileInput" class="hidden" @change="handleFileUpload" />
                        </div>
                    </div>
                </div>

                <!-- Filters/Empty State -->
                <div v-if="folders.length === 0 && files.length === 0" class="flex flex-col items-center justify-center h-[400px] text-gray-500">
                     <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-folder-open text-4xl text-gray-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-300">Carpeta vacía</h3>
                    <p class="text-sm mt-1">Sube archivos o crea una carpeta</p>
                </div>

                <div v-else>
                     <!-- GRID VIEW -->
                    <div v-if="viewMode === 'grid'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        <!-- Folders -->
                        <div v-for="folder in folders" :key="'folder-'+folder.id" 
                            @click="openFolder(folder.id)"
                            @contextmenu="showContextMenu($event, folder, 'folder')"
                            class="group relative bg-white/5 hover:bg-white/10 border border-white/5 hover:border-white/20 rounded-xl p-4 transition-all duration-200 cursor-pointer flex flex-col items-center text-center">
                            
                            <div class="w-16 h-16 mb-3 text-yellow-500 transition-transform group-hover:scale-110">
                                <i class="fas fa-folder text-6xl"></i>
                            </div>

                            <!-- Rename Input or Name -->
                            <div v-if="renamingItem?.id === folder.id && renamingItem?.type === 'folder'" @click.stop>
                                <input type="text" v-model="renamingItem.name" @keydown.enter="submitRename" @keydown.esc="renamingItem = null"
                                    class="w-full bg-black/40 border border-blue-500 rounded px-2 py-1 text-xs text-center text-white focus:outline-none" autofocus />
                            </div>
                            <span v-else class="text-sm font-medium text-gray-200 truncate w-full px-2" :title="folder.name">{{ folder.name }}</span>
                            
                            <span class="text-[10px] text-gray-500 mt-1">{{ new Date(folder.created_at).toLocaleDateString() }}</span>

                            <!-- Dropdown Trigger -->
                             <button @click.stop="showContextMenu($event, folder, 'folder')" 
                                class="absolute top-2 right-2 p-1 text-gray-400 hover:text-white rounded-md hover:bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>

                         <!-- Files -->
                        <div v-for="file in files" :key="'file-'+file.id" 
                            @click="downloadFile(file)"
                            @contextmenu="showContextMenu($event, file, 'file')"
                            class="group relative bg-white/5 hover:bg-white/10 border border-white/5 hover:border-white/20 rounded-xl p-4 transition-all duration-200 cursor-pointer flex flex-col items-center text-center">
                            
                            <div class="w-16 h-16 mb-3 flex items-center justify-center transition-transform group-hover:scale-110">
                                <img v-if="isImage(file.mime_type)" :src="file.url" class="w-full h-full object-cover rounded-lg shadow-md" loading="lazy" />
                                <i v-else class="fas fa-file text-5xl text-blue-400"></i>
                            </div>

                             <!-- Rename Input or Name -->
                            <div v-if="renamingItem?.id === file.id && renamingItem?.type === 'file'" @click.stop>
                                <input type="text" v-model="renamingItem.name" @keydown.enter="submitRename" @keydown.esc="renamingItem = null"
                                    class="w-full bg-black/40 border border-blue-500 rounded px-2 py-1 text-xs text-center text-white focus:outline-none" autofocus />
                            </div>
                            <a v-else :href="route('files.download', file.id)" class="text-sm font-medium text-gray-200 truncate w-full px-2 hover:text-blue-400 transition-colors block" :title="file.name">
                                {{ file.name }}
                            </a>
                            
                            <span class="text-[10px] text-gray-500 mt-1">{{ file.readable_size }}</span>

                             <!-- Dropdown Trigger -->
                             <button @click.stop="showContextMenu($event, file, 'file')" 
                                class="absolute top-2 right-2 p-1 text-gray-400 hover:text-white rounded-md hover:bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                    </div>

                    <!-- LIST VIEW -->
                    <div v-if="viewMode === 'list'" class="bg-white/5 rounded-xl border border-white/5 overflow-hidden">
                        <table class="w-full text-left text-sm">
                             <thead class="bg-white/5 text-gray-400 font-medium border-b border-white/5">
                                <tr>
                                    <th class="px-4 py-3 w-8"></th>
                                    <th class="px-4 py-3">Nombre</th>
                                    <th class="px-4 py-3">Tamaño</th>
                                    <th class="px-4 py-3">Modificado</th>
                                    <th class="px-4 py-3">Propietario</th>
                                    
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                 <tr v-for="folder in folders" :key="'l-folder-'+folder.id" 
                                    @click="openFolder(folder.id)"
                                    @contextmenu="showContextMenu($event, folder, 'folder')"
                                    class="group hover:bg-white/5 transition-colors cursor-pointer">
                                    <td class="px-4 py-3 text-yellow-500"><i class="fas fa-folder"></i></td>
                                    <td class="px-4 py-3 text-white font-medium">
                                        <div v-if="renamingItem?.id === folder.id && renamingItem?.type === 'folder'" @click.stop>
                                            <input type="text" v-model="renamingItem.name" @keydown.enter="submitRename" @keydown.esc="renamingItem = null"
                                                class="bg-black/40 border border-blue-500 rounded px-2 py-1 text-xs text-white focus:outline-none" autofocus />
                                        </div>
                                        <span v-else>{{ folder.name }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">-</td>
                                    <td class="px-4 py-3 text-gray-500">{{ new Date(folder.updated_at).toLocaleDateString() }}</td>
                                     <td class="px-4 py-3 text-gray-500 flex justify-between items-center">
                                        <span>{{ folder.user ? folder.user.name : 'Sistema' }}</span>
                                        <button @click.stop="showContextMenu($event, folder, 'folder')" class="text-gray-400 hover:text-white p-1">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                     </td>
                                </tr>
                                 <tr v-for="file in files" :key="'l-file-'+file.id" 
                                    @click="downloadFile(file)"
                                    @contextmenu="showContextMenu($event, file, 'file')"
                                    class="group hover:bg-white/5 transition-colors cursor-pointer">
                                    <td class="px-4 py-3 text-blue-400"><i class="fas fa-file"></i></td>
                                    <td class="px-4 py-3 text-white font-medium">
                                         <div v-if="renamingItem?.id === file.id && renamingItem?.type === 'file'" @click.stop>
                                            <input type="text" v-model="renamingItem.name" @keydown.enter="submitRename" @keydown.esc="renamingItem = null"
                                                class="bg-black/40 border border-blue-500 rounded px-2 py-1 text-xs text-white focus:outline-none" autofocus />
                                        </div>
                                         <a v-else :href="route('files.download', file.id)" class="hover:underline">{{ file.name }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ file.readable_size }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ new Date(file.updated_at).toLocaleDateString() }}</td>
                                     <td class="px-4 py-3 text-gray-500 flex justify-between items-center">
                                        <span>{{ file.user ? file.user.name : 'Sistema' }}</span>
                                        <button @click.stop="showContextMenu($event, file, 'file')" class="text-gray-400 hover:text-white p-1">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                     </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Context Menu -->
        <div v-if="contextMenu.visible" 
            :style="{ top: contextMenu.y + 'px', left: contextMenu.x + 'px' }"
            class="fixed bg-[#252525] border border-white/10 rounded-lg shadow-xl z-50 min-w-[160px] py-1">
            
            <button @click="startShare(contextMenu.item, contextMenu.type)" class="w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-white/10 flex items-center gap-2">
                <i class="fas fa-share-alt w-4 text-blue-400"></i> Compartir
            </button>
            <div class="border-t border-white/10 my-1"></div>

            <button @click="startRename(contextMenu.item, contextMenu.type)" class="w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-white/10 flex items-center gap-2">
                <i class="fas fa-edit w-4"></i> Renombrar
            </button>
            <button @click="deleteItem(contextMenu.item, contextMenu.type)" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-white/10 flex items-center gap-2">
                <i class="fas fa-trash w-4"></i> Eliminar
            </button>
            <div class="border-t border-white/10 my-1"></div>
            <button v-if="contextMenu.type === 'file'" @click="downloadFile(contextMenu.item)" class="w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-white/10 flex items-center gap-2">
                <i class="fas fa-download w-4"></i> Descargar
            </button>
        </div>

    </AppLayout>
</template>

<style scoped>
/* Allow overflow scroll for breadcrumbs but hide scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    display: none;
}
.overflow-x-auto {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
