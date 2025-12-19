<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    posts: Object,
    statusOptions: Array,
    projects: Array,
    users: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const mediums_filter = ref(props.filters.mediums_filter || '');
const content_type_filter = ref(props.filters.content_type_filter || '');
const status_filter = ref(props.filters.status_filter || '');
const project_filter = ref(props.filters.project_filter || '');
const responsible_filter = ref(props.filters.responsible_filter || '');
const date_from = ref(props.filters.date_from || '');
const date_to = ref(props.filters.date_to || '');
const perPage = ref(props.filters.perPage || 10);

const updateFilters = debounce(() => {
    router.get(route('marketing.socialmedia.index'), {
        search: search.value,
        mediums_filter: mediums_filter.value,
        content_type_filter: content_type_filter.value,
        status_filter: status_filter.value,
        project_filter: project_filter.value,
        responsible_filter: responsible_filter.value,
        date_from: date_from.value,
        date_to: date_to.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300);

watch([search, mediums_filter, content_type_filter, status_filter, project_filter, responsible_filter, date_from, date_to, perPage], () => {
    updateFilters();
});

const clearFilters = () => {
    search.value = '';
    mediums_filter.value = '';
    content_type_filter.value = '';
    status_filter.value = '';
    project_filter.value = '';
    responsible_filter.value = '';
    date_from.value = '';
    date_to.value = '';
    perPage.value = 10;
};

const deletePost = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta publicación?')) {
        router.delete(route('marketing.socialmedia.destroy', id), {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES');
};
</script>

<template>
    <AppLayout title="Redes Sociales & Web">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Redes Sociales & Web</h1>
                    <p class="text-gray-600 mt-1">Gestión de publicaciones y contenido</p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('marketing.socialmedia.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nueva Publicación
                    </Link>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Búsqueda general -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                    <input type="text"
                        v-model="search"
                        placeholder="Nombre de pieza o comentarios..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Filtro por medio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Medio / Canal</label>
                    <input type="text"
                        v-model="mediums_filter"
                        placeholder="Ej: Instagram, LinkedIn..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Filtro por tipo contenido -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo Contenido</label>
                    <input type="text"
                        v-model="content_type_filter"
                        placeholder="Ej: Reel, Post, Story..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Filtro por estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select v-model="status_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option v-for="status in statusOptions" :key="status.id" :value="status.id">{{ status.name }}</option>
                    </select>
                </div>

                <!-- Filtro por proyecto -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Proyecto</label>
                    <select v-model="project_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option>
                    </select>
                </div>

                <!-- Filtro por responsable -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                    <select v-model="responsible_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                </div>

                <!-- Fecha desde -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Programada Desde</label>
                    <input type="date"
                        v-model="date_from"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Fecha hasta -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Programada Hasta</label>
                    <input type="date"
                        v-model="date_to"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Registros por página -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Registros por página</label>
                    <select v-model="perPage"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <!-- Botón limpiar filtros -->
                <div class="flex items-end">
                    <button @click="clearFilters"
                        class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pieza / Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medios</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Prog.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="post in posts.data" :key="post.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ post.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ post.piece_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ post.mediums || '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ post.content_type || '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="post.status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="{
                                        'bg-green-100 text-green-800': post.status.name == 'Publicado',
                                        'bg-blue-100 text-blue-800': post.status.name == 'En Progreso' || post.status.name == 'Programado',
                                        'bg-yellow-100 text-yellow-800': post.status.name == 'Borrador' || post.status.name == 'Pendiente',
                                        'bg-red-100 text-red-800': post.status.name == 'Cancelado',
                                        'bg-gray-100 text-gray-800': !['Publicado', 'En Progreso', 'Programado', 'Borrador', 'Pendiente', 'Cancelado'].includes(post.status.name)
                                    }">
                                    {{ post.status.name }}
                                </span>
                                <span v-else class="text-sm text-gray-500">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatDate(post.scheduled_date) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ post.project ? post.project.name : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ post.responsible ? post.responsible.name : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <Link :href="route('marketing.socialmedia.edit', post.id)"
                                        class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </Link>
                                    <button @click="deletePost(post.id)"
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="posts.data.length === 0">
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron publicaciones
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="posts.links && posts.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                 <div class="flex items-center justify-between">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                         <div>
                            <p class="text-sm text-gray-700">
                                Mostrando
                                <span class="font-medium">{{ posts.from }}</span>
                                a
                                <span class="font-medium">{{ posts.to }}</span>
                                de
                                <span class="font-medium">{{ posts.total }}</span>
                                resultados
                            </p>
                        </div>
                        <div>
                             <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <template v-for="(link, key) in posts.links" :key="key">
                                     <Link 
                                        v-if="link.url" 
                                        :href="link.url" 
                                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                        :class="{ 'z-10 bg-yellow-50 border-yellow-500 text-yellow-600': link.active, 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active }"
                                        v-html="link.label" 
                                    />
                                    <span v-else
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                                        v-html="link.label">
                                    </span>
                                </template>
                            </nav>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
    </AppLayout>
</template>
