<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    kpi: Object,
    records: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const date_from = ref(props.filters.date_from || '');
const date_to = ref(props.filters.date_to || '');
const perPage = ref(props.filters.perPage || 10);

const updateSearch = debounce(() => {
    applyFilters();
}, 300);

const applyFilters = () => {
    router.get(route('kpis.show', props.kpi.id), {
        search: search.value,
        date_from: date_from.value,
        date_to: date_to.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

watch([date_from, date_to, perPage], () => {
    applyFilters();
});

const clearFilters = () => {
    search.value = '';
    date_from.value = '';
    date_to.value = '';
    applyFilters();
};

const deleteRecord = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este registro?')) {
        router.delete(route('kpis.records.destroy', id), {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    // Adjust for timezone offset to prevent one-day-off error
    const date = new Date(dateString);
    const userTimezoneOffset = date.getTimezoneOffset() * 60000;
    const adjustedDate = new Date(date.getTime() + userTimezoneOffset);
    return adjustedDate.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};
</script>

<template>
    <AppLayout :title="'KPI: ' + kpi.protocol_code">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <h1 class="text-2xl font-bold text-gray-800">{{ kpi.indicator_name }}</h1>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ kpi.protocol_code }}
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 text-sm text-gray-600">
                        <p><span class="font-semibold">Objetivo (Meta):</span> {{ kpi.target_value ?? 'N/A' }}</p>
                        <p><span class="font-semibold">Periodicidad:</span> {{ kpi.periodicity_days }} días</p>
                        <p><span class="font-semibold">Rol Responsable:</span> {{ kpi.role ? kpi.role.name : '-' }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('kpis.index')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                    <Link :href="route('kpis.records.create', kpi.id)"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nuevo Registro
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
                        placeholder="Buscar en observaciones..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <!-- Fecha Desde -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                    <input type="date" v-model="date_from"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                 <!-- Fecha Hasta -->
                 <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                    <input type="date" v-model="date_to"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
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

        <!-- Records Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registrado Por</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="record in records.data" :key="record.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ formatDate(record.record_date) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold" 
                                    :class="{'text-green-600': kpi.target_value && record.value >= kpi.target_value, 'text-red-600': kpi.target_value && record.value < kpi.target_value}">
                                    {{ record.value }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600 max-w-xs truncate" :title="record.observation">
                                    {{ record.observation || '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div v-if="record.files && record.files.length > 0" class="flex flex-col space-y-1">
                                    <a v-for="file in record.files" :key="file.id" 
                                       :href="'/storage/' + file.path"
                                       target="_blank"
                                       class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                                        <i class="fas fa-paperclip mr-1"></i>
                                        {{ file.name }}
                                    </a>
                                </div>
                                <span v-else class="text-xs text-gray-400">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ record.created_by ? record.created_by.name : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <Link :href="route('kpis.records.edit', record.id)" class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button @click="deleteRecord(record.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="records.data.length === 0">
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No hay registros para este KPI en el periodo seleccionado
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <Pagination :links="records.links" class="mt-6" />
        </div>
    </AppLayout>
</template>
