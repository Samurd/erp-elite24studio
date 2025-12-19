<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import MoneyInput from '@/Components/MoneyInput.vue';

const props = defineProps({
    apuCampaign: {
        type: Object,
        default: null,
    },
    campaigns: Array,
    unitOptions: Array,
});

const form = useForm({
    campaign_id: props.apuCampaign?.campaign_id || '',
    description: props.apuCampaign?.description || '',
    quantity: props.apuCampaign?.quantity || 1,
    unit_id: props.apuCampaign?.unit_id || '',
    unit_price: props.apuCampaign?.unit_price || 0,
    total_price: props.apuCampaign?.total_price || 0,
});

// Calculate total when quantity or unit_price changes
watch([() => form.quantity, () => form.unit_price], () => {
    const qty = parseFloat(form.quantity) || 0;
    const price = parseFloat(form.unit_price) || 0;
    form.total_price = qty * price;
});

const submit = () => {
    if (props.apuCampaign) {
        form.put(route('donations.apu-campaigns.update', props.apuCampaign.id), {
            preserveScroll: true,
        });
    } else {
        form.post(route('donations.apu-campaigns.store'), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Campaña -->
                <div>
                    <InputLabel for="campaign_id" value="Campaña *" />
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

                <!-- Descripción -->
                <div class="col-span-1 md:col-span-2">
                    <InputLabel for="description" value="Descripción *" />
                    <TextInput
                        id="description"
                        v-model="form.description"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Descripción del item"
                        required
                    />
                    <InputError :message="form.errors.description" class="mt-2" />
                </div>

                <!-- Cantidad -->
                <div>
                    <InputLabel for="quantity" value="Cantidad *" />
                    <TextInput
                        id="quantity"
                        v-model="form.quantity"
                        type="number"
                        min="1"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError :message="form.errors.quantity" class="mt-2" />
                </div>

                <!-- Unidad -->
                <div>
                    <InputLabel for="unit_id" value="Unidad" />
                    <select
                        id="unit_id"
                        v-model="form.unit_id"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    >
                        <option value="">Seleccionar unidad</option>
                        <option v-for="unit in unitOptions" :key="unit.id" :value="unit.id">
                            {{ unit.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.unit_id" class="mt-2" />
                </div>

                <!-- Valor Unitario -->
                <div>
                    <InputLabel for="unit_price" value="Valor Unitario *" />
                    <MoneyInput
                        id="unit_price"
                        v-model="form.unit_price"
                        class="mt-1 block w-full"
                        placeholder="$0.00"
                    />
                    <InputError :message="form.errors.unit_price" class="mt-2" />
                </div>

                <!-- Total -->
                <div>
                    <InputLabel for="total_price" value="Total" />
                    <MoneyInput
                        id="total_price"
                        v-model="form.total_price"
                        class="mt-1 block w-full bg-gray-100"
                        placeholder="$0.00"
                        disabled
                    />
                    <InputError :message="form.errors.total_price" class="mt-2" />
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <SecondaryButton @click="() => $inertia.visit(route('donations.apu-campaigns.index'))">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </SecondaryButton>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <i class="fas fa-save mr-2"></i>{{ apuCampaign ? 'Actualizar APU' : 'Guardar APU' }}
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
