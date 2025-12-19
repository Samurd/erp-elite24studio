<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue';
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue';

const props = defineProps({
    priorities: Array,
    priorities: Array,
    users: Array,
    approval: Object, // Optional: Passed when editing
});

const isEdit = computed(() => !!props.approval);

const form = useForm({
    name: props.approval?.name || '',
    buy: props.approval?.buy ? true : false,
    approvers: props.approval?.approvers?.map(a => a.user_id) || [],
    priority_id: props.approval?.priority_id || '',
    description: props.approval?.description || '',
    all_approvers: props.approval?.all_approvers ? true : false,
    files: [], 
    pending_file_ids: [],
});

const submit = () => {
    if (isEdit.value) {
        form.post(route('approvals.update', props.approval.id), {
             forceFormData: true,
        });
    } else {
        form.post(route('approvals.store'), {
            forceFormData: true, 
        });
    }
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
};
</script>

<template>
    <AppLayout title="Nueva Solicitud">
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    
                    <!-- Header -->
                    <div class="flex items-center gap-3 p-6 border-b bg-gray-50">
                        <div class="bg-yellow-700 text-white rounded p-2">
                             <i class="fas fa-file-signature"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">{{ isEdit ? 'Editar Solicitud' : 'Nueva Solicitud de Aprobación' }}</h2>
                            <p class="text-sm text-gray-500">{{ isEdit ? 'Modifica los detalles de tu solicitud' : 'Crea una nueva solicitud para tu equipo' }}</p>
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="p-6 space-y-6">
                        
                        <!-- Name is mandatory -->
                        <div>
                            <InputLabel for="name" value="Nombre de la solicitud *" />
                            <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" placeholder="Ej: Compra de Licencias" required />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <!-- Buy Checkbox -->
                         <div class="flex items-center">
                            <input id="buy" type="checkbox" v-model="form.buy" class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500" />
                            <label for="buy" class="ml-2 block text-sm text-gray-900">¿Es un solicitud de compra?</label>
                        </div>

                        <!-- Approvers (Multi Select) -->
                        <div>
                            <InputLabel for="approvers" value="Aprobadores *" />
                             <select id="approvers" v-model="form.approvers" multiple class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm h-32">
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Mantén presionado Ctrl (Winddows) o Cmd (Mac) para seleccionar múltiples</p>
                            <InputError :message="form.errors.approvers" class="mt-2" />
                        </div>

                        <!-- All Approvers Checkbox -->
                        <div class="flex items-center">
                            <input id="all_approvers" type="checkbox" v-model="form.all_approvers" class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500" />
                            <label for="all_approvers" class="ml-2 block text-sm text-gray-900">Solicitar respuesta de TODOS los destinatarios</label>
                        </div>

                        <!-- Priority -->
                        <div>
                             <InputLabel for="priority" value="Prioridad *" />
                             <select id="priority" v-model="form.priority_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                                <option value="" disabled>Selecciona una prioridad</option>
                                <option v-for="priority in priorities" :key="priority.id" :value="priority.id">{{ priority.name }}</option>
                            </select>
                            <InputError :message="form.errors.priority_id" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <InputLabel for="description" value="Detalles adicionales" />
                            <textarea id="description" v-model="form.description" rows="4" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm"></textarea>
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <!-- File Upload -->
                        <div class="mb-6">
                            <InputLabel value="Adjuntar Archivos" class="mb-2" />
                            
                            <!-- Edit Mode -->
                            <div v-if="isEdit">
                                <p class="text-xs text-yellow-600 mb-2 font-medium bg-yellow-50 p-2 rounded">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Editar la solicitud reiniciará el proceso de aprobación.
                                </p>
                                <ModelAttachments 
                                    :model-id="approval.id" 
                                    model-type="App\Models\Approval" 
                                    area-slug="aprobaciones" 
                                    :files="approval.files" 
                                />
                            </div>
                            
                            <!-- Create Mode -->
                            <div v-else>
                                <ModelAttachmentsCreator 
                                    model-type="App\Models\Approval"
                                    area-slug="aprobaciones"
                                    v-model:files="form.files"
                                    v-model:pendingFileIds="form.pending_file_ids"
                                />
                                <InputError :message="form.errors.files" class="mt-2" />
                            </div>
                        </div>

                    </div>

                    <!-- Actions -->
                     <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3 rounded-b-lg">
                        <Link :href="route('approvals.index')" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                            Cancelar
                        </Link>
                        <PrimaryButton @click="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ isEdit ? 'Actualizar Solicitud' : 'Crear Solicitud' }}
                        </PrimaryButton>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
