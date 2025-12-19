<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue';
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue';

const props = defineProps({
    report: Object,
    statusOptions: Array,
});

const isEdit = computed(() => !!props.report);

const form = useForm({
    _method: isEdit.value ? 'POST' : 'POST',
    title: props.report?.title || '',
    description: props.report?.description || '',
    date: props.report?.date ? props.report.date.split('T')[0] : '',
    hour: props.report?.hour ? props.report.hour.substring(0, 5) : '', // HH:MM
    status_id: props.report?.status_id || '',
    notes: props.report?.notes || '',
    files: [],
    pending_file_ids: [],
});

const submit = () => {
    if (isEdit.value) {
        form.post(route('reports.update', props.report.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('reports.store'), {
            forceFormData: true,
        });
    }
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
};
</script>

<template>
    <AppLayout :title="isEdit ? 'Editar Reporte' : 'Nuevo Reporte'">
        <main class="flex-1 p-10 bg-gray-100 font-sans">
            <div class="bg-white rounded-lg shadow-sm p-6 max-w-4xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ isEdit ? 'Editar Reporte' : 'Nuevo Reporte' }}
                    </h1>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Title -->
                        <div class="md:col-span-2">
                             <InputLabel for="title" value="Título" />
                             <TextInput id="title" v-model="form.title" type="text" class="mt-1 block w-full" required />
                             <InputError :message="form.errors.title" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <InputLabel for="description" value="Descripción" />
                            <textarea id="description" v-model="form.description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm"></textarea>
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <!-- Date -->
                        <div>
                             <InputLabel for="date" value="Fecha" />
                             <TextInput id="date" v-model="form.date" type="date" class="mt-1 block w-full" required />
                             <InputError :message="form.errors.date" class="mt-2" />
                        </div>

                         <!-- Hour -->
                        <div>
                             <InputLabel for="hour" value="Hora" />
                             <TextInput id="hour" v-model="form.hour" type="time" class="mt-1 block w-full" />
                             <InputError :message="form.errors.hour" class="mt-2" />
                        </div>

                         <!-- Status -->
                        <div>
                            <InputLabel for="status" value="Estado" />
                            <select id="status" v-model="form.status_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                                <option value="">Seleccione un estado</option>
                                <option v-for="option in statusOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                            </select>
                            <InputError :message="form.errors.status_id" class="mt-2" />
                        </div>

                         <!-- Notes -->
                        <div class="md:col-span-2">
                            <InputLabel for="notes" value="Notas" />
                            <textarea id="notes" v-model="form.notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm"></textarea>
                            <InputError :message="form.errors.notes" class="mt-2" />
                        </div>

                    </div>

                    <!-- Files -->
                     <div class="border-t pt-4">
                        <InputLabel value="Archivos Adjuntos" class="mb-2" />
                        
                        <!-- Edit Mode -->
                        <div v-if="isEdit">
                            <ModelAttachments 
                                :model-id="report.id" 
                                model-type="App\Models\Report" 
                                area-slug="reportes" 
                                :files="report.files" 
                            />
                        </div>

                        <!-- Create Mode -->
                        <div v-else>
                            <ModelAttachmentsCreator 
                                model-type="App\Models\Report"
                                area-slug="reportes"
                                v-model:files="form.files"
                                v-model:pendingFileIds="form.pending_file_ids"
                            />
                            <InputError :message="form.errors.files" class="mt-2" />
                        </div>
                     </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t">
                        <Link :href="route('reports.index')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </Link>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ isEdit ? 'Actualizar Reporte' : 'Guardar Reporte' }}
                        </PrimaryButton>
                    </div>

                </form>
            </div>
        </main>
    </AppLayout>
</template>
