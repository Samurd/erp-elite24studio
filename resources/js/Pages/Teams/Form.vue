<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    team: Object, // Optional for Edit mode reuse (though usually we split or wrap)
});

const form = useForm({
    name: props.team?.name || '',
    description: props.team?.description || '',
    isPublic: props.team ? !!props.team.isPublic : true,
});

const submit = () => {
    if (props.team) {
        form.put(route('teams.update', props.team.id));
    } else {
        form.post(route('teams.store'));
    }
};
</script>

<template>
    <div class="p-6 bg-white rounded-lg shadow-sm border border-gray-200">
        <form @submit.prevent="submit">
            <div class="mb-4">
                <InputLabel for="name" value="Nombre del Equipo" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div class="mb-4">
                <InputLabel for="description" value="Descripción" />
                <textarea
                    id="description"
                    v-model="form.description"
                    class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm"
                    rows="3"
                ></textarea>
                <InputError :message="form.errors.description" class="mt-2" />
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.isPublic" />
                    <span class="ml-2 text-sm text-gray-600">Equipo Público (visible para todos)</span>
                </label>
            </div>

            <div class="flex items-center justify-end">
                <Link :href="route('teams.index')" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                    Cancelar
                </Link>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ team ? 'Actualizar' : 'Crear Equipo' }}
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
