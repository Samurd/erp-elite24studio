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
    subs: Object,
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
    router.get(route('subs.index'), {
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

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    }).format(value / 100);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    // Using UTC date to avoid timezone shifts if storing just dates
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { timeZone: 'UTC' }); 
};

// Deletion Logic
const showDeleteModal = ref(false);
const subToDelete = ref(null);

const confirmDelete = (sub) => {
    subToDelete.value = sub;
    showDeleteModal.value = true;
};

const deleteSub = () => {
    if (subToDelete.value) {
        router.delete(route('subs.destroy', subToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false;
                subToDelete.value = null;
            }
        });
    }
};

const createNotification = (sub) => {
    router.post(route('subs.notification', sub.id), {}, {
        preserveScroll: true
    });
};
</script>

<template>
    <AppLayout title="Suscripciones">
        <main class="flex-1 p-10 bg-gray-100 font-sans">
            
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Suscripciones</h1>
                        <p class="text-gray-600 mt-1">Gestión de suscripciones y subcontratos</p>
                    </div>
                    <div class="flex space-x-3">
                         <Link :href="route('subs.create')" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center">
                            <i class="fas fa-plus mr-2"></i>Nueva Suscripción
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
                         <input v-model="search" type="text" placeholder="Nombre..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" />
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
                         <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio Desde</label>
                         <input v-model="date_from" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                    </div>

                    <!-- Date To -->
                     <div>
                         <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio Hasta</label>
                         <input v-model="date_to" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                    </div>

                    <!-- Clear Button (Placed at end of grid visually or next line) -->
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frecuencia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notificación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                       <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="subs.data.length === 0">
                                <td colspan="9" class="px-6 py-4 text-center text-gray-500">No se encontraron suscripciones</td>
                            </tr>
                            <tr v-for="sub in subs.data" :key="sub.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ sub.id }}</td>
                                <td class="px-6 py-4">
                                     <div class="text-sm font-medium text-gray-900">{{ sub.name }}</div>
                                     <div class="text-xs text-gray-500">{{ sub.type }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <span v-if="sub.frequency" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ sub.frequency.name }}
                                     </span>
                                     <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ formatCurrency(sub.amount) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <div class="text-sm text-gray-900">Inicio: {{ formatDate(sub.start_date) }}</div>
                                     <div class="text-xs text-gray-500">Renovación: {{ formatDate(sub.renewal_date) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                     <span v-if="sub.status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ sub.status.name }}
                                     </span>
                                     <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="sub.files_count > 0" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ sub.files_count }}
                                    </span>
                                    <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                     {{ sub.notification_templates_count > 0 ? 'Si' : 'No' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                     <div class="flex space-x-2">
                                        <!-- Create Notification -->
                                        <button v-if="sub.notification_templates_count === 0 && sub.renewal_date && sub.user_id" 
                                            @click="createNotification(sub)"
                                            class="text-blue-600 hover:text-blue-900 flex items-center" title="Crear notificación">
                                            <i class="fas fa-plus mr-1"></i> Crear notificación
                                        </button>
                                        <span v-else-if="sub.notification_templates_count === 0" class="text-xs text-gray-500">
                                            Faltan datos
                                        </span>

                                        <Link :href="route('subs.edit', sub.id)" class="text-blue-600 hover:text-blue-900 flex items-center" title="Editar">
                                            <i class="fas fa-pen-to-square mr-1"></i> Editar
                                        </Link>

                                        <button @click="confirmDelete(sub)" class="text-red-600 hover:text-red-900 flex items-center" title="Eliminar">
                                            <i class="fas fa-trash mr-1"></i> Eliminar
                                        </button>
                                     </div>
                                </td>
                            </tr>
                       </tbody>
                    </table>
                </div>
                 <div class="p-4 border-t border-gray-200">
                    <Pagination :links="subs.links" />
                </div>
            </div>

             <!-- Delete Modal -->
            <DialogModal :show="showDeleteModal" @close="showDeleteModal = false">
                <template #title>Eliminar Suscripción</template>
                <template #content>
                    <p>¿Estás seguro de que quieres eliminar esta suscripción?</p>
                </template>
                <template #footer>
                    <SecondaryButton @click="showDeleteModal = false">Cancelar</SecondaryButton>
                    <DangerButton @click="deleteSub" class="ml-3">Eliminar</DangerButton>
                </template>
            </DialogModal>

        </main>
    </AppLayout>
</template>
