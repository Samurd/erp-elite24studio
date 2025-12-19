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
    caseRecord: Object, // Existing record if editing
    contacts: Array,
    users: Array,
    states: Array, // Filtered by category
    case_types: Array, // Filtered by category
    defaultUserId: Number,
    currentDate: String,
});

const isEdit = computed(() => !!props.caseRecord);

const form = useForm({
    _method: isEdit.value ? 'POST' : 'POST', // Use POST for both, spoof PUT if editing for file handling quirks
    date: props.caseRecord?.date || props.currentDate,
    channel: props.caseRecord?.channel || '',
    case_type_id: props.caseRecord?.case_type_id || '',
    status_id: props.caseRecord?.status_id || '',
    assigned_to_id: props.caseRecord?.assigned_to_id || props.defaultUserId,
    contact_id: props.caseRecord?.contact_id || '',
    description: props.caseRecord?.description || '',
    description: props.caseRecord?.description || '',
    files: [],
    pending_file_ids: [],
});

const submit = () => {
    if (isEdit.value) {
        // Inertia spoofing for file uploads with PUT
        form.post(route('case-record.update', props.caseRecord.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('case-record.store'), {
            forceFormData: true,
        });
    }
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
};
</script>

<template>
    <AppLayout :title="isEdit ? 'Editar Caso' : 'Nuevo Registro de Caso'">
        <main class="flex-1 p-10 bg-gray-100 overflow-y-auto">
            <div class="bg-white w-full p-6 rounded-lg shadow">
                
                <!-- Header -->
                <div class="mb-6 border-b pb-4">
                    <h3 class="text-2xl font-bold leading-none">Registro de casos</h3>
                    <p class="text-gray-500">Área Comercial</p>
                    <p class="text-sm text-gray-400 mt-2">Diligencia todos los campos solicitados.</p>
                </div>

                <form @submit.prevent="submit">
                    
                    <!-- Grid Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        
                        <!-- Date -->
                        <div class="md:col-span-1">
                            <InputLabel for="date" value="Fecha" />
                            <TextInput id="date" v-model="form.date" type="date" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.date" class="mt-2" />
                        </div>

                         <!-- Channel -->
                         <div class="md:col-span-3"> <!-- Fix col span for better layout -->
                           <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <InputLabel for="channel" value="Canal" />
                                    <TextInput id="channel" v-model="form.channel" type="text" placeholder="Ej: WhatsApp, Correo..." class="mt-1 block w-full" required />
                                    <InputError :message="form.errors.channel" class="mt-2" />
                                </div>

                                <!-- Case Type -->
                                <div>
                                    <InputLabel for="case_type_id" value="Tipo de Caso" />
                                    <select id="case_type_id" v-model="form.case_type_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                                        <option value="">Seleccionar</option>
                                        <option v-for="type in case_types" :key="type.id" :value="type.id">{{ type.name }}</option>
                                    </select>
                                    <InputError :message="form.errors.case_type_id" class="mt-2" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <InputLabel for="status_id" value="Estado" />
                                    <select id="status_id" v-model="form.status_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                                        <option value="">Seleccionar</option>
                                        <option v-for="state in states" :key="state.id" :value="state.id">{{ state.name }}</option>
                                    </select>
                                    <InputError :message="form.errors.status_id" class="mt-2" />
                                </div>
                           </div>
                        </div>

                        <!-- Advisor -->
                         <div class="md:col-span-2">
                            <InputLabel for="assigned_to_id" value="Asesor" />
                            <select id="assigned_to_id" v-model="form.assigned_to_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                                <option value="">Seleccionar</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                            <InputError :message="form.errors.assigned_to_id" class="mt-2" />
                        </div>

                        <!-- Contact -->
                        <div class="md:col-span-2">
                            <InputLabel for="contact_id" value="Contacto" />
                            <select id="contact_id" v-model="form.contact_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                                <option value="">Seleccionar Contacto</option>
                                <option v-for="contact in contacts" :key="contact.id" :value="contact.id">{{ contact.name }}</option>
                            </select>
                            <InputError :message="form.errors.contact_id" class="mt-2" />
                        </div>

                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <InputLabel for="description" value="Descripción / Observaciones" />
                        <textarea id="description" v-model="form.description" rows="5" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm"></textarea>
                        <InputError :message="form.errors.description" class="mt-2" />
                    </div>

                    <!-- Attachments -->
                    <div class="mb-6">
                        <InputLabel value="Archivos Adjuntos" class="mb-2" />
                        
                        <!-- Edit Mode: Full Manager -->
                        <div v-if="isEdit">
                             <ModelAttachments 
                                :model-id="caseRecord.id" 
                                model-type="App\Models\CaseRecord" 
                                area-slug="registro-casos" 
                                :files="caseRecord.files" 
                            />
                        </div>

                        <!-- Create Mode: Creator -->
                        <div v-else>
                            <ModelAttachmentsCreator 
                                model-type="App\Models\CaseRecord"
                                area-slug="registro-casos"
                                v-model:files="form.files"
                                v-model:pendingFileIds="form.pending_file_ids"
                            />
                             <InputError :message="form.errors.files" class="mt-2" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between border-t pt-6">
                        <Link :href="route('case-record.index')" class="text-gray-600 hover:text-gray-800">Cancelar</Link>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ isEdit ? 'Actualizar Registro' : 'Crear Registro' }}
                        </PrimaryButton>
                    </div>

                </form>

            </div>
        </main>
    </AppLayout>
</template>
