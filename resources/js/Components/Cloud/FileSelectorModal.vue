<script setup>
import { ref, watch, onMounted, onUnmounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const isOpen = ref(false);
const isLoading = ref(false);
const allFolders = ref([]);
const allFiles = ref([]);
const currentFolderId = ref(null);

const context = ref({
    type: null, 
    id: null,   
    area_id: null 
});

const selectedFileId = ref(null);

// -- COMPUTED FOR CLIENT-SIDE NAV --

const currentFolders = computed(() => {
    return allFolders.value.filter(f => f.parent_id === currentFolderId.value);
});

const currentFiles = computed(() => {
    return allFiles.value.filter(f => f.folder_id === currentFolderId.value);
});

const breadcrumbs = computed(() => {
    const crumbs = [];
    if (!currentFolderId.value) return crumbs;

    let curr = allFolders.value.find(f => f.id === currentFolderId.value);
    while (curr) {
        crumbs.unshift(curr);
        curr = allFolders.value.find(f => f.id === curr.parent_id);
    }
    return crumbs;
});

// -- ACTIONS --

const open = (detail) => {
    isOpen.value = true;
    context.value = detail;
    currentFolderId.value = null;
    selectedFileId.value = null;
    
    // Load if empty
    if (allFolders.value.length === 0 && allFiles.value.length === 0) {
        loadData();
    }
};

const close = () => {
    isOpen.value = false;
    context.value = {};
    // Keep data cached in memory (don't clear allFolders/allFiles)
};

const loadData = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(route('cloud.selector.data'));
        const data = response.data;
        allFolders.value = data.folders;
        allFiles.value = data.files;
    } catch (error) {
        console.error("Error loading cloud data", error);
    } finally {
        isLoading.value = false;
    }
};

const navigateTo = (folderId) => {
    currentFolderId.value = folderId;
    selectedFileId.value = null;
};

const selectFile = (file) => {
    selectedFileId.value = selectedFileId.value === file.id ? null : file.id;
};

const confirmSelection = () => {
    if (!selectedFileId.value) return;

    const file = allFiles.value.find(f => f.id === selectedFileId.value);
    
    if (!context.value.id) {
         window.dispatchEvent(new CustomEvent('file-selected-for-creation', {
            detail: {
                fileId: file.id,
                file: file,
                contextType: context.value.type
            }
        }));
        close();
    } 
    else {
        router.post(route('cloud.attachments.link'), {
            file_id: file.id,
            model_type: context.value.type,
            model_id: context.value.id,
            area_id: context.value.area_id
        }, {
            preserveScroll: true,
            onSuccess: () => {
                window.dispatchEvent(new CustomEvent('file-linked'));
                close();
            }
        });
    }
};

const onOpenEvent = (e) => {
    open(e.detail);
};

const onCloseEvent = () => {
    close();
};

onMounted(() => {
    window.addEventListener('open-cloud-selector', onOpenEvent);
    window.addEventListener('close-file-selector-modal', onCloseEvent);
});

onUnmounted(() => {
    window.removeEventListener('open-cloud-selector', onOpenEvent);
    window.removeEventListener('close-file-selector-modal', onCloseEvent);
});
</script>

<template>
    <Modal :show="isOpen" @close="close" maxWidth="2xl">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Seleccionar Archivo de Cloud
                    </h3>
                    
                    <!-- Breadcrumbs -->
                    <div class="mt-2 flex items-center text-sm text-gray-500 mb-4 bg-gray-50 p-2 rounded">
                        <button @click="navigateTo(null)" class="hover:text-blue-600 hover:underline">
                            <i class="fas fa-home"></i> Inicio
                        </button>
                        <template v-for="crumb in breadcrumbs" :key="crumb.id">
                            <span class="mx-2">/</span>
                            <button @click="navigateTo(crumb.id)" class="hover:text-blue-600 hover:underline">
                                {{ crumb.name }}
                            </button>
                        </template>
                    </div>

                    <!-- Content -->
                    <div class="h-96 overflow-y-auto border rounded-md p-2">
                        <div v-if="isLoading" class="flex justify-center items-center h-full text-gray-400">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                        </div>
                        <div v-else>
                            <!-- Folders -->
                            <div v-if="currentFolders.length > 0" class="mb-4">
                                <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Carpetas</h4>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    <div v-for="folder in currentFolders" :key="folder.id"
                                        @click="navigateTo(folder.id)"
                                        class="cursor-pointer p-3 bg-yellow-50 rounded border border-yellow-100 hover:bg-yellow-100 flex items-center">
                                        <i class="fas fa-folder text-yellow-500 mr-2"></i>
                                        <span class="truncate text-sm text-gray-700">{{ folder.name }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Files -->
                            <div v-if="currentFiles.length > 0">
                                <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Archivos</h4>
                                <div class="space-y-1">
                                    <div v-for="file in currentFiles" :key="file.id"
                                        @click="selectFile(file)"
                                        class="cursor-pointer p-2 rounded border flex items-center justify-between transition"
                                        :class="selectedFileId === file.id ? 'bg-blue-100 border-blue-500 ring-1 ring-blue-500' : 'bg-white border-gray-200 hover:bg-gray-50'">
                                        <div class="flex items-center overflow-hidden">
                                            <i class="fas fa-file text-gray-400 mr-3" :class="{'text-blue-500': selectedFileId === file.id}"></i>
                                            <span class="truncate text-sm" :class="selectedFileId === file.id ? 'font-semibold text-blue-900' : 'text-gray-700'">
                                                {{ file.name }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ file.readable_size }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="currentFolders.length === 0 && currentFiles.length === 0" class="flex flex-col items-center justify-center h-48 text-gray-400">
                                <i class="fas fa-folder-open text-3xl mb-2"></i>
                                <p class="text-sm">Carpeta vacía</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <PrimaryButton class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                :disabled="!selectedFileId || isLoading"
                @click="confirmSelection">
                Seleccionar
            </PrimaryButton>
            <SecondaryButton class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                @click="close">
                Cancelar
            </SecondaryButton>
        </div>
    </Modal>
</template>
