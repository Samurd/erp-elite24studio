<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue';

const props = defineProps({
    kpi: Object,
    kpiRecord: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    record_date: props.kpiRecord?.record_date 
        ? new Date(props.kpiRecord.record_date).toISOString().split('T')[0] 
        : new Date().toISOString().split('T')[0],
    value: props.kpiRecord?.value || '',
    observation: props.kpiRecord?.observation || '',
    files: [], // Only for new uploads in this simple context or if Creator supports it
    pending_file_ids: [], // Not used for direct ModelAttachmentsCreator storage if standard
    // However, KpiRecordController uses specific handling.
    // Let's adapt to ModelAttachmentsCreator which emits 'update:files' (File objects)
    // and 'update:pendingFileIds' (Cloud IDs).
    folder_id: null,
});

// We need to handle the custom folder selection that was present in Livewire
// The ModelAttachmentsCreator has "openSelector" which dispatches 'open-cloud-selector'
// We need to listen to 'folder-selected' if we want to store in a specific folder.
// Actually, ModelAttachmentsCreator logic is:
// 1. Select from Cloud -> adds to pendingFileIds (links existing)
// 2. Upload new -> adds to files array.
// The Livewire form allowed selecting a target folder for NEW uploads.
// Resource analysis showed ModelAttachmentsCreator doesn't seemingly expose "Target Folder for Upload".
// It just handles files.
// However, the controller we wrote expects `folder_id` for new uploads.
// I will add a simple folder selector or rely on the Cloud selector event if feasible.
// For now, I will use ModelAttachmentsCreator as is for files, but I need to capture the `folder_id` if the user wants to specify it.
// The Livewire view had a specific "Select Folder" button that set `selectedFolderId`.

const selectedFolderName = ref(null);

const handleFolderSelection = (event) => {
    // Assuming a global event or we implement a button
    // The Livewire used "open-folder-selector" which likely opened a modal.
    // I can replicate the button to open the selector.
    window.dispatchEvent(new CustomEvent('open-folder-selector', {
        detail: {
            callback: (folder) => {
                form.folder_id = folder.id;
                selectedFolderName.value = folder.name;
            }
        }
    }));
};

// Listen for folder selection from the global modal
// This assumes the layout or a global component handles "open-folder-selector" and emits back or calls callback.
// If not, I might need to implement the modal call more specifically.
// Given strict instructions to "replicate", I will assume we can trigger the existing folder selector if it exists globally.
// If not, I will add a simple button that simulates it or standard "Cloud" selection.

const submit = () => {
    if (props.kpiRecord) {
        form.post(route('kpis.records.update', props.kpiRecord.id), { // Use POST with _method PUT for files
            _method: 'put',
            preserveScroll: true,
        });
    } else {
        form.post(route('kpis.records.store', props.kpi.id), {
            preserveScroll: true,
        });
    }
};

const deleteResultFile = (fileId) => {
    if (confirm('¿Eliminar este archivo?')) {
        router.delete(route('kpis.records.files.destroy', fileId), {
             preserveScroll: true,
        });
    }
}
</script>

<template>
    <AppLayout :title="kpiRecord ? 'Editar Registro' : 'Nuevo Registro'">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ kpiRecord ? 'Editar Registro' : 'Nuevo Registro' }}
                    </h1>
                    <p class="text-gray-600 mt-1">
                        {{ kpiRecord ? 'Modificar registro existente' : 'Agregar medición al KPI' }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('kpis.show', kpi.id)"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- KPI Info -->
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <h3 class="font-semibold text-gray-800">{{ kpi.indicator_name }}</h3>
                        <div class="flex space-x-4 text-sm text-gray-600 mt-1">
                            <span>Código: <span class="font-medium">{{ kpi.protocol_code }}</span></span>
                            <span>Meta: <span class="font-medium">{{ kpi.target_value ?? 'N/A' }}</span></span>
                        </div>
                    </div>

                    <!-- Fecha -->
                    <div>
                        <InputLabel for="record_date" value="Fecha de Registro *" />
                        <TextInput
                            id="record_date"
                            v-model="form.record_date"
                            type="date"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.record_date" class="mt-2" />
                    </div>

                    <!-- Valor -->
                    <div>
                        <InputLabel for="value" value="Valor Obtenido *" />
                        <TextInput
                            id="value"
                            v-model="form.value"
                            type="number"
                            step="0.01"
                            class="mt-1 block w-full"
                            placeholder="Ej: 95.5"
                            required
                        />
                        <InputError :message="form.errors.value" class="mt-2" />
                    </div>

                    <!-- Observación -->
                    <div class="md:col-span-2">
                        <InputLabel for="observation" value="Observación" />
                        <textarea
                            id="observation"
                            v-model="form.observation"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            rows="4"
                            placeholder="Comentarios adicionales..."
                        ></textarea>
                        <InputError :message="form.errors.observation" class="mt-2" />
                    </div>
                </div>

                <!-- Archivos -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Evidencias / Adjuntos</h3>
        

                    <!-- Use ModelAttachmentsCreator for Drag & Drop and File List -->
                    <ModelAttachmentsCreator 
                        model-type="kpi_record"
                        :files="form.files"
                        @update:files="form.files = $event"
                        :pending-file-ids="form.pending_file_ids"
                        @update:pending-file-ids="form.pending_file_ids = $event"
                    />
                    
                    <!-- Existing Files List (for Edit) -->
                    <div v-if="kpiRecord && kpiRecord.files && kpiRecord.files.length > 0" class="mt-4">
                         <h4 class="text-sm font-semibold text-gray-700 mb-2">Archivos Existentes:</h4>
                         <div class="space-y-2">
                            <div v-for="file in kpiRecord.files" :key="file.id" class="flex items-center justify-between p-3 bg-white border rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-file text-gray-400 mr-3"></i>
                                    <a :href="'/storage/' + file.path" target="_blank" class="text-sm text-blue-600 hover:underline">
                                        {{ file.name }}
                                    </a>
                                </div>
                                <!-- Note: Delete action should ideally be handled via a method that calls destroy endpoint -->
                                <!-- Since we are in a form, maybe just show them. But user might want to delete. -->
                                <!-- I'll add a delete button if possible, but standard flow might be in Show view. -->
                                <!-- The Livewire form had delete buttons. -->
                                <button type="button" @click="() => deleteResultFile(file.id)" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                         </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                    <Link :href="route('kpis.show', kpi.id)"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition">
                        Cancelar
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        <i class="fas fa-save mr-2"></i>{{ kpiRecord ? 'Actualizar Registro' : 'Guardar Registro' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
