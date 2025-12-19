<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    modelType: String,
    areaSlug: String,
    // V-Models for Parent Form
    files: Array, // Array of File objects (new uploads)
    pendingFileIds: Array, // Array of IDs (linked from cloud)
});

const emit = defineEmits(['update:files', 'update:pendingFileIds']);

const isDropping = ref(false);
const inputRef = ref(null);
// Local state for pending cloud files (to show names)
const pendingCloudFiles = ref([]); 

// Computed proxies for v-model triggers? 
// Simplest is to emit update on change.

const handleFileChange = (e) => {
    const newFiles = Array.from(e.target.files);
    emit('update:files', [...props.files, ...newFiles]);
    // clear input
    if (inputRef.value) inputRef.value.value = '';
};

const handleDrop = (e) => {
    isDropping.value = false;
    const newFiles = Array.from(e.dataTransfer.files);
    emit('update:files', [...props.files, ...newFiles]);
};

const removeUpload = (index) => {
    const newFiles = [...props.files];
    newFiles.splice(index, 1);
    emit('update:files', newFiles);
};

const openSelector = () => {
    // Dipatch window event for Vue Modal
    window.dispatchEvent(new CustomEvent('open-cloud-selector', {
        detail: {
            type: props.modelType,
            id: null, // Null = Creation Mode
            area_id: null // Ideally pass valid area ID if available
        }
    }));
};

const onFileSelected = (e) => {
    // Check context if provided?
    const { fileId, file, contextType } = e.detail;

    // Optional: Check contextType matches props.modelType to avoid cross-talk?
    if (contextType && contextType !== props.modelType) return;

    if (!props.pendingFileIds.includes(fileId)) {
        emit('update:pendingFileIds', [...props.pendingFileIds, fileId]);
        
        // Add full file object to local display list
        if (file) {
            pendingCloudFiles.value.push(file);
        }
    }
};

const removeCloudFile = (index) => {
    const newIds = [...props.pendingFileIds];
    newIds.splice(index, 1);
    emit('update:pendingFileIds', newIds);

    pendingCloudFiles.value.splice(index, 1);
};

onMounted(() => {
    window.addEventListener('file-selected-for-creation', onFileSelected);
});

onUnmounted(() => {
    window.removeEventListener('file-selected-for-creation', onFileSelected);
});
</script>

<template>
    <div class="bg-white rounded-lg shadow p-6 border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Adjuntar Archivos</h3>
            <SecondaryButton @click="openSelector" type="button">
                <i class="fas fa-link mr-2"></i> Seleccionar de Cloud
            </SecondaryButton>
        </div>

        <!-- Drop Zone -->
        <div
            class="relative mb-6 border-2 border-dashed rounded-lg p-6 text-center transition-colors cursor-pointer"
            :class="isDropping ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400'"
            @dragover.prevent="isDropping = true"
            @dragleave.prevent="isDropping = false"
            @drop.prevent="handleDrop"
            @click="inputRef.click()"
        >
            <input 
                type="file" 
                ref="inputRef" 
                multiple 
                class="hidden" 
                @change="handleFileChange"
            >
            <div>
                <p class="text-gray-500 text-sm">
                    <i class="fas fa-cloud-upload-alt text-2xl mb-2 text-blue-400 block"></i>
                    Haz clic o arrastra para agregar
                </p>
                <p class="text-xs text-gray-400 mt-1">Se vincularán al crear el registro</p>
            </div>
        </div>

        <!-- Lists -->
        <div class="space-y-3">
            <!-- New Uploads -->
            <div v-if="files.length > 0">
                <p class="text-xs font-bold text-gray-500 uppercase mb-2">Nuevos Archivos:</p>
                <div class="space-y-2">
                    <div v-for="(file, index) in files" :key="'new-' + index"
                        class="flex items-center justify-between bg-blue-50 p-2 rounded border border-blue-100">
                        <div class="flex items-center overflow-hidden">
                             <i class="fas fa-file-medical text-blue-400 mr-2"></i>
                            <span class="text-sm text-blue-900 truncate" :title="file.name">
                                {{ file.name }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-blue-600">{{ (file.size / 1024).toFixed(1) }} KB</span>
                            <button type="button" @click="removeUpload(index)"
                                class="text-red-400 hover:text-red-600 p-1 rounded hover:bg-red-50">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cloud Links -->
            <div v-if="pendingCloudFiles.length > 0">
                <p class="text-xs font-bold text-gray-500 uppercase mb-2 mt-4">Vincular desde Cloud:</p>
                <div class="space-y-2">
                    <div v-for="(file, index) in pendingCloudFiles" :key="file.id"
                        class="flex items-center justify-between bg-purple-50 p-2 rounded border border-purple-100">
                        <div class="flex items-center overflow-hidden">
                            <i class="fas fa-link text-purple-400 mr-2"></i>
                            <span class="text-sm text-purple-900 truncate" :title="file.name">
                                {{ file.name }}
                            </span>
                        </div>
                         <button type="button" @click="removeCloudFile(index)"
                                class="text-red-400 hover:text-red-600 p-1 rounded hover:bg-red-50">
                                <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
             
             <!-- Empty -->
            <div v-if="files.length === 0 && pendingCloudFiles.length === 0" class="text-center text-gray-400 text-xs py-4 italic border-t border-gray-100">
                No hay archivos seleccionados.
            </div>
        </div>
    </div>
</template>
