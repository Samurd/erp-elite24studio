<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    kpis: Object,
    roles: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const role_filter = ref(props.filters.role_filter || '');
const period_filter = ref(props.filters.period_filter || '');
const perPage = ref(props.filters.perPage || 10);

const updateSearch = debounce(() => {
    applyFilters();
}, 300);

const applyFilters = () => {
    router.get(route('kpis.index'), {
        search: search.value,
        role_filter: role_filter.value,
        period_filter: period_filter.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

watch([role_filter, period_filter, perPage], () => {
    applyFilters();
});

const clearFilters = () => {
    search.value = '';
    role_filter.value = '';
    period_filter.value = '';
    applyFilters();
};

const deleteKpi = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este KPI?')) {
        router.delete(route('kpis.destroy', id), {
            preserveScroll: true,
        });
    }
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
</script>

<template>
    <AppLayout title="KPIs">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Indicadores de Gestión (KPIs)</h1>
                    <p class="text-gray-600 mt-1">Gestión y seguimiento de indicadores de rendimiento</p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('kpis.create')"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nuevo KPI
                    </Link>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Búsqueda -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                    <input type="text" v-model="search" @input="updateSearch"
                        placeholder="Nombre o código..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Filtro por Rol -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rol Responsable</label>
                    <select v-model="role_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">
                            {{ role.name }}
                        </option>
                    </select>
                </div>

                <!-- Filtro por Periodo (Actividad reciente) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Actividad Reciente</label>
                    <select v-model="period_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todo el historial</option>
                        <option value="week">Última semana</option>
                        <option value="month">Último mes</option>
                        <option value="quarter">Último trimestre</option>
                    </select>
                </div>

                <!-- Botón limpiar -->
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Indicador</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frecuencia</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Último Registro</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="kpi in kpis.data" :key="kpi.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ kpi.protocol_code }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ kpi.indicator_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ kpi.target_value ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ kpi.periodicity_days }} días</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ kpi.role ? kpi.role.name : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div v-if="kpi.records && kpi.records.length > 0">
                                    <div class="text-sm font-bold text-gray-900">{{ kpi.records[0].value }}</div>
                                    <div class="text-xs text-gray-500">{{ formatDate(kpi.records[0].record_date) }}</div>
                                </div>
                                <span v-else class="text-xs text-gray-400 italic">Sin registros</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <Link :href="route('kpis.show', kpi.id)" class="text-green-600 hover:text-green-900" title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    
                                    <Link :href="route('kpis.records.create', kpi.id)" class="text-blue-600 hover:text-blue-900" title="Agregar Registro">
                                        <i class="fas fa-plus-circle"></i>
                                    </Link>

                                    <Link :href="route('kpis.edit', kpi.id)" class="text-indigo-600 hover:text-indigo-900" title="Editar KPI">
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    
                                    <button @click="deleteKpi(kpi.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="kpis.data.length === 0">
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron KPIs registrados
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <Pagination :links="kpis.links" class="mt-6" />
        </div>
    </AppLayout>
</template>
