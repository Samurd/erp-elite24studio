<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue';
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue';

const props = defineProps({
    adpiece: Object,
    typeOptions: Array,
    formatOptions: Array,
    statusOptions: Array,
    projects: Array,
    teams: Array,
    strategies: Array,
});

const isEdit = computed(() => !!props.adpiece);

const form = useForm({
    _method: isEdit.value ? 'PUT' : 'POST',
    name: props.adpiece?.name || '',
    type_id: props.adpiece?.type_id || '',
    format_id: props.adpiece?.format_id || '',
    status_id: props.adpiece?.status_id || '',
    project_id: props.adpiece?.project_id || '',
    team_id: props.adpiece?.team_id || '',
    strategy_id: props.adpiece?.strategy_id || '',
    media: props.adpiece?.media || '',
    instructions: props.adpiece?.instructions || '',
    instructions: props.adpiece?.instructions || '',
    files: [],
    pending_file_ids: [],
});

const submit = () => {
    if (isEdit.value) {
        form.post(route('marketing.ad-pieces.update', props.adpiece.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('marketing.ad-pieces.store'), {
            forceFormData: true,
        });
    }
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
};
</script>

<template>
    <AppLayout :title="isEdit ? 'Editar Pieza' : 'Nueva Pieza'">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ isEdit ? 'Editar Pieza' : 'Nueva Pieza' }}
                    </h1>
                    <p class="text-gray-600 mt-1">
                        {{ isEdit ? 'Actualiza la información de la pieza publicitaria' : 'Complete los datos para registrar una nueva pieza publicitaria' }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('marketing.ad-pieces.index')"
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
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                        <input type="text" id="name" v-model="form.name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Nombre de la pieza">
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

                <!-- Clasificación -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                     <!-- Tipo -->
                     <div>
                        <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select id="type_id" v-model="form.type_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar tipo</option>
                            <option v-for="type in typeOptions" :key="type.id" :value="type.id">{{ type.name }}</option>
                        </select>
                        <div v-if="form.errors.type_id" class="text-red-500 text-sm mt-1">{{ form.errors.type_id }}</div>
                    </div>
                    
                    <!-- Formato -->
                     <div>
                        <label for="format_id" class="block text-sm font-medium text-gray-700 mb-2">Formato</label>
                        <select id="format_id" v-model="form.format_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar formato</option>
                            <option v-for="format in formatOptions" :key="format.id" :value="format.id">{{ format.name }}</option>
                        </select>
                        <div v-if="form.errors.format_id" class="text-red-500 text-sm mt-1">{{ form.errors.format_id }}</div>
                    </div>

                    <!-- Medio -->
                    <div>
                        <label for="media" class="block text-sm font-medium text-gray-700 mb-2">Medio</label>
                        <input type="text" id="media" v-model="form.media"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Ej: Facebook">
                        <div v-if="form.errors.media" class="text-red-500 text-sm mt-1">{{ form.errors.media }}</div>
                    </div>

                     <!-- Equipo -->
                     <div>
                        <label for="team_id" class="block text-sm font-medium text-gray-700 mb-2">Equipo</label>
                        <select id="team_id" v-model="form.team_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar equipo</option>
                            <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                        </select>
                        <div v-if="form.errors.team_id" class="text-red-500 text-sm mt-1">{{ form.errors.team_id }}</div>
                    </div>
                </div>

                <!-- Relaciones -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Proyecto -->
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Proyecto</label>
                        <select id="project_id" v-model="form.project_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Sin Proyecto</option>
                            <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option>
                        </select>
                        <div v-if="form.errors.project_id" class="text-red-500 text-sm mt-1">{{ form.errors.project_id }}</div>
                    </div>
                    
                    <!-- Estrategia -->
                    <div>
                        <label for="strategy_id" class="block text-sm font-medium text-gray-700 mb-2">Estrategia</label>
                        <select id="strategy_id" v-model="form.strategy_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Sin Estrategia</option>
                            <option v-for="strategy in strategies" :key="strategy.id" :value="strategy.id">{{ strategy.name }}</option>
                        </select>
                        <div v-if="form.errors.strategy_id" class="text-red-500 text-sm mt-1">{{ form.errors.strategy_id }}</div>
                    </div>
                </div>

                <!-- Instrucciones -->
                <div class="grid grid-cols-1 gap-6 mt-6">
                    <div>
                        <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">Instrucciones</label>
                        <textarea id="instructions" v-model="form.instructions" rows="5"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Instrucciones para la pieza..."></textarea>
                        <div v-if="form.errors.instructions" class="text-red-500 text-sm mt-1">{{ form.errors.instructions }}</div>
                    </div>
                </div>

                 <div class="border-t pt-4 mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Archivos Adjuntos</label>
                    
                     <!-- Edit Mode -->
                     <div v-if="isEdit">
                        <ModelAttachments 
                            :model-id="adpiece.id" 
                            model-type="App\Models\Adpiece" 
                            area-slug="marketing" 
                            :files="adpiece.files" 
                        />
                     </div>

                     <!-- Create Mode -->
                     <div v-else>
                        <ModelAttachmentsCreator 
                            model-type="App\Models\Adpiece"
                            area-slug="marketing"
                            v-model:files="form.files"
                            v-model:pendingFileIds="form.pending_file_ids"
                        />
                        <p v-if="form.errors.files" class="text-red-500 text-sm mt-1">{{ form.errors.files }}</p>
                     </div>
                 </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                    <Link :href="route('marketing.ad-pieces.index')"
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </Link>
                    <button type="submit"
                        :disabled="form.processing"
                        class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50">
                        <i class="fas fa-save mr-2"></i>{{ isEdit ? 'Actualizar' : 'Guardar' }} Pieza
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
