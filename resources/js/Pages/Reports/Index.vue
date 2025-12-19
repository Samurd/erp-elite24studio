<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import Pagination from '@/Components/Pagination.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    reports: Object,
    statusOptions: Array,
    filters: Object,
});

// Filters
const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const date_from = ref(props.filters.date_from || '');
const date_to = ref(props.filters.date_to || '');

// Watch for filter changes
const updateFilters = debounce(() => {
    router.get(route('reports.index'), {
        search: search.value,
        status: status.value,
        date_from: date_from.value,
        date_to: date_to.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300);

watch([search, status, date_from, date_to], updateFilters);

const resetFilters = () => {
    search.value = '';
    status.value = '';
    date_from.value = '';
    date_to.value = '';
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { timeZone: 'UTC' });
};

const formatTime = (timeString) => {
    if (!timeString) return '';
    const [hours, minutes] = timeString.split(':');
    return `${hours}:${minutes}`;
};

// Deletion Logic
const showDeleteModal = ref(false);
const reportToDelete = ref(null);

const confirmDelete = (report) => {
    reportToDelete.value = report;
    showDeleteModal.value = true;
};

const deleteReport = () => {
    if (reportToDelete.value) {
        router.delete(route('reports.destroy', reportToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false;
                reportToDelete.value = null;
            }
        });
    }
};
</script>

<template>
    <AppLayout title="Reportes">
        <main class="flex-1 p-10 bg-gray-100 font-sans">
            
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Reportes</h1>
                        <p class="text-gray-600 mt-1">Gestión de reportes</p>
                    </div>
                    <div class="flex space-x-3">
                         <Link :href="route('reports.create')" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center">
                            <i class="fas fa-plus mr-2"></i>Nuevo Reporte
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                         <input v-model="search" type="text" placeholder="Título o Descripción..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                    </div>

                    <!-- Status -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                         <select v-model="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Todos</option>
                            <option v-for="option in statusOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                         <input v-model="date_from" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                    </div>

                    <!-- Date To -->
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                         <input v-model="date_to" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                    </div>

                    <!-- Clear Button -->
                     <div class="lg:col-start-4 flex items-end">
                         <button @click="resetFilters" class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                       <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="reports.data.length === 0">
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron reportes</td>
                            </tr>
                            <tr v-for="report in reports.data" :key="report.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ report.id }}</td>
                                <td class="px-6 py-4">
                                     <div class="text-sm font-medium text-gray-900">{{ report.title }}</div>
                                     <div class="text-xs text-gray-500">{{ report.description ? report.description.substring(0, 50) + (report.description.length > 50 ? '...' : '') : '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <div class="text-sm text-gray-900">{{ formatDate(report.date) }}</div>
                                     <div class="text-xs text-gray-500">{{ report.hour ? formatTime(report.hour) : '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <span v-if="report.status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ report.status.name }}
                                     </span>
                                     <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="report.files_count > 0" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ report.files_count }}
                                    </span>
                                    <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                     <div class="flex space-x-2">
                                        <Link :href="route('reports.edit', report.id)" class="text-blue-600 hover:text-blue-900 flex items-center" title="Editar">
                                            <i class="fas fa-pen-to-square mr-1"></i> Editar
                                        </Link>

                                        <button @click="confirmDelete(report)" class="text-red-600 hover:text-red-900 flex items-center" title="Eliminar">
                                            <i class="fas fa-trash mr-1"></i> Eliminar
                                        </button>
                                     </div>
                                </td>
                            </tr>
                       </tbody>
                    </table>
                </div>
                 <div class="p-4 border-t border-gray-200">
                    <Pagination :links="reports.links" />
                </div>
            </div>

             <!-- Delete Modal -->
            <DialogModal :show="showDeleteModal" @close="showDeleteModal = false">
                <template #title>Eliminar Reporte</template>
                <template #content>
                    <p>¿Estás seguro de que quieres eliminar este reporte?</p>
                </template>
                <template #footer>
                    <SecondaryButton @click="showDeleteModal = false">Cancelar</SecondaryButton>
                    <DangerButton @click="deleteReport" class="ml-3">Eliminar</DangerButton>
                </template>
            </DialogModal>

        </main>
    </AppLayout>
</template>
