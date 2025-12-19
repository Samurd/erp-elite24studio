<script setup>
import { computed, watch } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    template: Object,
    users: Array,
});

const frequencies = {
    daily: { label: 'Diaria', interval: 'daily', value: 1 },
    weekly: { label: 'Semanal', interval: 'weekly', value: 1 },
    biweekly: { label: 'Quincenal (15 días)', interval: 'days', value: 15 },
    monthly: { label: 'Mensual', interval: 'monthly', value: 1 },
    bimonthly: { label: 'Bimestral', interval: 'months', value: 2 },
    quarterly: { label: 'Trimestral', interval: 'months', value: 3 },
    biannual: { label: 'Semestral', interval: 'months', value: 6 },
    yearly: { label: 'Anual', interval: 'yearly', value: 1 },
};

const form = useForm({
    title: props.template?.title || '',
    message: props.template?.message || '',
    type: props.template?.type || 'reminder',
    user_id: props.template?.user_id || (props.users.length > 0 ? props.users[0].id : null),
    is_active: props.template ? props.template.is_active : true, // Check if this comes as integer or boolean
    scheduled_at: props.template?.scheduled_at ? props.template.scheduled_at.slice(0, 16) : '',
    reminder_days: props.template?.reminder_days || 3,
    event_date: props.template?.event_date ? props.template.event_date.slice(0, 16) : '',
    selected_frequency: 'monthly', // Default
    recurring_day: props.template?.recurring_pattern?.day || null,
});

// Logic to determine initial frequency if editing
if (props.template && props.template.type === 'recurring' && props.template.recurring_pattern) {
    const pattern = props.template.recurring_pattern;
    const interval = pattern.interval || 'monthly';
    const value = pattern.value || 1;
    
    // Find matching frequency key
    const match = Object.keys(frequencies).find(key => 
        frequencies[key].interval === interval && frequencies[key].value == value
    );
    
    if (match) {
        form.selected_frequency = match;
    } else {
        // Fallback or custom handling if needed
        form.selected_frequency = 'monthly';
    }
} else if (!props.template) {
    // defaults
    form.user_id = props.users.length > 0 ? props.users.find(u => u.id ===  props.users[0].id )?.id : null; 
    // Wait, the line above is weird. Let's just default to first user if new.
    // If we have auth user id available in props or page props we could use that, but for now just pick first or null.
     if (props.users.length > 0 && !form.user_id) {
        form.user_id = props.users[0].id;
    }
}

const submit = () => {
    if (props.template) {
        form.put(route('notifications.update', props.template.id));
    } else {
        form.post(route('notifications.store'));
    }
};

</script>

<template>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form @submit.prevent="submit">

            <!-- Configuración de la Notificación -->
            <div class="mb-8 border-b pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración de la Notificación</h3>

                <!-- Tipo -->
                <div class="mb-6">
                    <InputLabel value="Tipo de Notificación" class="mb-2" />
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Reminder -->
                        <label class="cursor-pointer">
                            <input type="radio" v-model="form.type" value="reminder" class="peer sr-only">
                            <div
                                class="p-4 border rounded-lg hover:bg-gray-50 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-all"
                                :class="{'border-yellow-500 bg-yellow-50': form.type === 'reminder'}">
                                <div class="font-medium text-gray-900">Recordatorio</div>
                                <div class="text-xs text-gray-500">Días antes de una fecha</div>
                            </div>
                        </label>
                        <!-- Scheduled -->
                        <label class="cursor-pointer">
                            <input type="radio" v-model="form.type" value="scheduled" class="peer sr-only">
                            <div
                                class="p-4 border rounded-lg hover:bg-gray-50 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-all"
                                :class="{'border-yellow-500 bg-yellow-50': form.type === 'scheduled'}">
                                <div class="font-medium text-gray-900">Programada</div>
                                <div class="text-xs text-gray-500">Fecha y hora específica</div>
                            </div>
                        </label>
                        <!-- Recurring -->
                        <label class="cursor-pointer">
                            <input type="radio" v-model="form.type" value="recurring" class="peer sr-only">
                            <div
                                class="p-4 border rounded-lg hover:bg-gray-50 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-all"
                                :class="{'border-yellow-500 bg-yellow-50': form.type === 'recurring'}">
                                <div class="font-medium text-gray-900">Recurrente</div>
                                <div class="text-xs text-gray-500">Se repite periódicamente</div>
                            </div>
                        </label>
                    </div>
                    <InputError :message="form.errors.type" class="mt-2" />
                </div>

                <!-- Campos dinámicos según tipo -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <template v-if="form.type === 'reminder'">
                        <div>
                            <InputLabel for="event_date" value="Fecha del Evento" />
                            <input type="datetime-local" id="event_date" v-model="form.event_date"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500 mt-1 block">
                            <p class="text-xs text-gray-500 mt-1">Fecha base para el recordatorio.</p>
                            <InputError :message="form.errors.event_date" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="reminder_days" value="Días de anticipación" />
                            <input type="number" id="reminder_days" v-model="form.reminder_days" min="1"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500 mt-1 block">
                            <p class="text-xs text-gray-500 mt-1">Días antes de la fecha del evento.</p>
                            <InputError :message="form.errors.reminder_days" class="mt-2" />
                        </div>
                    </template>

                    <template v-if="form.type === 'scheduled'">
                        <div>
                            <InputLabel for="scheduled_at" value="Fecha y Hora" />
                            <input type="datetime-local" id="scheduled_at" v-model="form.scheduled_at"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500 mt-1 block">
                            <InputError :message="form.errors.scheduled_at" class="mt-2" />
                        </div>
                    </template>

                    <template v-if="form.type === 'recurring'">
                        <div>
                            <InputLabel for="selected_frequency" value="Frecuencia" />
                            <select id="selected_frequency" v-model="form.selected_frequency"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500 mt-1 block">
                                <option v-for="(config, key) in frequencies" :key="key" :value="key">
                                    {{ config.label }}
                                </option>
                            </select>
                            <InputError :message="form.errors.selected_frequency" class="mt-2" />
                        </div>
                    </template>
                </div>

                <!-- Campos Comunes -->
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <InputLabel for="user_id" value="Destinatario" />
                        <select id="user_id" v-model="form.user_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500 mt-1 block">
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                {{ user.name }} ({{ user.email }})
                            </option>
                        </select>
                        <InputError :message="form.errors.user_id" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="title" value="Título" />
                        <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required />
                        <InputError :message="form.errors.title" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="message" value="Mensaje" />
                        <textarea id="message" v-model="form.message" rows="3"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500 mt-1 block"></textarea>
                        <InputError :message="form.errors.message" class="mt-2" />
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Las notificaciones se enviarán automáticamente por correo electrónico al
                                    destinatario seleccionado.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-if="template" class="flex items-center mt-4">
                        <input type="checkbox" id="is_active" v-model="form.is_active"
                            class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Notificación Activa</label>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-3">
                <Link :href="route('notifications.index')"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </Link>
                <button type="submit" :disabled="form.processing"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50">
                    {{ template ? 'Actualizar' : 'Crear' }} Notificación
                </button>
            </div>
        </form>
    </div>
</template>
