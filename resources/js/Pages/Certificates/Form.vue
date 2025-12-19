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
    certificate: Object,
    typeOptions: Array,
    statusOptions: Array,
    userOptions: Array,
});

const isEdit = computed(() => !!props.certificate);

const form = useForm({
    _method: isEdit.value ? 'POST' : 'POST',
    name: props.certificate?.name || '',
    type_id: props.certificate?.type_id || '',
    status_id: props.certificate?.status_id || '',
    assigned_to_id: props.certificate?.assigned_to_id || '',
    issued_at: props.certificate?.issued_at ? props.certificate.issued_at.split('T')[0] : '',
    expires_at: props.certificate?.expires_at ? props.certificate.expires_at.split('T')[0] : '',
    description: props.certificate?.description || '',
    description: props.certificate?.description || '',
    files: [],
    pending_file_ids: [],
});

const submit = () => {
    if (isEdit.value) {
        form.post(route('certificates.update', props.certificate.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('certificates.store'), {
            forceFormData: true,
        });
    }
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
};
</script>

<template>
    <AppLayout :title="isEdit ? 'Actualizar Certificado' : 'Añadir nuevo Certificado'">
        <main class="flex-1 p-10 bg-gray-100 font-sans">
             <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                         <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEdit ? 'Actualizar Certificado' : 'Añadir nuevo Certificado' }}
                        </h1>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">

                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div class="grid grid-cols-1 gap-6">
                        
                         <!-- Name -->
                        <div>
                             <InputLabel for="name" value="Nombre del Certificado" />
                             <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" placeholder="Nombre del certificado" required />
                             <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                         <!-- Status -->
                        <div>
                            <InputLabel for="status_id" value="Estado" />
                            <select id="status_id" v-model="form.status_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                                <option value="">Seleccionar</option>
                                <option v-for="option in statusOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                            </select>
                            <InputError :message="form.errors.status_id" class="mt-2" />
                        </div>

                         <!-- Type -->
                        <div>
                            <InputLabel for="type_id" value="Tipo" />
                            <select id="type_id" v-model="form.type_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                                <option value="">Seleccionar</option>
                                <option v-for="option in typeOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                            </select>
                            <InputError :message="form.errors.type_id" class="mt-2" />
                        </div>

                         <!-- Issued At -->
                        <div>
                             <InputLabel for="issued_at" value="Fecha de Emisión" />
                             <TextInput id="issued_at" v-model="form.issued_at" type="date" class="mt-1 block w-full" required />
                             <InputError :message="form.errors.issued_at" class="mt-2" />
                        </div>

                         <!-- Expires At -->
                        <div>
                             <InputLabel for="expires_at" value="Fecha de Vencimiento" />
                             <TextInput id="expires_at" v-model="form.expires_at" type="date" class="mt-1 block w-full" />
                             <InputError :message="form.errors.expires_at" class="mt-2" />
                        </div>

                         <!-- Assigned To -->
                        <div>
                            <InputLabel for="assigned_to_id" value="Responsable" />
                            <select id="assigned_to_id" v-model="form.assigned_to_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                                <option value="">Seleccionar</option>
                                <option v-for="option in userOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                            </select>
                            <InputError :message="form.errors.assigned_to_id" class="mt-2" />
                        </div>

                         <!-- Description -->
                        <div>
                            <InputLabel for="description" value="Observaciones" />
                            <textarea id="description" v-model="form.description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" placeholder="Agregue sus observaciones"></textarea>
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                    </div>

                    <!-- Files -->
                     <div class="border-t pt-4">
                        <InputLabel value="Archivos Adjuntos" class="mb-2" />
                        
                        <!-- Edit Mode -->
                        <div v-if="isEdit">
                            <ModelAttachments 
                                :model-id="certificate.id" 
                                model-type="App\Models\Certificate" 
                                area-slug="certificados" 
                                :files="certificate.files" 
                            />
                        </div>

                        <!-- Create Mode -->
                        <div v-else>
                            <ModelAttachmentsCreator 
                                model-type="App\Models\Certificate"
                                area-slug="certificados"
                                v-model:files="form.files"
                                v-model:pendingFileIds="form.pending_file_ids"
                            />
                            <p v-if="form.errors.files" class="text-red-500 text-sm mt-1">{{ form.errors.files }}</p>
                        </div>
                     </div>

                    <div class="flex justify-start space-x-3 pt-6 border-t">
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="bg-yellow-700 hover:bg-yellow-800 text-white">
                            {{ isEdit ? 'Actualizar' : 'Crear' }}
                        </PrimaryButton>
                    </div>

                </form>
            </div>
        </main>
    </AppLayout>
</template>
