<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextArea from '@/Components/TextArea.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    worksite: Object,
    visit: Object,
    visitStatusOptions: Array,
    visitorOptions: Array,
});

const form = useForm({
    visit_date: props.visit ? props.visit.visit_date : '',
    performed_by: props.visit ? props.visit.performed_by : '',
    status_id: props.visit ? props.visit.status_id : '',
    general_observations: props.visit ? props.visit.general_observations : '',
    internal_notes: props.visit ? props.visit.internal_notes : '',
});

const submit = () => {
    if (props.visit) {
        form.put(route('worksites.visits.update', [props.worksite.id, props.visit.id]));
    } else {
        form.post(route('worksites.visits.store', props.worksite.id));
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Información de la Obra (solo lectura) -->
            <div class="md:col-span-2">
                <InputLabel value="Obra" />
                <TextInput
                    type="text"
                    :value="worksite.name"
                    readonly
                    class="w-full bg-gray-50 text-gray-700"
                />
            </div>

            <!-- ID de la Visita (solo lectura, si es edición) -->
            <div v-if="visit">
                <InputLabel value="ID de Visita" />
                <TextInput
                    type="text"
                    :value="'#' + visit.id"
                    readonly
                    class="w-full bg-gray-50 text-gray-700"
                />
            </div>

            <!-- Fecha de Visita -->
            <div>
                <InputLabel for="visit_date" value="Fecha de Visita" required />
                <TextInput
                    id="visit_date"
                    type="date"
                    v-model="form.visit_date"
                    class="w-full"
                    required
                />
                <InputError :message="form.errors.visit_date" class="mt-2" />
            </div>

            <!-- Visitante -->
            <div>
                <InputLabel for="performed_by" value="Visita realizada por" />
                <SelectInput
                    id="performed_by"
                    v-model="form.performed_by"
                    class="w-full"
                >
                    <option value="">Seleccionar visitante...</option>
                    <option v-for="visitor in visitorOptions" :key="visitor.id" :value="visitor.id">
                        {{ visitor.name }}
                    </option>
                </SelectInput>
                <InputError :message="form.errors.performed_by" class="mt-2" />
            </div>

            <!-- Estado -->
            <div>
                <InputLabel for="status_id" value="Estado" />
                <SelectInput
                    id="status_id"
                    v-model="form.status_id"
                    class="w-full"
                >
                    <option value="">Seleccionar estado...</option>
                    <option v-for="status in visitStatusOptions" :key="status.id" :value="status.id">
                        {{ status.name }}
                    </option>
                </SelectInput>
                <InputError :message="form.errors.status_id" class="mt-2" />
            </div>

            <!-- Observaciones Generales -->
            <div class="md:col-span-2">
                <InputLabel for="general_observations" value="Observaciones Generales" />
                <TextArea
                    id="general_observations"
                    v-model="form.general_observations"
                    rows="2"
                    placeholder="Observaciones generales sobre la visita..."
                    class="w-full"
                />
                <InputError :message="form.errors.general_observations" class="mt-2" />
            </div>

            <!-- Notas Internas -->
            <div class="md:col-span-2">
                <InputLabel for="internal_notes" value="Notas Internas" />
                <TextArea
                    id="internal_notes"
                    v-model="form.internal_notes"
                    rows="2"
                    placeholder="Notas internas sobre la visita..."
                    class="w-full"
                />
                <InputError :message="form.errors.internal_notes" class="mt-2" />
            </div>

            <!-- Información adicional (solo lectura, si es edición) -->
            <div v-if="visit">
                <InputLabel value="Fecha de Creación" />
                <TextInput
                    type="text"
                    :value="new Date(visit.created_at).toLocaleString()"
                    readonly
                    class="w-full bg-gray-50 text-gray-700"
                />
            </div>
             <div v-if="visit">
                <InputLabel value="Última Actualización" />
                <TextInput
                    type="text"
                    :value="new Date(visit.updated_at).toLocaleString()"
                    readonly
                    class="w-full bg-gray-50 text-gray-700"
                />
            </div>
        </div>

        <!-- Botones -->
        <div class="flex justify-end space-x-3 mt-6">
            <a :href="route('worksites.show', worksite.id)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <PrimaryButton class="bg-yellow-600 hover:bg-yellow-700" :disabled="form.processing">
                <i class="fas fa-save mr-2"></i>
                {{ visit ? 'Actualizar Visita' : 'Guardar Visita' }}
            </PrimaryButton>
        </div>
    </form>
</template>
