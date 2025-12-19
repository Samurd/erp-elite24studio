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
    license: Object,
    licenseTypeOptions: Array,
    statusOptions: Array,
    projects: Array,
});

const isEdit = computed(() => !!props.license);

const form = useForm({
    _method: isEdit.value ? 'POST' : 'POST',
    project_id: props.license?.project_id || '',
    license_type_id: props.license?.license_type_id || '',
    status_id: props.license?.status_id || '',
    entity: props.license?.entity || '',
    company: props.license?.company || '',
    eradicated_number: props.license?.eradicated_number || '',
    eradicatd_date: props.license?.eradicatd_date ? props.license.eradicatd_date.split('T')[0] : '',
    estimated_approval_date: props.license?.estimated_approval_date ? props.license.estimated_approval_date.split('T')[0] : '',
    expiration_date: props.license?.expiration_date ? props.license.expiration_date.split('T')[0] : '',
    requires_extension: props.license?.requires_extension ? true : false,
    observations: props.license?.observations || '',
    files: [],
    pending_file_ids: [],
});

const submit = () => {
    if (isEdit.value) {
        form.post(route('licenses.update', props.license.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('licenses.store'), {
            forceFormData: true,
        });
    }
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
};
</script>

<template>
    <AppLayout :title="isEdit ? 'Editar Licencia' : 'Nueva Licencia'">
        <main class="flex-1 p-10 bg-gray-100 font-sans">
             <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                         <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEdit ? 'Editar Licencia' : 'Nueva Licencia' }}
                        </h1>
                        <p class="text-gray-600 mt-1">{{ isEdit ? 'Actualizar información de la licencia' : 'Registrar nueva licencia' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">

                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                         <!-- Project -->
                        <div>
                             <InputLabel for="project_id" value="Proyecto" />
                             <select id="project_id" v-model="form.project_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
                                <option value="">Seleccionar proyecto</option>
                                <option v-for="option in projects" :key="option.id" :value="option.id">{{ option.name }}</option>
                            </select>
                             <InputError :message="form.errors.project_id" class="mt-2" />
                        </div>

                         <!-- License Type -->
                        <div>
                            <InputLabel for="license_type_id" value="Tipo de Trámite" />
                            <select id="license_type_id" v-model="form.license_type_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
                                <option value="">Seleccionar tipo</option>
                                <option v-for="option in licenseTypeOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                            </select>
                            <InputError :message="form.errors.license_type_id" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div>
                            <InputLabel for="status_id" value="Estado" />
                            <select id="status_id" v-model="form.status_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                                <option value="">Seleccionar estado</option>
                                <option v-for="option in statusOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                            </select>
                            <InputError :message="form.errors.status_id" class="mt-2" />
                        </div>

                         <!-- Entity -->
                        <div>
                             <InputLabel for="entity" value="Entidad Tramitadora" />
                             <TextInput id="entity" v-model="form.entity" type="text" class="mt-1 block w-full" placeholder="Nombre de entidad" />
                             <InputError :message="form.errors.entity" class="mt-2" />
                        </div>

                        <!-- Company -->
                        <div>
                             <InputLabel for="company" value="Empresa Gestora" />
                             <TextInput id="company" v-model="form.company" type="text" class="mt-1 block w-full" placeholder="Nombre de empresa" />
                             <InputError :message="form.errors.company" class="mt-2" />
                        </div>

                         <!-- Eradicated Number -->
                        <div>
                             <InputLabel for="eradicated_number" value="Número de Erradicado" />
                             <TextInput id="eradicated_number" v-model="form.eradicated_number" type="text" class="mt-1 block w-full" placeholder="Número..." />
                             <InputError :message="form.errors.eradicated_number" class="mt-2" />
                        </div>

                    </div>

                    <!-- Dates Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <!-- Eradicated Date -->
                        <div>
                             <InputLabel for="eradicatd_date" value="Fecha Erradicado" />
                             <TextInput id="eradicatd_date" v-model="form.eradicatd_date" type="date" class="mt-1 block w-full" />
                             <InputError :message="form.errors.eradicatd_date" class="mt-2" />
                        </div>

                         <!-- Estimated Approval Date -->
                        <div>
                             <InputLabel for="estimated_approval_date" value="Fecha Estimada Aprobación" />
                             <TextInput id="estimated_approval_date" v-model="form.estimated_approval_date" type="date" class="mt-1 block w-full" />
                             <InputError :message="form.errors.estimated_approval_date" class="mt-2" />
                        </div>

                         <!-- Expiration Date -->
                        <div>
                             <InputLabel for="expiration_date" value="Fecha de Vencimiento" />
                             <TextInput id="expiration_date" v-model="form.expiration_date" type="date" class="mt-1 block w-full" />
                             <InputError :message="form.errors.expiration_date" class="mt-2" />
                        </div>
                    </div>

                    <!-- Third Row -->
                    <div class="grid grid-cols-1 gap-6 mt-6">
                         <!-- Requires Extension -->
                        <div>
                            <InputLabel value="¿Necesita Prórroga?" class="mb-2" />
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" :value="true" v-model="form.requires_extension" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Sí</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" :value="false" v-model="form.requires_extension" class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">No</span>
                                </label>
                            </div>
                        </div>

                        <!-- Observations -->
                        <div>
                            <InputLabel for="observations" value="Observaciones" />
                            <textarea id="observations" v-model="form.observations" rows="4" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" placeholder="Notas adicionales..."></textarea>
                            <InputError :message="form.errors.observations" class="mt-2" />
                        </div>
                    </div>

                      <div class="border-t pt-4">
                         <InputLabel value="Archivos Adjuntos" class="mb-2" />
                         
                         <!-- Edit Mode -->
                         <div v-if="isEdit">
                            <ModelAttachments 
                                :model-id="license.id" 
                                model-type="App\Models\License" 
                                area-slug="licencias" 
                                :files="license.files" 
                            />
                         </div>

                         <!-- Create Mode -->
                         <div v-else>
                            <ModelAttachmentsCreator 
                                model-type="App\Models\License"
                                area-slug="licencias"
                                v-model:files="form.files"
                                v-model:pendingFileIds="form.pending_file_ids"
                            />
                            <p v-if="form.errors.files" class="text-red-500 text-sm mt-1">{{ form.errors.files }}</p>
                         </div>
                      </div>

                    <div class="flex justify-start space-x-3 pt-6 border-t">
                        <Link :href="route('licenses.index')" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Cancelar
                        </Link>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="bg-yellow-700 hover:bg-yellow-800 text-white">
                            {{ isEdit ? 'Actualizar' : 'Guardar' }}
                        </PrimaryButton>
                    </div>

                </form>
            </div>
        </main>
    </AppLayout>
</template>
