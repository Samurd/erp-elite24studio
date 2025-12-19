<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue';
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue';

const props = defineProps({
    event: Object,
    eventTypeOptions: Array,
    statusOptions: Array,
    responsibleOptions: Array,
});

const isEdit = computed(() => !!props.event);

const form = useForm({
    _method: isEdit.value ? 'PUT' : 'POST',
    name: props.event?.name || '',
    type_id: props.event?.type_id || '',
    event_date: props.event?.event_date ? props.event.event_date.split('T')[0] : new Date().toISOString().split('T')[0],
    location: props.event?.location || '',
    status_id: props.event?.status_id || '',
    responsible_id: props.event?.responsible_id || '',
    observations: props.event?.observations || '',
    files: [],
    pending_file_ids: [],
});

const submit = () => {
    if (isEdit.value) {
        form.post(route('marketing.events.update', props.event.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('marketing.events.store'), {
            forceFormData: true,
        });
    }
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
};
</script>

<template>
    <AppLayout :title="isEdit ? 'Editar Evento' : 'Nuevo Evento'">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ isEdit ? 'Editar Evento' : 'Nuevo Evento' }}
                    </h1>
                    <p class="text-gray-600 mt-1">
                        {{ isEdit ? 'Actualiza la información del evento' : 'Complete los datos para registrar un nuevo evento' }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('marketing.events.index')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Nombre -->
                    <div class="lg:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Evento *</label>
                        <input type="text" id="name" v-model="form.name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Ej: Lanzamiento de producto">
                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>

                     <!-- Fecha -->
                    <div>
                        <label for="event_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha del Evento *</label>
                        <input type="date" id="event_date" v-model="form.event_date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <div v-if="form.errors.event_date" class="text-red-500 text-sm mt-1">{{ form.errors.event_date }}</div>
                    </div>

                     <!-- Lugar -->
                    <div class="lg:col-span-1">
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Lugar / Ubicación *</label>
                         <input type="text" id="location" v-model="form.location"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Ej: Salon de conferencias, Hotel...">
                        <div v-if="form.errors.location" class="text-red-500 text-sm mt-1">{{ form.errors.location }}</div>
                    </div>
                </div>

                <!-- Clasificación -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                     <!-- Tipo -->
                     <div>
                        <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Evento</label>
                        <select id="type_id" v-model="form.type_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar tipo</option>
                            <option v-for="type in eventTypeOptions" :key="type.id" :value="type.id">{{ type.name }}</option>
                        </select>
                        <div v-if="form.errors.type_id" class="text-red-500 text-sm mt-1">{{ form.errors.type_id }}</div>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select id="status_id" v-model="form.status_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar estado</option>
                            <option v-for="status in statusOptions" :key="status.id" :value="status.id">{{ status.name }}</option>
                        </select>
                        <div v-if="form.errors.status_id" class="text-red-500 text-sm mt-1">{{ form.errors.status_id }}</div>
                    </div>

                    <!-- Responsable -->
                    <div>
                        <label for="responsible_id" class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                        <select id="responsible_id" v-model="form.responsible_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar responsable</option>
                            <option v-for="user in responsibleOptions" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                        <div v-if="form.errors.responsible_id" class="text-red-500 text-sm mt-1">{{ form.errors.responsible_id }}</div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="grid grid-cols-1 gap-6 mt-6">
                    <div>
                        <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                        <textarea id="observations" v-model="form.observations" rows="5"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Detalles adicionales del evento..."></textarea>
                        <div v-if="form.errors.observations" class="text-red-500 text-sm mt-1">{{ form.errors.observations }}</div>
                    </div>
                </div>

                 <div class="border-t pt-4 mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Archivos Adjuntos</label>
                    
                     <!-- Edit Mode -->
                     <div v-if="isEdit">
                        <ModelAttachments 
                            :model-id="event.id" 
                            model-type="App\Models\Event" 
                            area-slug="marketing" 
                            :files="event.files" 
                        />
                     </div>

                     <!-- Create Mode -->
                     <div v-else>
                        <ModelAttachmentsCreator 
                            model-type="App\Models\Event"
                            area-slug="marketing"
                            v-model:files="form.files"
                            v-model:pendingFileIds="form.pending_file_ids"
                        />
                        <p v-if="form.errors.files" class="text-red-500 text-sm mt-1">{{ form.errors.files }}</p>
                     </div>
                 </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                    <Link :href="route('marketing.events.index')"
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </Link>
                    <button type="submit"
                        :disabled="form.processing"
                        class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50">
                        <i class="fas fa-save mr-2"></i>{{ isEdit ? 'Actualizar' : 'Guardar' }} Evento
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
