<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    meeting: Object,
    statusOptions: Array,
    teamOptions: Array,
    userOptions: Array,
});

const formatTimeForInput = (isoString) => {
    if (!isoString) return '';
    const date = new Date(isoString);
    if (isNaN(date.getTime())) return isoString.substring(0, 5); // Fallback
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
};

const form = useForm({
    title: props.meeting?.title || '',
    date: props.meeting?.date ? props.meeting.date.split('T')[0] : '', // Format for date input
    start_time: props.meeting?.start_time ? formatTimeForInput(props.meeting.start_time) : '', // Format HH:MM
    end_time: props.meeting?.end_time ? formatTimeForInput(props.meeting.end_time) : '',
    team_id: props.meeting?.team_id || '',
    status_id: props.meeting?.status_id || '',
    notes: props.meeting?.notes || '',
    observations: props.meeting?.observations || '',
    url: props.meeting?.url || '',
    goal: props.meeting?.goal ? true : false,
    responsibles: props.meeting?.responsibles?.map(u => u.id) || [],
});

const submit = () => {
    if (props.meeting) {
        form.put(route('meetings.update', props.meeting.id));
    } else {
        form.post(route('meetings.store'));
    }
};
</script>

<template>
    <AppLayout :title="meeting ? 'Editar Reunión' : 'Nueva Reunión'">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ meeting ? 'Editar Reunión' : 'Nueva Reunión' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">{{ meeting ? 'Editar Reunión' : 'Nueva Reunión' }}</h1>
                            <p class="text-gray-600 mt-1">{{ meeting ? 'Actualizar información de la reunión' : 'Crear una nueva reunión en el sistema' }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <Link :href="route('meetings.index')"
                               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>Volver
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Title -->
                                <div>
                                    <InputLabel for="title" value="Título de la Reunión *" />
                                    <TextInput id="title" v-model="form.title" type="text" class="mt-1 block w-full" placeholder="Ej: Reunión de seguimiento..." />
                                    <InputError :message="form.errors.title" class="mt-2" />
                                </div>

                                <!-- Date -->
                                <div>
                                    <InputLabel for="date" value="Fecha *" />
                                    <TextInput id="date" v-model="form.date" type="date" class="mt-1 block w-full" />
                                    <InputError :message="form.errors.date" class="mt-2" />
                                </div>

                                <!-- Time Range -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="start_time" value="Hora Inicio *" />
                                        <TextInput id="start_time" v-model="form.start_time" type="time" class="mt-1 block w-full" />
                                        <InputError :message="form.errors.start_time" class="mt-2" />
                                    </div>
                                    <div>
                                        <InputLabel for="end_time" value="Hora Fin *" />
                                        <TextInput id="end_time" v-model="form.end_time" type="time" class="mt-1 block w-full" />
                                        <InputError :message="form.errors.end_time" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Team -->
                                <div>
                                    <InputLabel for="team_id" value="Equipo Responsable" />
                                    <select id="team_id" v-model="form.team_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Seleccionar equipo...</option>
                                        <option v-for="team in teamOptions" :key="team.id" :value="team.id">{{ team.name }}</option>
                                    </select>
                                    <InputError :message="form.errors.team_id" class="mt-2" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <InputLabel for="status_id" value="Estado" />
                                    <select id="status_id" v-model="form.status_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">Seleccionar estado...</option>
                                        <option v-for="status in statusOptions" :key="status.id" :value="status.id">{{ status.name }}</option>
                                    </select>
                                    <InputError :message="form.errors.status_id" class="mt-2" />
                                </div>
                                
                                <!-- URL -->
                                 <div>
                                    <InputLabel for="url" value="URL de la Reunión" />
                                    <TextInput id="url" v-model="form.url" type="url" class="mt-1 block w-full" placeholder="https://zoom.us/..." />
                                    <InputError :message="form.errors.url" class="mt-2" />
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- Responsibles -->
                                <div>
                                    <InputLabel for="responsibles" value="Responsables" />
                                    <select id="responsibles" v-model="form.responsibles" multiple class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm h-40">
                                        <option v-for="user in userOptions" :key="user.id" :value="user.id">
                                            {{ user.name }} ({{ user.email }})
                                        </option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Mantén presionado Ctrl/Cmd para seleccionar múltiples</p>
                                    <InputError :message="form.errors.responsibles" class="mt-2" />
                                </div>

                                <!-- Notes -->
                                <div>
                                    <InputLabel for="notes" value="Notas Previas" />
                                    <textarea id="notes" v-model="form.notes" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Notas, agenda, temas a tratar..."></textarea>
                                    <InputError :message="form.errors.notes" class="mt-2" />
                                </div>

                                <!-- Observations -->
                                <div>
                                    <InputLabel for="observations" value="Observaciones Finales" />
                                    <textarea id="observations" v-model="form.observations" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Conclusiones, asistencias, evaluación general..."></textarea>
                                    <InputError :message="form.errors.observations" class="mt-2" />
                                </div>

                                <!-- Goal -->
                                <div class="flex items-center">
                                    <input id="goal" type="checkbox" v-model="form.goal" class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                    <label for="goal" class="ml-2 text-sm text-gray-700">Meta cumplida</label>
                                    <InputError :message="form.errors.goal" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-8 flex justify-end space-x-3">
                            <Link :href="route('meetings.index')" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                                Cancelar
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="bg-yellow-600 hover:bg-yellow-700">
                                <i class="fas fa-save mr-2"></i> {{ meeting ? 'Actualizar Reunión' : 'Guardar Reunión' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
