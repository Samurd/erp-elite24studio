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
    policy: Object,
    typeOptions: Array,
    statusOptions: Array,
    userOptions: Array,
});

const isEdit = computed(() => !!props.policy);

const form = useForm({
    _method: isEdit.value ? 'POST' : 'POST',
    name: props.policy?.name || '',
    type_id: props.policy?.type_id || '',
    status_id: props.policy?.status_id || '',
    assigned_to_id: props.policy?.assigned_to_id || '',
    issued_at: props.policy?.issued_at ? props.policy.issued_at.split('T')[0] : '',
    reviewed_at: props.policy?.reviewed_at ? props.policy.reviewed_at.split('T')[0] : '',
    description: props.policy?.description || '',
    description: props.policy?.description || '',
    files: [],
    pending_file_ids: [],
});

const submit = () => {
    if (isEdit.value) {
        form.post(route('policies.update', props.policy.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('policies.store'), {
            forceFormData: true,
        });
    }
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
};
</script>

<template>
    <AppLayout :title="isEdit ? 'Editar Política' : 'Nueva Política'">
        <main class="flex-1 p-10 bg-gray-100 font-sans">
             <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                         <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEdit ? 'Editar Política' : 'Nueva Política' }}
                        </h1>
                        <p class="text-gray-600 mt-1">{{ isEdit ? 'Editar información de la política' : 'Crear una nueva política en el sistema' }}</p>
                    </div>
                    <div class="flex space-x-3">
                         <Link :href="route('policies.index')" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </Link>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">

                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Left Column -->
                        <div class="space-y-6">
                             <!-- Name -->
                            <div>
                                 <InputLabel for="name" value="Nombre de la Política" />
                                 <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" placeholder="Ej: Política de Seguridad de la Información" required />
                                 <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                             <!-- Type -->
                            <div>
                                <InputLabel for="type_id" value="Tipo de Política" />
                                <select id="type_id" v-model="form.type_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option v-for="option in typeOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                                </select>
                                <InputError :message="form.errors.type_id" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <InputLabel for="status_id" value="Estado" />
                                <select id="status_id" v-model="form.status_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                                    <option value="">Seleccionar estado...</option>
                                    <option v-for="option in statusOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                                </select>
                                <InputError :message="form.errors.status_id" class="mt-2" />
                            </div>

                             <!-- Assigned To -->
                            <div>
                                <InputLabel for="assigned_to_id" value="Responsable" />
                                <select id="assigned_to_id" v-model="form.assigned_to_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                                    <option value="">Seleccionar responsable...</option>
                                    <option v-for="option in userOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                                </select>
                                <InputError :message="form.errors.assigned_to_id" class="mt-2" />
                            </div>

                        </div>

                        <!-- Right Column -->
                         <div class="space-y-6">
                             <!-- Issued At -->
                            <div>
                                 <InputLabel for="issued_at" value="Fecha de Emisión" />
                                 <TextInput id="issued_at" v-model="form.issued_at" type="date" class="mt-1 block w-full" required />
                                 <InputError :message="form.errors.issued_at" class="mt-2" />
                            </div>

                             <!-- Reviewed At -->
                            <div>
                                 <InputLabel for="reviewed_at" value="Fecha de Última Revisión" />
                                 <TextInput id="reviewed_at" v-model="form.reviewed_at" type="date" class="mt-1 block w-full" />
                                 <InputError :message="form.errors.reviewed_at" class="mt-2" />
                            </div>

                             <!-- Description -->
                            <div>
                                <InputLabel for="description" value="Descripción" />
                                <textarea id="description" v-model="form.description" rows="4" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" placeholder="Descripción detallada de la política..."></textarea>
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>
                        </div>

                    </div>

                    <!-- Files -->
                     <div class="border-t pt-4">
                        <InputLabel value="Archivos Adjuntos" class="mb-2" />
                        
                        <!-- Edit Mode -->
                        <div v-if="isEdit">
                            <ModelAttachments 
                                :model-id="policy.id" 
                                model-type="App\Models\Policy" 
                                area-slug="politicas" 
                                :files="policy.files" 
                            />
                        </div>

                        <!-- Create Mode -->
                        <div v-else>
                            <ModelAttachmentsCreator 
                                model-type="App\Models\Policy"
                                area-slug="politicas"
                                v-model:files="form.files"
                                v-model:pendingFileIds="form.pending_file_ids"
                            />
                            <p v-if="form.errors.files" class="text-red-500 text-sm mt-1">{{ form.errors.files }}</p>
                        </div>
                     </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t">
                        <Link :href="route('policies.index')" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Cancelar
                        </Link>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-save mr-2"></i> {{ isEdit ? 'Guardar Política' : 'Guardar Política' }}
                        </PrimaryButton>
                    </div>

                </form>
            </div>
        </main>
    </AppLayout>
</template>
