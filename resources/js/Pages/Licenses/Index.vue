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
    licenses: Object,
    licenseTypeOptions: Array,
    statusOptions: Array,
    filters: Object,
});

// Filters
const search = ref(props.filters.search || '');
const license_type_filter = ref(props.filters.license_type_filter || '');
const status_filter = ref(props.filters.status_filter || '');
const entity_filter = ref(props.filters.entity_filter || '');
const company_filter = ref(props.filters.company_filter || '');
const requires_extension_filter = ref(props.filters.requires_extension_filter || '');
const date_from = ref(props.filters.date_from || '');
const date_to = ref(props.filters.date_to || '');

// Watch for filter changes
const updateFilters = debounce(() => {
    router.get(route('licenses.index'), {
        search: search.value,
        license_type_filter: license_type_filter.value,
        status_filter: status_filter.value,
        entity_filter: entity_filter.value,
        company_filter: company_filter.value,
        requires_extension_filter: requires_extension_filter.value,
        date_from: date_from.value,
        date_to: date_to.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300);

watch([search, license_type_filter, status_filter, entity_filter, company_filter, requires_extension_filter, date_from, date_to], updateFilters);

const resetFilters = () => {
    search.value = '';
    license_type_filter.value = '';
    status_filter.value = '';
    entity_filter.value = '';
    company_filter.value = '';
    requires_extension_filter.value = '';
    date_from.value = '';
    date_to.value = '';
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { timeZone: 'UTC' });
};

const isExpired = (dateString) => {
    if (!dateString) return false;
    const date = new Date(dateString);
    const now = new Date();
    return date < now;
};

// Deletion Logic
const showDeleteModal = ref(false);
const licenseToDelete = ref(null);

const confirmDelete = (license) => {
    licenseToDelete.value = license;
    showDeleteModal.value = true;
};

const deleteLicense = () => {
    if (licenseToDelete.value) {
        router.delete(route('licenses.destroy', licenseToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false;
                licenseToDelete.value = null;
            }
        });
    }
};

const getStatusBadgeClass = (statusName) => {
    switch (statusName) {
        case 'Aprobada': return 'bg-green-100 text-green-800';
        case 'En Trámite': return 'bg-yellow-100 text-yellow-800';
        case 'Rechazada': return 'bg-red-100 text-red-800';
        case 'En Revisión': return 'bg-blue-100 text-blue-800';
        case 'Observada': return 'bg-purple-100 text-purple-800';
        case 'Vencida': return 'bg-gray-100 text-gray-800';
        case 'Prorrogada': return 'bg-teal-100 text-teal-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <AppLayout title="Licencias">
        <main class="flex-1 p-10 bg-gray-100 font-sans">
            
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Licencias</h1>
                        <p class="text-gray-600 mt-1">Gestión de licencias del sistema</p>
                    </div>
                    <div class="flex space-x-3">
                         <Link :href="route('licenses.create')" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center">
                            <i class="fas fa-plus mr-2"></i>Nueva Licencia
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
                         <input v-model="search" type="text" placeholder="Entidad, empresa o erradicado..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                    </div>

                    <!-- Type -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Licencia</label>
                         <select v-model="license_type_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Todos</option>
                            <option v-for="option in licenseTypeOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                         <select v-model="status_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Todos</option>
                            <option v-for="option in statusOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                        </select>
                    </div>

                    <!-- Entity -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Entidad</label>
                         <input v-model="entity_filter" type="text" placeholder="Nombre entidad..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                    </div>

                    <!-- Company -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Empresa</label>
                         <input v-model="company_filter" type="text" placeholder="Nombre empresa..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                    </div>

                     <!-- Requires Extension -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Requiere Prórroga</label>
                         <select v-model="requires_extension_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Todos</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Vencimiento Desde</label>
                         <input v-model="date_from" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                    </div>

                    <!-- Date To -->
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Vencimiento Hasta</label>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número Erradicado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Vencimiento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prórroga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                       <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="licenses.data.length === 0">
                                <td colspan="11" class="px-6 py-4 text-center text-gray-500">No se encontraron licencias</td>
                            </tr>
                            <tr v-for="license in licenses.data" :key="license.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ license.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ license.project ? license.project.name : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ license.entity || '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ license.company || '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ license.eradicated_number || '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <span v-if="license.type" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ license.type.name }}
                                     </span>
                                     <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <span v-if="license.status" :class="getStatusBadgeClass(license.status.name)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ license.status.name }}
                                     </span>
                                     <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <div class="text-sm text-gray-900">{{ formatDate(license.expiration_date) }}</div>
                                     <div v-if="isExpired(license.expiration_date)" class="text-xs text-red-600 mt-1">Vencida</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <span v-if="license.requires_extension" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Sí</span>
                                     <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">No</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="license.files && license.files.length > 0" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ license.files.length }}
                                    </span>
                                    <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                     <div class="flex space-x-2">
                                        <Link :href="route('licenses.edit', license.id)" class="text-blue-600 hover:text-blue-900 flex items-center" title="Editar">
                                            <i class="fas fa-pen-to-square mr-1"></i> Editar
                                        </Link>

                                        <button @click="confirmDelete(license)" class="text-red-600 hover:text-red-900 flex items-center" title="Eliminar">
                                            <i class="fas fa-trash mr-1"></i> Eliminar
                                        </button>
                                     </div>
                                </td>
                            </tr>
                       </tbody>
                    </table>
                </div>
                 <div class="p-4 border-t border-gray-200">
                    <Pagination :links="licenses.links" />
                </div>
            </div>

             <!-- Delete Modal -->
            <DialogModal :show="showDeleteModal" @close="showDeleteModal = false">
                <template #title>Eliminar Licencia</template>
                <template #content>
                    <p>¿Estás seguro de que quieres eliminar esta licencia?</p>
                </template>
                <template #footer>
                    <SecondaryButton @click="showDeleteModal = false">Cancelar</SecondaryButton>
                    <DangerButton @click="deleteLicense" class="ml-3">Eliminar</DangerButton>
                </template>
            </DialogModal>

        </main>
    </AppLayout>
</template>
