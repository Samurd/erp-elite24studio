<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue';
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue';

const props = defineProps({
    volunteer: {
        type: Object,
        default: null,
    },
    campaigns: Array,
    statusOptions: Array,
});

const form = useForm({
    name: props.volunteer?.name || '',
    email: props.volunteer?.email || '',
    phone: props.volunteer?.phone || '',
    address: props.volunteer?.address || '',
    city: props.volunteer?.city || '',
    state: props.volunteer?.state || '',
    country: props.volunteer?.country || '',
    role: props.volunteer?.role || '',
    campaign_id: props.volunteer?.campaign_id || '',
    status_id: props.volunteer?.status_id || '',
    certified: props.volunteer?.certified ? true : false,
    pending_file_ids: [],
    files: [],
});

const submit = () => {
    if (props.volunteer) {
        form.put(route('donations.volunteers.update', props.volunteer.id), {
            preserveScroll: true,
        });
    } else {
        form.post(route('donations.volunteers.store'), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form @submit.prevent="submit">
            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Información Personal</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Nombre -->
                <div class="md:col-span-2">
                    <InputLabel for="name" value="Nombre Completo *" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Nombre completo del voluntario"
                        required
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <InputLabel for="email" value="Correo Electrónico" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        placeholder="correo@ejemplo.com"
                    />
                    <InputError :message="form.errors.email" class="mt-2" />
                </div>

                <!-- Teléfono -->
                <div>
                    <InputLabel for="phone" value="Teléfono" />
                    <TextInput
                        id="phone"
                        v-model="form.phone"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="+57 300 123 4567"
                    />
                    <InputError :message="form.errors.phone" class="mt-2" />
                </div>
            </div>

            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Ubicación</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Dirección -->
                <div class="md:col-span-2">
                    <InputLabel for="address" value="Dirección" />
                    <TextInput
                        id="address"
                        v-model="form.address"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Dirección de residencia"
                    />
                    <InputError :message="form.errors.address" class="mt-2" />
                </div>

                <!-- Ciudad -->
                <div>
                    <InputLabel for="city" value="Ciudad" />
                    <TextInput
                        id="city"
                        v-model="form.city"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Ciudad"
                    />
                    <InputError :message="form.errors.city" class="mt-2" />
                </div>

                <!-- Estado/Departamento -->
                <div>
                    <InputLabel for="state" value="Estado / Departamento" />
                    <TextInput
                        id="state"
                        v-model="form.state"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Estado o Departamento"
                    />
                    <InputError :message="form.errors.state" class="mt-2" />
                </div>

                <!-- País -->
                <div>
                    <InputLabel for="country" value="País" />
                    <TextInput
                        id="country"
                        v-model="form.country"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="País"
                    />
                    <InputError :message="form.errors.country" class="mt-2" />
                </div>
            </div>

            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Detalles del Voluntariado</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Campaña -->
                <div>
                    <InputLabel for="campaign_id" value="Campaña Asignada" />
                    <select
                        id="campaign_id"
                        v-model="form.campaign_id"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    >
                        <option value="">Ninguna campaóa específica</option>
                        <option v-for="campaign in campaigns" :key="campaign.id" :value="campaign.id">
                            {{ campaign.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.campaign_id" class="mt-2" />
                </div>

                <!-- Estado -->
                <div>
                    <InputLabel for="status_id" value="Estado" />
                    <select
                        id="status_id"
                        v-model="form.status_id"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    >
                        <option value="">Seleccionar estado</option>
                        <option v-for="status in statusOptions" :key="status.id" :value="status.id">
                            {{ status.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.status_id" class="mt-2" />
                </div>

                <!-- Rol -->
                <div>
                    <InputLabel for="role" value="Rol / Cargo" />
                    <TextInput
                        id="role"
                        v-model="form.role"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Ej: Logística, Coordinador, Apoyo"
                    />
                    <InputError :message="form.errors.role" class="mt-2" />
                </div>

                <!-- Certificado -->
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <Checkbox v-model:checked="form.certified" name="certified" />
                        <span class="ml-2 text-sm text-gray-700">Certificado Entregado</span>
                    </label>
                    <InputError :message="form.errors.certified" class="mt-2" />
                </div>
            </div>

            <!-- Archivos -->
            <div class="mt-6 border-t pt-6">
                 <div v-if="!volunteer">
                    <ModelAttachmentsCreator 
                        v-model:files="form.files"
                        v-model:pendingFileIds="form.pending_file_ids" 
                        area-slug="donaciones"
                    />
                 </div>
                 <div v-else>
                     <ModelAttachments 
                        :model-id="volunteer.id"
                        :model-type="String('App\\Models\\Volunteer')"
                        area-slug="donaciones"
                     />
                 </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <SecondaryButton @click="() => $inertia.visit(route('donations.volunteers.index'))">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </SecondaryButton>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <i class="fas fa-save mr-2"></i>{{ volunteer ? 'Actualizar Voluntario' : 'Guardar Voluntario' }}
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
