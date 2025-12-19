<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue'; // Assuming standard Pagination component exists, otherwise standard links
import debounce from 'lodash/debounce';

const props = defineProps({
    strategies: Object,
    statusOptions: Array,
    responsibleOptions: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status_filter = ref(props.filters.status_filter || '');
const responsible_filter = ref(props.filters.responsible_filter || '');
const platform_filter = ref(props.filters.platform_filter || '');
const target_audience_filter = ref(props.filters.target_audience_filter || '');
const date_from = ref(props.filters.date_from || '');
const date_to = ref(props.filters.date_to || '');
const perPage = ref(props.filters.perPage || 10);

const updateFilters = debounce(() => {
    router.get(route('marketing.strategies.index'), {
        search: search.value,
        status_filter: status_filter.value,
        responsible_filter: responsible_filter.value,
        platform_filter: platform_filter.value,
        target_audience_filter: target_audience_filter.value,
        date_from: date_from.value,
        date_to: date_to.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300);

watch([search, status_filter, responsible_filter, platform_filter, target_audience_filter, date_from, date_to, perPage], () => {
    updateFilters();
});

const clearFilters = () => {
    search.value = '';
    status_filter.value = '';
    responsible_filter.value = '';
    platform_filter.value = '';
    target_audience_filter.value = '';
    date_from.value = '';
    date_to.value = '';
    perPage.value = 10;
};

const deleteStrategy = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta estrategia?')) {
        router.delete(route('marketing.strategies.destroy', id), {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    // Handle timezone if necessary, but simple split is safer for date strings
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES');
};
</script>

<template>
    <AppLayout title="Estrategias de Marketing">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Estrategias de Marketing</h1>
                    <p class="text-gray-600 mt-1">Gestión de estrategias de marketing</p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('marketing.strategies.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nueva Estrategia
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
                        placeholder="Nombre, objetivo u observaciones..."
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

                <!-- Filtro por responsable -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                    <select v-model="responsible_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option v-for="responsible in responsibleOptions" :key="responsible.id" :value="responsible.id">{{ responsible.name }}</option>
                    </select>
                </div>

                <!-- Filtro por plataforma -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plataforma</label>
                    <input type="text"
                        v-model="platform_filter"
                        placeholder="Nombre de la plataforma..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Filtro por público objetivo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Público Objetivo</label>
                    <input type="text"
                        v-model="target_audience_filter"
                        placeholder="Público objetivo..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Fecha desde -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio Desde</label>
                    <input type="date"
                        v-model="date_from"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Fecha hasta -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin Hasta</label>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nombre
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Objetivo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fechas
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Público Objetivo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Plataformas
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Responsable
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="strategy in strategies.data" :key="strategy.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    #{{ strategy.id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ strategy.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">
                                    {{ strategy.objective || '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="strategy.status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="{
                                        'bg-green-100 text-green-800': strategy.status.name == 'Activa',
                                        'bg-blue-100 text-blue-800': strategy.status.name == 'En Progreso',
                                        'bg-yellow-100 text-yellow-800': strategy.status.name == 'Pausada',
                                        'bg-purple-100 text-purple-800': strategy.status.name == 'Completada',
                                        'bg-red-100 text-red-800': strategy.status.name == 'Cancelada',
                                        'bg-gray-100 text-gray-800': !['Activa', 'En Progreso', 'Pausada', 'Completada', 'Cancelada'].includes(strategy.status.name)
                                    }">
                                    {{ strategy.status.name }}
                                </span>
                                <span v-else class="text-sm text-gray-500">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div v-if="strategy.start_date && strategy.end_date">
                                    <div class="text-sm text-gray-900">
                                        {{ formatDate(strategy.start_date) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        al {{ formatDate(strategy.end_date) }}
                                    </div>
                                </div>
                                <span v-else class="text-sm text-gray-500">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ strategy.target_audience || '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ strategy.platforms || '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div v-if="strategy.responsible" class="text-sm text-gray-900">
                                    {{ strategy.responsible.name }}
                                </div>
                                <span v-else class="text-sm text-gray-500">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <Link :href="route('marketing.strategies.edit', strategy.id)"
                                        class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </Link>
                                    <button @click="deleteStrategy(strategy.id)"
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="strategies.data.length === 0">
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron estrategias
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="strategies.links && strategies.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                 <!-- Simple pagination links loop since I don't have the Pagination component source yet, reusing standard link structure -->
                 <div class="flex items-center justify-between">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                         <div>
                            <p class="text-sm text-gray-700">
                                Mostrando
                                <span class="font-medium">{{ strategies.from }}</span>
                                a
                                <span class="font-medium">{{ strategies.to }}</span>
                                de
                                <span class="font-medium">{{ strategies.total }}</span>
                                resultados
                            </p>
                        </div>
                        <div>
                             <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <template v-for="(link, key) in strategies.links" :key="key">
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
