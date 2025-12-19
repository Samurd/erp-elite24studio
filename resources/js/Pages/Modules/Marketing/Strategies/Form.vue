<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue';
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue';

const props = defineProps({
    strategy: Object,
    statusOptions: Array,
    users: Array,
});

const isEdit = computed(() => !!props.strategy);

const form = useForm({
    _method: isEdit.value ? 'PUT' : 'POST',
    name: props.strategy?.name || '',
    objective: props.strategy?.objective || '',
    status_id: props.strategy?.status_id || '',
    start_date: props.strategy?.start_date ? props.strategy.start_date.split('T')[0] : '', // Adjust date format if needed (e.g., from full ISO string)
    end_date: props.strategy?.end_date ? props.strategy.end_date.split('T')[0] : '',
    target_audience: props.strategy?.target_audience || '',
    platforms: props.strategy?.platforms || '',
    responsible_id: props.strategy?.responsible_id || '',
    notify_team: !!props.strategy?.notify_team,
    add_to_calendar: !!props.strategy?.add_to_calendar,
    observations: props.strategy?.observations || '',
    observations: props.strategy?.observations || '',
    files: [],
    pending_file_ids: [],
});

const submit = () => {
    if (isEdit.value) {
        form.post(route('marketing.strategies.update', props.strategy.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('marketing.strategies.store'), {
            forceFormData: true,
        });
    }
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
};
</script>

<template>
    <AppLayout :title="isEdit ? 'Editar Estrategia' : 'Nueva Estrategia'">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ isEdit ? 'Editar Estrategia' : 'Nueva Estrategia' }}
                    </h1>
                    <p class="text-gray-600 mt-1">
                        {{ isEdit ? 'Actualiza la información de la estrategia' : 'Complete los datos para registrar una nueva estrategia de marketing' }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('marketing.strategies.index')"
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
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Estrategia *</label>
                        <input type="text" id="name" v-model="form.name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Ej: Campaña de Verano 2024">
                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
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
                </div>

                <!-- Objetivo -->
                <div class="grid grid-cols-1 gap-6 mt-6">
                    <div>
                        <label for="objective" class="block text-sm font-medium text-gray-700 mb-2">Objetivo Principal</label>
                        <textarea id="objective" v-model="form.objective" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Describir el objetivo principal de la estrategia..."></textarea>
                        <div v-if="form.errors.objective" class="text-red-500 text-sm mt-1">{{ form.errors.objective }}</div>
                    </div>
                </div>

                <!-- Fechas y Responsable -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <!-- Fecha de Inicio -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Inicio</label>
                        <input type="date" id="start_date" v-model="form.start_date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <div v-if="form.errors.start_date" class="text-red-500 text-sm mt-1">{{ form.errors.start_date }}</div>
                    </div>

                    <!-- Fecha de Fin -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Fin</label>
                        <input type="date" id="end_date" v-model="form.end_date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <div v-if="form.errors.end_date" class="text-red-500 text-sm mt-1">{{ form.errors.end_date }}</div>
                    </div>

                    <!-- Responsable -->
                    <div>
                        <label for="responsible_id" class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                        <select id="responsible_id" v-model="form.responsible_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar responsable</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                        <div v-if="form.errors.responsible_id" class="text-red-500 text-sm mt-1">{{ form.errors.responsible_id }}</div>
                    </div>
                </div>

                <!-- Público Objetivo y Plataformas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Público Objetivo -->
                    <div>
                        <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-2">Público Objetivo</label>
                        <input type="text" id="target_audience" v-model="form.target_audience"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Ej: Jóvenes 18-25 años, interés en tecnología">
                        <div v-if="form.errors.target_audience" class="text-red-500 text-sm mt-1">{{ form.errors.target_audience }}</div>
                    </div>

                    <!-- Plataformas -->
                    <div>
                        <label for="platforms" class="block text-sm font-medium text-gray-700 mb-2">Plataformas</label>
                        <input type="text" id="platforms" v-model="form.platforms"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Ej: Facebook, Instagram, LinkedIn, TikTok">
                        <div v-if="form.errors.platforms" class="text-red-500 text-sm mt-1">{{ form.errors.platforms }}</div>
                    </div>
                </div>

                <!-- Opciones Adicionales -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Opciones Adicionales</label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" v-model="form.notify_team"
                                    class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Notificar al equipo sobre esta estrategia</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="form.add_to_calendar"
                                    class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Agregar al calendario del equipo</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="grid grid-cols-1 gap-6 mt-6">
                    <div>
                        <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                        <textarea id="observations" v-model="form.observations" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Notas adicionales, comentarios importantes, etc..."></textarea>
                        <div v-if="form.errors.observations" class="text-red-500 text-sm mt-1">{{ form.errors.observations }}</div>
                    </div>
                </div>

                 <!-- Files -->
                 <div class="border-t pt-4 mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Archivos Adjuntos</label>
                    
                     <!-- Edit Mode -->
                     <div v-if="isEdit">
                        <ModelAttachments 
                            :model-id="strategy.id" 
                            model-type="App\Models\Strategy" 
                            area-slug="marketing" 
                            :files="strategy.files" 
                        />
                     </div>

                     <!-- Create Mode -->
                     <div v-else>
                        <ModelAttachmentsCreator 
                            model-type="App\Models\Strategy"
                            area-slug="marketing"
                            v-model:files="form.files"
                            v-model:pendingFileIds="form.pending_file_ids"
                        />
                        <p v-if="form.errors.files" class="text-red-500 text-sm mt-1">{{ form.errors.files }}</p>
                     </div>
                 </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                    <Link :href="route('marketing.strategies.index')"
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </Link>
                    <button type="submit"
                        :disabled="form.processing"
                        class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50">
                        <i class="fas fa-save mr-2"></i>{{ isEdit ? 'Actualizar' : 'Guardar' }} Estrategia
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
