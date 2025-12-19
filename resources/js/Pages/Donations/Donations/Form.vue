<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import MoneyInput from '@/Components/MoneyInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue';
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue';

const props = defineProps({
    donation: {
        type: Object,
        default: null,
    },
    campaigns: Array,
});

const form = useForm({
    name: props.donation?.name || '',
    campaign_id: props.donation?.campaign_id || '',
    amount: props.donation?.amount || null,
    payment_method: props.donation?.payment_method || '',
    date: props.donation?.date ? props.donation.date.split('T')[0] : new Date().toISOString().split('T')[0],
    certified: props.donation?.certified ? true : false,
    pending_file_ids: [],
    files: [],
});

const submit = () => {
    if (props.donation) {
        form.put(route('donations.donations.update', props.donation.id), {
            preserveScroll: true,
        });
    } else {
        form.post(route('donations.donations.store'), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre del Donante -->
                <div class="md:col-span-2">
                    <InputLabel for="name" value="Nombre del Donante *" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Ingrese el nombre del donante o entidad"
                        required
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <!-- Campaña -->
                <div>
                    <InputLabel for="campaign_id" value="Campaña" />
                    <select
                        id="campaign_id"
                        v-model="form.campaign_id"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    >
                        <option value="">Seleccionar campaña</option>
                        <option v-for="campaign in campaigns" :key="campaign.id" :value="campaign.id">
                            {{ campaign.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.campaign_id" class="mt-2" />
                </div>

                <!-- Monto -->
                <div>
                    <InputLabel for="amount" value="Monto" />
                    <MoneyInput
                        id="amount"
                        v-model="form.amount"
                        class="mt-1 block w-full"
                        placeholder="$0.00"
                    />
                    <InputError :message="form.errors.amount" class="mt-2" />
                </div>

                <!-- Método de Pago -->
                <div>
                    <InputLabel for="payment_method" value="Método de Pago *" />
                    <TextInput
                        id="payment_method"
                        v-model="form.payment_method"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Ej: Transferencia, Efectivo, Tarjeta"
                        required
                    />
                    <InputError :message="form.errors.payment_method" class="mt-2" />
                </div>

                <!-- Fecha -->
                <div>
                    <InputLabel for="date" value="Fecha de Donación *" />
                    <TextInput
                        id="date"
                        v-model="form.date"
                        type="date"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError :message="form.errors.date" class="mt-2" />
                </div>

                <!-- Certificado -->
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <Checkbox v-model:checked="form.certified" name="certified" />
                        <span class="ml-2 text-sm text-gray-700">Certificado</span>
                    </label>
                    <InputError :message="form.errors.certified" class="mt-2" />
                </div>
            </div>

            <!-- Archivos -->
            <div class="mt-6 border-t pt-6">
                 <div v-if="!donation">
                    <ModelAttachmentsCreator 
                        v-model:files="form.files"
                        v-model:pendingFileIds="form.pending_file_ids" 
                        area-slug="donaciones"
                    />
                 </div>
                 <div v-else>
                     <ModelAttachments 
                        :model-id="donation.id"
                        :model-type="String('App\\Models\\Donation')"
                        area-slug="donaciones"
                     />
                 </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <SecondaryButton @click="() => $inertia.visit(route('donations.donations.index'))">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </SecondaryButton>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <i class="fas fa-save mr-2"></i>{{ donation ? 'Actualizar Donación' : 'Guardar Donación' }}
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
