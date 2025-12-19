<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import MoneyInput from '@/Components/MoneyInput.vue';
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue';
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue';

const props = defineProps({
    sub: Object,
    statusOptions: Array,
    frequencyOptions: Array,
});

const isEdit = computed(() => !!props.sub);

const form = useForm({
    _method: isEdit.value ? 'POST' : 'POST',
    name: props.sub?.name || '',
    frequency_id: props.sub?.frequency_id || '',
    type: props.sub?.type || '',
    amount: props.sub?.amount || 0, // Ensure this matches MoneyInput expectations
    status_id: props.sub?.status_id || '',
    start_date: props.sub?.start_date ? props.sub.start_date.split('T')[0] : '',
    renewal_date: props.sub?.renewal_date ? props.sub.renewal_date.split('T')[0] : '',
    notes: props.sub?.notes || '',
    files: [],
    pending_file_ids: [],
});

const submit = () => {
    if (isEdit.value) {
        form.post(route('subs.update', props.sub.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('subs.store'), {
            forceFormData: true,
        });
    }
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
};
</script>

<template>
    <AppLayout :title="isEdit ? 'Editar Suscripción' : 'Nueva Suscripción'">
        <main class="flex-1 p-10 bg-gray-100 font-sans">
            <div class="bg-white rounded-lg shadow-sm p-6 max-w-4xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ isEdit ? 'Editar Suscripción' : 'Nueva Suscripción' }}
                    </h1>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Name -->
                        <div class="md:col-span-2">
                             <InputLabel for="name" value="Nombre *" />
                             <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                             <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <!-- Frequency -->
                        <div>
                            <InputLabel for="frequency" value="Frecuencia *" />
                            <select id="frequency" v-model="form.frequency_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                                <option value="">Seleccione una frecuencia</option>
                                <option v-for="option in frequencyOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                            </select>
                            <InputError :message="form.errors.frequency_id" class="mt-2" />
                        </div>

                         <!-- Type -->
                         <div>
                             <InputLabel for="type" value="Tipo *" />
                             <TextInput id="type" v-model="form.type" type="text" class="mt-1 block w-full" placeholder="Ej: Software, Servicio..." required />
                             <InputError :message="form.errors.type" class="mt-2" />
                        </div>

                        <!-- Amount -->
                         <div>
                             <MoneyInput 
                                id="amount" 
                                v-model="form.amount" 
                                label="Monto *" 
                                :error="form.errors.amount" 
                             />
                        </div>

                         <!-- Status -->
                        <div>
                            <InputLabel for="status" value="Estado *" />
                            <select id="status" v-model="form.status_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                                <option value="">Seleccione un estado</option>
                                <option v-for="option in statusOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                            </select>
                            <InputError :message="form.errors.status_id" class="mt-2" />
                        </div>

                         <!-- Start Date -->
                         <div>
                             <InputLabel for="start_date" value="Fecha Inicio *" />
                             <TextInput id="start_date" v-model="form.start_date" type="date" class="mt-1 block w-full" required />
                             <InputError :message="form.errors.start_date" class="mt-2" />
                        </div>

                        <!-- Renewal Date -->
                         <div>
                             <InputLabel for="renewal_date" value="Fecha Renovación *" />
                             <TextInput id="renewal_date" v-model="form.renewal_date" type="date" class="mt-1 block w-full" required />
                             <InputError :message="form.errors.renewal_date" class="mt-2" />
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
                                :model-id="sub.id" 
                                model-type="App\Models\Sub" 
                                area-slug="finanzas" 
                                :files="sub.files" 
                            />
                        </div>

                        <!-- Create Mode -->
                        <div v-else>
                            <ModelAttachmentsCreator 
                                model-type="App\Models\Sub"
                                area-slug="finanzas"
                                v-model:files="form.files"
                                v-model:pendingFileIds="form.pending_file_ids"
                            />
                            <p v-if="form.errors.files" class="text-red-500 text-sm mt-1">{{ form.errors.files }}</p>
                        </div>
                     </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t">
                        <Link :href="route('subs.index')" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </Link>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ isEdit ? 'Actualizar Suscripción' : 'Guardar Suscripción' }}
                        </PrimaryButton>
                    </div>

                </form>
            </div>
        </main>
    </AppLayout>
</template>
