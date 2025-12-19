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
    policies: Object,
    typeOptions: Array,
    statusOptions: Array,
    userOptions: Array,
    filters: Object,
});

// Filters
const search = ref(props.filters.search || '');
const type_filter = ref(props.filters.type_filter || '');
const status_filter = ref(props.filters.status_filter || '');
const assigned_to_filter = ref(props.filters.assigned_to_filter || '');
const date_from = ref(props.filters.date_from || '');
const date_to = ref(props.filters.date_to || '');

// Watch for filter changes
const updateFilters = debounce(() => {
    router.get(route('policies.index'), {
        search: search.value,
        type_filter: type_filter.value,
        status_filter: status_filter.value,
        assigned_to_filter: assigned_to_filter.value,
        date_from: date_from.value,
        date_to: date_to.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300);

watch([search, type_filter, status_filter, assigned_to_filter, date_from, date_to], updateFilters);

const resetFilters = () => {
    search.value = '';
    type_filter.value = '';
    status_filter.value = '';
    assigned_to_filter.value = '';
    date_from.value = '';
    date_to.value = '';
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { timeZone: 'UTC' });
};

// Deletion Logic
const showDeleteModal = ref(false);
const policyToDelete = ref(null);

const confirmDelete = (policy) => {
    policyToDelete.value = policy;
    showDeleteModal.value = true;
};

const deletePolicy = () => {
    if (policyToDelete.value) {
        router.delete(route('policies.destroy', policyToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false;
                policyToDelete.value = null;
            }
        });
    }
};

const getStatusBadgeClass = (statusName) => {
    switch (statusName) {
        case 'Activa': return 'bg-green-100 text-green-800';
        case 'En Revisión': return 'bg-yellow-100 text-yellow-800';
        case 'Vencida': return 'bg-red-100 text-red-800';
        case 'Suspendida': return 'bg-gray-100 text-gray-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <AppLayout title="Políticas">
        <main class="flex-1 p-10 bg-gray-100 font-sans">
            
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Políticas</h1>
                        <p class="text-gray-600 mt-1">Gestión de políticas empresariales</p>
                    </div>
                    <div class="flex space-x-3">
                         <Link :href="route('policies.create')" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center">
                            <i class="fas fa-plus mr-2"></i>Nueva Política
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
                         <input v-model="search" type="text" placeholder="Nombre o descripción..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                    </div>

                    <!-- Type -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Política</label>
                         <select v-model="type_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Todos</option>
                            <option v-for="option in typeOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
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

                    <!-- Assigned To -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                         <select v-model="assigned_to_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Todos</option>
                            <option v-for="option in userOptions" :key="option.id" :value="option.id">{{ option.name }}</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Emisión Desde</label>
                         <input v-model="date_from" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                    </div>

                    <!-- Date To -->
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Emisión Hasta</label>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Emisión</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Revisión</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                       <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="policies.data.length === 0">
                                <td colspan="9" class="px-6 py-4 text-center text-gray-500">No se encontraron políticas</td>
                            </tr>
                            <tr v-for="policy in policies.data" :key="policy.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ policy.id }}</td>
                                <td class="px-6 py-4">
                                     <div class="text-sm font-medium text-gray-900">{{ policy.name }}</div>
                                     <div class="text-xs text-gray-500 mt-1">{{ policy.description ? policy.description.substring(0, 50) + (policy.description.length > 50 ? '...' : '') : '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <span v-if="policy.type" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ policy.type.name }}
                                     </span>
                                     <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <div class="text-sm text-gray-900">{{ formatDate(policy.issued_at) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <div class="text-sm text-gray-900">{{ formatDate(policy.reviewed_at) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <div class="text-sm text-gray-900">{{ policy.assigned_to ? policy.assigned_to.name : '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <span v-if="policy.status" :class="getStatusBadgeClass(policy.status.name)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ policy.status.name }}
                                     </span>
                                     <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="policy.files && policy.files.length > 0" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ policy.files.length }}
                                    </span>
                                    <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                     <div class="flex space-x-2">
                                        <Link :href="route('policies.show', policy.id)" class="text-green-600 hover:text-green-900 flex items-center" title="Ver">
                                            <i class="fas fa-eye mr-1"></i> Ver
                                        </Link>

                                        <Link :href="route('policies.edit', policy.id)" class="text-blue-600 hover:text-blue-900 flex items-center" title="Editar">
                                            <i class="fas fa-pen-to-square mr-1"></i> Editar
                                        </Link>

                                        <button @click="confirmDelete(policy)" class="text-red-600 hover:text-red-900 flex items-center" title="Eliminar">
                                            <i class="fas fa-trash mr-1"></i> Eliminar
                                        </button>
                                     </div>
                                </td>
                            </tr>
                       </tbody>
                    </table>
                </div>
                 <div class="p-4 border-t border-gray-200">
                    <Pagination :links="policies.links" />
                </div>
            </div>

             <!-- Delete Modal -->
            <DialogModal :show="showDeleteModal" @close="showDeleteModal = false">
                <template #title>Eliminar Política</template>
                <template #content>
                    <p>¿Estás seguro de que quieres eliminar esta política?</p>
                </template>
                <template #footer>
                    <SecondaryButton @click="showDeleteModal = false">Cancelar</SecondaryButton>
                    <DangerButton @click="deletePolicy" class="ml-3">Eliminar</DangerButton>
                </template>
            </DialogModal>

        </main>
    </AppLayout>
</template>
