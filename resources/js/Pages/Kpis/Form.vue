<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    kpi: {
        type: Object,
        default: null,
    },
    roles: Array,
});

const form = useForm({
    periodicity_days: props.kpi?.periodicity_days || 30,
    indicator_name: props.kpi?.indicator_name || '',
    target_value: props.kpi?.target_value || '',
    role_id: props.kpi?.role_id || '',
});

const submit = () => {
    if (props.kpi) {
        form.put(route('kpis.update', props.kpi.id), {
            preserveScroll: true,
        });
    } else {
        form.post(route('kpis.store'), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Nombre del Indicador -->
                <div class="col-span-1 md:col-span-2">
                    <InputLabel for="indicator_name" value="Nombre del Indicador *" />
                    <TextInput
                        id="indicator_name"
                        v-model="form.indicator_name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Ej: Satisfacción del Cliente"
                        required
                    />
                    <InputError :message="form.errors.indicator_name" class="mt-2" />
                </div>

                <!-- Valor Objetivo (Meta) -->
                <div>
                    <InputLabel for="target_value" value="Valor Objetivo (Meta)" />
                    <TextInput
                        id="target_value"
                        v-model="form.target_value"
                        type="number"
                        step="0.01"
                        class="mt-1 block w-full"
                        placeholder="Ej: 90"
                    />
                    <InputError :message="form.errors.target_value" class="mt-2" />
                </div>

                <!-- Periodicidad (Días) -->
                <div>
                    <InputLabel for="periodicity_days" value="Periodicidad (Días) *" />
                    <TextInput
                        id="periodicity_days"
                        v-model="form.periodicity_days"
                        type="number"
                        min="1"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError :message="form.errors.periodicity_days" class="mt-2" />
                </div>

                <!-- Rol Responsable -->
                <div>
                    <InputLabel for="role_id" value="Rol Responsable *" />
                    <select
                        id="role_id"
                        v-model="form.role_id"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required
                    >
                        <option value="">Seleccionar rol</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">
                            {{ role.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.role_id" class="mt-2" />
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <SecondaryButton @click="() => $inertia.visit(route('kpis.index'))">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </SecondaryButton>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <i class="fas fa-save mr-2"></i>{{ kpi ? 'Actualizar KPI' : 'Guardar KPI' }}
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
