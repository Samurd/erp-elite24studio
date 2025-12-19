<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import MoneyInput from '@/Components/MoneyInput.vue';

const props = defineProps({
    campaign: {
        type: Object,
        default: null,
    },
    statusOptions: Array,
    users: Array,
});

const form = useForm({
    name: props.campaign?.name || '',
    date_event: props.campaign?.date_event ? props.campaign.date_event.split('T')[0] : '', // Handle date format
    address: props.campaign?.address || '',
    responsible_id: props.campaign?.responsible_id || '',
    status_id: props.campaign?.status_id || '',
    goal: props.campaign?.goal || null,
    estimated_budget: props.campaign?.estimated_budget || null,
    alliances: props.campaign?.alliances || '',
    description: props.campaign?.description || '',
});

const submit = () => {
    if (props.campaign) {
        form.put(route('donations.campaigns.update', props.campaign.id), {
            preserveScroll: true,
        });
    } else {
        form.post(route('donations.campaigns.store'), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Nombre -->
                <div class="lg:col-span-2">
                    <InputLabel for="name" value="Nombre de la Campaña *" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Nombre de la campaña"
                        required
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <!-- Fecha del Evento -->
                <div>
                    <InputLabel for="date_event" value="Fecha del Evento" />
                    <TextInput
                        id="date_event"
                        v-model="form.date_event"
                        type="date"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.date_event" class="mt-2" />
                </div>

                <!-- Dirección -->
                <div class="lg:col-span-2">
                    <InputLabel for="address" value="Lugar del Evento" />
                    <TextInput
                        id="address"
                        v-model="form.address"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Dirección o lugar del evento"
                    />
                    <InputError :message="form.errors.address" class="mt-2" />
                </div>

                <!-- Responsable -->
                <div>
                    <InputLabel for="responsible_id" value="Responsable" />
                    <select
                        id="responsible_id"
                        v-model="form.responsible_id"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    >
                        <option value="">Seleccionar responsable</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">
                            {{ user.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.responsible_id" class="mt-2" />
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

                <!-- Meta -->
                <div>
                    <InputLabel for="goal" value="Meta" />
                    <MoneyInput
                        id="goal"
                        v-model="form.goal"
                        class="mt-1 block w-full"
                        placeholder="$0.00"
                    />
                    <InputError :message="form.errors.goal" class="mt-2" />
                </div>

                <!-- Presupuesto Estimado -->
                <div>
                    <InputLabel for="estimated_budget" value="Presupuesto Estimado" />
                    <MoneyInput
                        id="estimated_budget"
                        v-model="form.estimated_budget"
                        class="mt-1 block w-full"
                        placeholder="$0.00"
                    />
                    <InputError :message="form.errors.estimated_budget" class="mt-2" />
                </div>
            </div>

            <!-- Alianzas -->
            <div class="grid grid-cols-1 gap-6 mt-6">
                <div>
                    <InputLabel for="alliances" value="Alianzas" />
                    <textarea
                        id="alliances"
                        v-model="form.alliances"
                        rows="3"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        placeholder="Describa las alianzas involucradas en la campaña..."
                    ></textarea>
                    <InputError :message="form.errors.alliances" class="mt-2" />
                </div>

                <!-- Descripción -->
                <div>
                    <InputLabel for="description" value="Descripción" />
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="4"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        placeholder="Descripción detallada de la campaña..."
                    ></textarea>
                    <InputError :message="form.errors.description" class="mt-2" />
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <SecondaryButton @click="() => $inertia.visit(route('donations.campaigns.index'))">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </SecondaryButton>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <i class="fas fa-save mr-2"></i>{{ campaign ? 'Actualizar' : 'Guardar' }} Campaña
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
