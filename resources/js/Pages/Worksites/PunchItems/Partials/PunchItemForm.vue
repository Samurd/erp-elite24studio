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
    punchItem: Object,
    statusOptions: Array,
    responsibles: Array,
});

const form = useForm({
    status_id: props.punchItem ? props.punchItem.status_id : '',
    responsible_id: props.punchItem ? props.punchItem.responsible_id : '',
    observations: props.punchItem ? props.punchItem.observations : '',
});

const submit = () => {
    if (props.punchItem) {
        form.put(route('worksites.punch-items.update', [props.worksite.id, props.punchItem.id]));
    } else {
        form.post(route('worksites.punch-items.store', props.worksite.id));
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Estado -->
            <div>
                <InputLabel for="status_id" value="Estado" />
                <SelectInput
                    id="status_id"
                    v-model="form.status_id"
                    class="w-full"
                >
                    <option value="">Seleccionar estado</option>
                    <option v-for="status in statusOptions" :key="status.id" :value="status.id">
                        {{ status.name }}
                    </option>
                </SelectInput>
                <InputError :message="form.errors.status_id" class="mt-2" />
            </div>

            <!-- Responsable -->
            <div>
                <InputLabel for="responsible_id" value="Responsable" />
                <SelectInput
                    id="responsible_id"
                    v-model="form.responsible_id"
                    class="w-full"
                >
                    <option value="">Seleccionar responsable</option>
                    <option v-for="responsible in responsibles" :key="responsible.id" :value="responsible.id">
                        {{ responsible.name }}
                    </option>
                </SelectInput>
                <InputError :message="form.errors.responsible_id" class="mt-2" />
            </div>

            <!-- Observaciones -->
            <div class="md:col-span-2">
                <InputLabel for="observations" value="Observaciones" required />
                <TextArea
                    id="observations"
                    v-model="form.observations"
                    rows="6"
                    placeholder="Descripción detallada del Punch Item..."
                    class="w-full"
                    required
                />
                <InputError :message="form.errors.observations" class="mt-2" />
            </div>

            <!-- Información de la obra (solo lectura) -->
            <div>
                <InputLabel value="Obra Asociada" />
                <TextInput
                    type="text"
                    :value="worksite.name"
                    readonly
                    class="w-full bg-gray-50 text-gray-700"
                />
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
            <a :href="route('worksites.show', worksite.id)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="fas fa-times mr-2"></i>Cancelar
            </a>
            <PrimaryButton class="bg-yellow-600 hover:bg-yellow-700" :disabled="form.processing">
                <i class="fas fa-save mr-2"></i>
                {{ punchItem ? 'Actualizar' : 'Guardar' }} Punch Item
            </PrimaryButton>
        </div>
    </form>
</template>
