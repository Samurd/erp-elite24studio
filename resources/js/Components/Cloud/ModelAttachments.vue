<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    modelId: Number,
    modelType: String,
    areaSlug: String,
    files: Array, // Initial files passed from Inertia prop
});

const isDropping = ref(false);
const inputRef = ref(null);

// Form for uploads
const form = useForm({
    files: [],
    model_id: props.modelId,
    model_type: props.modelType,
    area_slug: props.areaSlug,
});

const uploadFiles = () => {
    form.post(route('cloud.attachments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('files');
            if (inputRef.value) inputRef.value.value = '';
        },
    });
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
    if (form.files.length > 0) {
        uploadFiles();
    }
};

const handleDrop = (e) => {
    isDropping.value = false;
    form.files = Array.from(e.dataTransfer.files);
    if (form.files.length > 0) {
        uploadFiles();
    }
};

const openSelector = () => {
    // Dipatch window event for Vue Modal
    window.dispatchEvent(new CustomEvent('open-cloud-selector', {
        detail: {
            type: props.modelType,
            id: props.modelId,
            area_id: null 
        }
    }));
};

// Detach
const detach = (fileId) => {
    if (!confirm('¿Desvincular archivo?')) return;
    router.post(route('cloud.attachments.detach', fileId), {
        model_id: props.modelId,
        model_type: props.modelType,
        area_slug: props.areaSlug,
    }, { preserveScroll: true });
};

// Destroy
const destroy = (fileId) => {
    if (!confirm('¿Eliminar permanentemente?')) return;
    router.delete(route('cloud.attachments.destroy', fileId), {
        data: { area_slug: props.areaSlug },
         preserveScroll: true 
    });
};

// Listener for file-linked (from Modal)
const onFileLinked = () => {
    // Reload Inertia to get new files
    router.reload({ only: ['files', 'event'] }); // 'event' might contain files relation
};

onMounted(() => {
    window.addEventListener('file-linked', onFileLinked);
});

onUnmounted(() => {
    window.removeEventListener('file-linked', onFileLinked);
});
</script>

<template>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Archivos Adjuntos</h3>
            <SecondaryButton @click="openSelector" type="button">
                <i class="fas fa-link mr-2"></i>Seleccionar del Cloud
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
            <div v-if="!form.processing">
                <p class="text-gray-500 text-sm">
                    <i class="fas fa-plus-circle text-2xl mb-2 text-blue-400 block"></i>
                    Haz clic o arrastra para agregar
                </p>
            </div>
            <div v-else>
                <div class="text-blue-600 text-sm font-semibold">Subiendo...</div>
            </div>
            <div v-if="form.errors.files" class="text-red-500 text-sm mt-2">
                {{ form.errors.files }}
            </div>
        </div>

        <!-- File List -->
        <div class="space-y-2">
            <div v-for="file in files" :key="file.id" 
                class="flex items-center justify-between p-3 bg-gray-50 rounded border hover:shadow-sm transition group">
                
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="text-gray-400 text-xl group-hover:text-blue-500 transition">
                         <i class="fas fa-file"></i>
                    </div>
                    <div class="truncate">
                        <a :href="route('files.view', file.id)" target="_blank"
                            class="text-sm font-medium text-gray-700 hover:text-blue-600 truncate block">
                            {{ file.name }}
                        </a>
                        <span class="text-xs text-gray-400">{{ (file.size / 1024).toFixed(1) }} KB</span>
                    </div>
                </div>

                <div class="flex items-center space-x-1">
                    <a :href="route('files.download', file.id)"
                        class="text-gray-400 hover:text-blue-600 p-2 rounded hover:bg-blue-50 transition" title="Descargar">
                        <i class="fas fa-download"></i>
                    </a>

                    <button type="button" @click="detach(file.id)"
                        class="text-gray-400 hover:text-orange-500 p-2 rounded hover:bg-orange-50 transition"
                        title="Desvincular">
                        <i class="fas fa-unlink"></i>
                    </button>

                    <!-- Permission check for delete? We let backend handle it, button visible -->
                    <button type="button" @click="destroy(file.id)"
                        class="text-gray-400 hover:text-red-600 p-2 rounded hover:bg-red-50 transition"
                        title="Eliminar definitivamente">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div v-if="files.length === 0" class="text-center text-gray-400 py-4 text-sm border border-dashed border-gray-200 rounded">
                No hay archivos adjuntos.
            </div>
        </div>
    </div>
</template>
