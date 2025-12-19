<script setup>
import { ref, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    campaigns: Object,
    filters: Object,
    statusOptions: Array,
    responsibleOptions: Array,
});

const search = ref(props.filters.search || '');
const status_filter = ref(props.filters.status_filter || '');
const responsible_filter = ref(props.filters.responsible_filter || '');
const date_from = ref(props.filters.date_from || '');
const date_to = ref(props.filters.date_to || '');
const perPage = ref(props.filters.perPage || 10);

const updateSearch = debounce(() => {
    applyFilters();
}, 300);

const applyFilters = () => {
    router.get(route('donations.campaigns.index'), {
        search: search.value,
        status_filter: status_filter.value,
        responsible_filter: responsible_filter.value,
        date_from: date_from.value,
        date_to: date_to.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

watch([status_filter, responsible_filter, date_from, date_to, perPage], () => {
    applyFilters();
});

const clearFilters = () => {
    search.value = '';
    status_filter.value = '';
    responsible_filter.value = '';
    date_from.value = '';
    date_to.value = '';
    applyFilters();
};

const formatCurrency = (value) => {
    if (value === null || value === undefined) return '-';
    // Remove .00 if present for cleaner look if requested, but for now standard formatting
    return new Intl.NumberFormat('es-CO', { // Assuming currency locale based on context
        style: 'currency',
        currency: 'COP', // Or USD depending on project default, assuming COP based on names
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const getStatusClasses = (statusName) => {
    if (!statusName) return 'bg-gray-100 text-gray-800';
    switch (statusName) {
        case 'Activa': return 'bg-green-100 text-green-800';
        case 'En Planificación': return 'bg-yellow-100 text-yellow-800';
        case 'Finalizada': return 'bg-blue-100 text-blue-800';
        case 'Cancelada': return 'bg-red-100 text-red-800';
        case 'Pausada': return 'bg-purple-100 text-purple-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const getDateStatus = (dateString) => {
    if (!dateString) return null;
    const date = new Date(dateString);
    const now = new Date();
    // Reset hours for comparison
    date.setHours(0,0,0,0);
    now.setHours(0,0,0,0);

    const diffTime = date - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < 0) return { text: 'Pasado', class: 'text-red-600' };
    if (diffDays === 0) return { text: 'Hoy', class: 'text-green-600' };
    if (diffDays <= 7) return { text: 'Próximo', class: 'text-yellow-600' };
    return null;
};

const deleteCampaign = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta campaña?')) {
        router.delete(route('donations.campaigns.destroy', id));
    }
};
</script>

<template>
    <AppLayout title="Campañas">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Campañas</h1>
                    <p class="text-gray-600 mt-1">Gestión de campañas de donaciones</p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('donations.campaigns.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nueva Campaña
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
                    <input type="text" v-model="search" @input="updateSearch"
                        placeholder="Nombre, dirección, descripción..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Filtro por estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select v-model="status_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option v-for="status in statusOptions" :key="status.id" :value="status.id">
                            {{ status.name }}
                        </option>
                    </select>
                </div>

                <!-- Filtro por responsable -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                    <select v-model="responsible_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option v-for="responsible in responsibleOptions" :key="responsible.id" :value="responsible.id">
                            {{ responsible.name }}
                        </option>
                    </select>
                </div>

                <!-- Fecha desde -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Evento Desde</label>
                    <input type="date" v-model="date_from"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Fecha hasta -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Evento Hasta</label>
                    <input type="date" v-model="date_to"
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Evento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Presupuesto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Donaciones</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="campaign in campaigns.data" :key="campaign.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">#{{ campaign.id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ campaign.name }}</div>
                                <div v-if="campaign.description" class="text-xs text-gray-500 mt-1">
                                    {{ campaign.description.substring(0, 80) + (campaign.description.length > 80 ? '...' : '') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div v-if="campaign.date_event">
                                    <div class="text-sm text-gray-900">{{ formatDate(campaign.date_event) }}</div>
                                    <div v-if="getDateStatus(campaign.date_event)" :class="['text-xs', getDateStatus(campaign.date_event).class]">
                                        {{ getDateStatus(campaign.date_event).text }}
                                    </div>
                                </div>
                                <span v-else class="text-sm text-gray-500">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ campaign.address || '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div v-if="campaign.responsible" class="text-sm text-gray-900">{{ campaign.responsible.name }}</div>
                                <span v-else class="text-sm text-gray-500">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="campaign.status" :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClasses(campaign.status.name)]">
                                    {{ campaign.status.name }}
                                </span>
                                <span v-else class="text-sm text-gray-500">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ formatCurrency(campaign.goal) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ formatCurrency(campaign.estimated_budget) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ formatCurrency(campaign.total_donations_amount) }}</div>
                                <div class="text-xs text-gray-500">{{ campaign.donations_count }} donación(es)</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <Link :href="route('donations.campaigns.edit', campaign.id)" class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </Link>
                                    <button @click="deleteCampaign(campaign.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="campaigns.data.length === 0">
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron campañas
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <Pagination :links="campaigns.links" class="mt-6" />
        </div>
    </AppLayout>
</template>
