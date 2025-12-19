<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue';
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue';

const props = defineProps({
    post: Object,
    statusOptions: Array,
    projects: Array,
    users: Array,
});

const isEdit = computed(() => !!props.post);

const form = useForm({
    _method: isEdit.value ? 'PUT' : 'POST',
    piece_name: props.post?.piece_name || '',
    mediums: props.post?.mediums || '',
    content_type: props.post?.content_type || '',
    scheduled_date: props.post?.scheduled_date ? props.post.scheduled_date.split('T')[0] : '',
    project_id: props.post?.project_id || '',
    responsible_id: props.post?.responsible_id || '',
    status_id: props.post?.status_id || '',
    comments: props.post?.comments || '',
    comments: props.post?.comments || '',
    files: [],
    pending_file_ids: [],
});

const submit = () => {
    if (isEdit.value) {
        form.post(route('marketing.socialmedia.update', props.post.id), {
            forceFormData: true,
        });
    } else {
        form.post(route('marketing.socialmedia.store'), {
            forceFormData: true,
        });
    }
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
};
</script>

<template>
    <AppLayout :title="isEdit ? 'Editar Publicación' : 'Nueva Publicación'">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ isEdit ? 'Editar Publicación' : 'Nueva Publicación' }}
                    </h1>
                    <p class="text-gray-600 mt-1">
                        {{ isEdit ? 'Actualiza la información de la publicación' : 'Complete los datos para registrar una nueva publicación' }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('marketing.socialmedia.index')"
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
                    <!-- Nombre de Pieza -->
                    <div class="lg:col-span-2">
                        <label for="piece_name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de Pieza / Post *</label>
                        <input type="text" id="piece_name" v-model="form.piece_name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Ej: Post Navidad LinkedIn">
                        <div v-if="form.errors.piece_name" class="text-red-500 text-sm mt-1">{{ form.errors.piece_name }}</div>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                        <select id="status_id" v-model="form.status_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar estado</option>
                            <option v-for="status in statusOptions" :key="status.id" :value="status.id">{{ status.name }}</option>
                        </select>
                        <div v-if="form.errors.status_id" class="text-red-500 text-sm mt-1">{{ form.errors.status_id }}</div>
                    </div>
                </div>

                <!-- Detalles Contenido -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Medios -->
                    <div>
                        <label for="mediums" class="block text-sm font-medium text-gray-700 mb-2">Medios / Canales</label>
                        <input type="text" id="mediums" v-model="form.mediums"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Ej: Instagram, LinkedIn, Facebook">
                        <div v-if="form.errors.mediums" class="text-red-500 text-sm mt-1">{{ form.errors.mediums }}</div>
                    </div>

                    <!-- Tipo Contenido -->
                    <div>
                        <label for="content_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Contenido</label>
                        <input type="text" id="content_type" v-model="form.content_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Ej: Reel, Carrusel, Story">
                        <div v-if="form.errors.content_type" class="text-red-500 text-sm mt-1">{{ form.errors.content_type }}</div>
                    </div>
                </div>

                <!-- Fecha y Asignaciones -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <!-- Fecha Programada -->
                    <div>
                        <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha Programada</label>
                        <input type="date" id="scheduled_date" v-model="form.scheduled_date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <div v-if="form.errors.scheduled_date" class="text-red-500 text-sm mt-1">{{ form.errors.scheduled_date }}</div>
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

                <!-- Comentarios -->
                <div class="grid grid-cols-1 gap-6 mt-6">
                    <div>
                        <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">Comentarios / Copy</label>
                        <textarea id="comments" v-model="form.comments" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Texto del post, notas de diseño, hashtags..."></textarea>
                        <div v-if="form.errors.comments" class="text-red-500 text-sm mt-1">{{ form.errors.comments }}</div>
                    </div>
                </div>

                 <!-- Files -->
                     <div class="border-t pt-4 mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Archivos Adjuntos</label>
                        
                         <!-- Edit Mode -->
                         <div v-if="isEdit">
                            <ModelAttachments 
                                :model-id="post.id" 
                                model-type="App\Models\SocialMediaPost" 
                                area-slug="marketing" 
                                :files="post.files" 
                            />
                         </div>
    
                         <!-- Create Mode -->
                         <div v-else>
                            <ModelAttachmentsCreator 
                                model-type="App\Models\SocialMediaPost"
                                area-slug="marketing"
                                v-model:files="form.files"
                                v-model:pendingFileIds="form.pending_file_ids"
                            />
                            <p v-if="form.errors.files" class="text-red-500 text-sm mt-1">{{ form.errors.files }}</p>
                         </div>
                     </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                    <Link :href="route('marketing.socialmedia.index')"
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </Link>
                    <button type="submit"
                        :disabled="form.processing"
                        class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50">
                        <i class="fas fa-save mr-2"></i>{{ isEdit ? 'Actualizar' : 'Guardar' }} Publicación
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
