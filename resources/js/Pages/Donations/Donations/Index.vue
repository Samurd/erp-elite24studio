<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    donations: Object,
    filters: Object,
    campaigns: Array,
    paymentMethods: Array,
});

const search = ref(props.filters.search || '');
const campaign_filter = ref(props.filters.campaign_filter || '');
const payment_method_filter = ref(props.filters.payment_method_filter || '');
const certified_filter = ref(props.filters.certified_filter || '');
const date_from = ref(props.filters.date_from || '');
const date_to = ref(props.filters.date_to || '');
const perPage = ref(props.filters.perPage || 10);

const updateSearch = debounce(() => {
    applyFilters();
}, 300);

const applyFilters = () => {
    router.get(route('donations.donations.index'), {
        search: search.value,
        campaign_filter: campaign_filter.value,
        payment_method_filter: payment_method_filter.value,
        certified_filter: certified_filter.value,
        date_from: date_from.value,
        date_to: date_to.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

watch([campaign_filter, payment_method_filter, certified_filter, date_from, date_to, perPage], () => {
    applyFilters();
});

const clearFilters = () => {
    search.value = '';
    campaign_filter.value = '';
    payment_method_filter.value = '';
    certified_filter.value = '';
    date_from.value = '';
    date_to.value = '';
    applyFilters();
};

const formatCurrency = (value) => {
    if (value === null || value === undefined) return '-';
    // Amount is stored in cents, convert to main unit
    value = value / 100;
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP', 
        minimumFractionDigits: 2,
    }).format(value);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    // Handle date string possibly including time
    const date = new Date(dateString);
    // Adjust for timezone if needed, typically UTC from backend
    // For display, local date string is usually fine, but be consistent
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const deleteDonation = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta donación?')) {
        router.delete(route('donations.donations.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout title="Donaciones">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Donaciones</h1>
                    <p class="text-gray-600 mt-1">Gestión de donaciones del sistema</p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('donations.donations.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nueva Donación
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
                        placeholder="Nombre del donante..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Filtro por campaña -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Campaña</label>
                    <select v-model="campaign_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todas</option>
                        <option v-for="campaign in campaigns" :key="campaign.id" :value="campaign.id">
                            {{ campaign.name }}
                        </option>
                    </select>
                </div>

                <!-- Filtro por método de pago -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Método de Pago</label>
                    <select v-model="payment_method_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option v-for="method in paymentMethods" :key="method" :value="method">
                            {{ method }}
                        </option>
                    </select>
                </div>

                <!-- Filtro por certificado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Certificado</label>
                    <select v-model="certified_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>

                <!-- Fecha desde -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Donación Desde</label>
                    <input type="date" v-model="date_from"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Fecha hasta -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Donación Hasta</label>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Donante</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaña</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método de Pago</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificado</th>
                            <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivos</th> -->
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="donation in donations.data" :key="donation.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">#{{ donation.id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ donation.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ donation.campaign ? donation.campaign.name : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ formatCurrency(donation.amount) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ donation.payment_method }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div v-if="donation.date" class="text-sm text-gray-900">{{ formatDate(donation.date) }}</div>
                                <span v-else class="text-sm text-gray-500">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="donation.certified" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Sí
                                </span>
                                <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    No
                                </span>
                            </td>
                            <!-- <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ donation.files_count || 0 }}</div> 
                                Note: logic for files_count might need to be added to controller query if important to show
                            -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <Link :href="route('donations.donations.edit', donation.id)" class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </Link>
                                    <button @click="deleteDonation(donation.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="donations.data.length === 0">
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron donaciones
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <Pagination :links="donations.links" class="mt-6" />
        </div>
    </AppLayout>
</template>
