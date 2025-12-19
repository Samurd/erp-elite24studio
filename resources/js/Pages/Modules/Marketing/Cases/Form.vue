<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue';
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue';

const props = defineProps({
    caseMarketing: Object,
    typeOptions: Array,
    statusOptions: Array,
    projects: Array,
    users: Array,
});

const isEdit = computed(() => !!props.caseMarketing);

const form = useForm({
    _method: isEdit.value ? 'PUT' : 'POST',
    subject: props.caseMarketing?.subject || '',
    type_id: props.caseMarketing?.type_id || '',
    status_id: props.caseMarketing?.status_id || '',
    date: props.caseMarketing?.date ? props.caseMarketing.date.split('T')[0] : new Date().toISOString().split('T')[0],
    project_id: props.caseMarketing?.project_id || '',
    responsible_id: props.caseMarketing?.responsible_id || '',
    mediums: props.caseMarketing?.mediums || '',
    description: props.caseMarketing?.description || '',
    description: props.caseMarketing?.description || '',
    files: [],
    pending_file_ids: [],
});

const submit = () => {
    if (isEdit.value) {
        form.post(route('marketing.cases.update', props.caseMarketing.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('marketing.cases.store'), {
            forceFormData: true,
        });
    }
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
};
</script>

<template>
    <AppLayout :title="isEdit ? 'Editar Caso' : 'Nuevo Caso'">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ isEdit ? 'Editar Caso' : 'Nuevo Caso' }}
                    </h1>
                    <p class="text-gray-600 mt-1">
                        {{ isEdit ? 'Actualiza la información del caso' : 'Complete los datos para registrar un nuevo caso' }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('marketing.cases.index')"
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
                    <!-- Asunto -->
                    <div class="lg:col-span-2">
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Asunto *</label>
                        <input type="text" id="subject" v-model="form.subject"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Resumen del requerimiento o caso">
                        <div v-if="form.errors.subject" class="text-red-500 text-sm mt-1">{{ form.errors.subject }}</div>
                    </div>

                    <!-- Fecha -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                         <input type="date" id="date" v-model="form.date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <div v-if="form.errors.date" class="text-red-500 text-sm mt-1">{{ form.errors.date }}</div>
                    </div>
                </div>

                <!-- Clasificación -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                     <!-- Tipo -->
                     <div>
                        <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Caso</label>
                        <select id="type_id" v-model="form.type_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar tipo</option>
                            <option v-for="type in typeOptions" :key="type.id" :value="type.id">{{ type.name }}</option>
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
                </div>

                <!-- Detalles -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                     <!-- Medios -->
                     <div>
                        <label for="mediums" class="block text-sm font-medium text-gray-700 mb-2">Medios / Canales *</label>
                        <input type="text" id="mediums" v-model="form.mediums"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Ej: Web, Email, Reunión">
                        <div v-if="form.errors.mediums" class="text-red-500 text-sm mt-1">{{ form.errors.mediums }}</div>
                    </div>

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

                <!-- Descripción -->
                <div class="grid grid-cols-1 gap-6 mt-6">
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea id="description" v-model="form.description" rows="5"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Detalles del caso..."></textarea>
                        <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                    </div>
                </div>

                 <div class="border-t pt-4 mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Archivos Adjuntos</label>
                    
                     <!-- Edit Mode -->
                     <div v-if="isEdit">
                        <ModelAttachments 
                            :model-id="caseMarketing.id" 
                            model-type="App\Models\CaseMarketing" 
                            area-slug="marketing" 
                            :files="caseMarketing.files" 
                        />
                     </div>

                     <!-- Create Mode -->
                     <div v-else>
                        <ModelAttachmentsCreator 
                            model-type="App\Models\CaseMarketing"
                            area-slug="marketing"
                            v-model:files="form.files"
                            v-model:pendingFileIds="form.pending_file_ids"
                        />
                        <p v-if="form.errors.files" class="text-red-500 text-sm mt-1">{{ form.errors.files }}</p>
                     </div>
                 </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                    <Link :href="route('marketing.cases.index')"
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </Link>
                    <button type="submit"
                        :disabled="form.processing"
                        class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50">
                        <i class="fas fa-save mr-2"></i>{{ isEdit ? 'Actualizar' : 'Guardar' }} Caso
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
