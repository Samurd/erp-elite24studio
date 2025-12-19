<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    adpieces: Object,
    typeOptions: Array,
    formatOptions: Array,
    statusOptions: Array,
    projects: Array,
    teams: Array,
    strategies: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const type_filter = ref(props.filters.type_filter || '');
const format_filter = ref(props.filters.format_filter || '');
const status_filter = ref(props.filters.status_filter || '');
const project_filter = ref(props.filters.project_filter || '');
const team_filter = ref(props.filters.team_filter || '');
const strategy_filter = ref(props.filters.strategy_filter || '');
const media_filter = ref(props.filters.media_filter || '');
const perPage = ref(props.filters.perPage || 10);

const updateFilters = debounce(() => {
    router.get(route('marketing.ad-pieces.index'), {
        search: search.value,
        type_filter: type_filter.value,
        format_filter: format_filter.value,
        status_filter: status_filter.value,
        project_filter: project_filter.value,
        team_filter: team_filter.value,
        strategy_filter: strategy_filter.value,
        media_filter: media_filter.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300);

watch([search, type_filter, format_filter, status_filter, project_filter, team_filter, strategy_filter, media_filter, perPage], () => {
    updateFilters();
});

const clearFilters = () => {
    search.value = '';
    type_filter.value = '';
    format_filter.value = '';
    status_filter.value = '';
    project_filter.value = '';
    team_filter.value = '';
    strategy_filter.value = '';
    media_filter.value = '';
    perPage.value = 10;
};

const deleteAdPiece = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta pieza publicitaria?')) {
        router.delete(route('marketing.ad-pieces.destroy', id), {
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
    <AppLayout title="Piezas Publicitarias">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Piezas Publicitarias</h1>
                    <p class="text-gray-600 mt-1">Gestión de piezas gráficas y contenido publicitario</p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('marketing.ad-pieces.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nueva Pieza
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
                        placeholder="Nombre o medio..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Filtro por tipo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                    <select v-model="type_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option v-for="type in typeOptions" :key="type.id" :value="type.id">{{ type.name }}</option>
                    </select>
                </div>

                <!-- Filtro por formato -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Formato</label>
                    <select v-model="format_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option v-for="format in formatOptions" :key="format.id" :value="format.id">{{ format.name }}</option>
                    </select>
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

                 <!-- Filtro por medio -->
                 <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Medio</label>
                    <input type="text"
                        v-model="media_filter"
                        placeholder="Ej: Facebook, Google..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
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

                <!-- Filtro por equipo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Equipo</label>
                    <select v-model="team_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                    </select>
                </div>

                 <!-- Filtro por estrategia -->
                 <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estrategia</label>
                    <select v-model="strategy_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todas</option>
                        <option v-for="strategy in strategies" :key="strategy.id" :value="strategy.id">{{ strategy.name }}</option>
                    </select>
                </div>

                <!-- Registros por página -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Registros</label>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formato</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estrategia</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="adpiece in adpieces.data" :key="adpiece.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ adpiece.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ adpiece.name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ adpiece.media || '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ adpiece.type ? adpiece.type.name : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ adpiece.format ? adpiece.format.name : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="adpiece.status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ adpiece.status.name }}
                                </span>
                                <span v-else class="text-sm text-gray-500">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ adpiece.project ? adpiece.project.name : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ adpiece.responsible ? adpiece.responsible.name : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ adpiece.strategy ? adpiece.strategy.name : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <Link :href="route('marketing.ad-pieces.edit', adpiece.id)"
                                        class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </Link>
                                    <button @click="deleteAdPiece(adpiece.id)"
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="adpieces.data.length === 0">
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron piezas publicitarias
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="adpieces.links && adpieces.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                 <div class="flex items-center justify-between">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                         <div>
                            <p class="text-sm text-gray-700">
                                Mostrando
                                <span class="font-medium">{{ adpieces.from }}</span>
                                a
                                <span class="font-medium">{{ adpieces.to }}</span>
                                de
                                <span class="font-medium">{{ adpieces.total }}</span>
                                resultados
                            </p>
                        </div>
                        <div>
                             <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <template v-for="(link, key) in adpieces.links" :key="key">
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
