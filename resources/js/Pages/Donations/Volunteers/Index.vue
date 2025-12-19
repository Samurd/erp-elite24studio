<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    volunteers: Object,
    filters: Object,
    campaigns: Array,
    statusOptions: Array,
    roles: Array,
    cities: Array,
    countries: Array,
});

const search = ref(props.filters.search || '');
const campaign_filter = ref(props.filters.campaign_filter || '');
const status_filter = ref(props.filters.status_filter || '');
const role_filter = ref(props.filters.role_filter || '');
const certified_filter = ref(props.filters.certified_filter || '');
const city_filter = ref(props.filters.city_filter || '');
const country_filter = ref(props.filters.country_filter || '');
const perPage = ref(props.filters.perPage || 10);

const updateSearch = debounce(() => {
    applyFilters();
}, 300);

const applyFilters = () => {
    router.get(route('donations.volunteers.index'), {
        search: search.value,
        campaign_filter: campaign_filter.value,
        status_filter: status_filter.value,
        role_filter: role_filter.value,
        certified_filter: certified_filter.value,
        city_filter: city_filter.value,
        country_filter: country_filter.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

watch([campaign_filter, status_filter, role_filter, certified_filter, city_filter, country_filter, perPage], () => {
    applyFilters();
});

const clearFilters = () => {
    search.value = '';
    campaign_filter.value = '';
    status_filter.value = '';
    role_filter.value = '';
    certified_filter.value = '';
    city_filter.value = '';
    country_filter.value = '';
    applyFilters();
};

const deleteVolunteer = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este voluntario?')) {
        router.delete(route('donations.volunteers.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout title="Voluntarios">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Voluntarios</h1>
                    <p class="text-gray-600 mt-1">Gestión de voluntarios del sistema</p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('donations.volunteers.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nuevo Voluntario
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
                        placeholder="Nombre, email o teléfono..."
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

                <!-- Filtro por Estado -->
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

                <!-- Filtro por Rol -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                     <select v-model="role_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option v-for="role in roles" :key="role" :value="role">
                            {{ role }}
                        </option>
                    </select>
                </div>

                 <!-- Filtro por Ciudad -->
                 <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ciudad</label>
                     <select v-model="city_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todas</option>
                        <option v-for="city in cities" :key="city" :value="city">
                            {{ city }}
                        </option>
                    </select>
                </div>

                <!-- Filtro por País -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">País</label>
                     <select v-model="country_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option v-for="country in countries" :key="country" :value="country">
                            {{ country }}
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaña</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="volunteer in volunteers.data" :key="volunteer.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ volunteer.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <div v-if="volunteer.email"><i class="fas fa-envelope mr-1 text-gray-400"></i> {{ volunteer.email }}</div>
                                    <div v-if="volunteer.phone"><i class="fas fa-phone mr-1 text-gray-400"></i> {{ volunteer.phone }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ [volunteer.city, volunteer.country].filter(Boolean).join(', ') || '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ volunteer.campaign ? volunteer.campaign.name : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ volunteer.role || '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="volunteer.status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :style="{ backgroundColor: volunteer.status.color + '20', color: volunteer.status.color }">
                                    {{ volunteer.status.name }}
                                </span>
                                <span v-else class="text-gray-500 text-sm">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="volunteer.certified" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Sí
                                </span>
                                <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    No
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <Link :href="route('donations.volunteers.edit', volunteer.id)" class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </Link>
                                    <button @click="deleteVolunteer(volunteer.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="volunteers.data.length === 0">
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron voluntarios
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <Pagination :links="volunteers.links" class="mt-6" />
        </div>
    </AppLayout>
</template>
