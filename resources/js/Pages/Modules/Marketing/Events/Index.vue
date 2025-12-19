<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    events: Object,
    eventTypeOptions: Array,
    statusOptions: Array,
    responsibleOptions: Array,
    totalPresupuesto: Number,
    filters: Object,
});

const search = ref(props.filters.search || '');
const type_filter = ref(props.filters.type_filter || '');
const status_filter = ref(props.filters.status_filter || '');
const responsible_filter = ref(props.filters.responsible_filter || '');
const date_from = ref(props.filters.date_from || '');
const date_to = ref(props.filters.date_to || '');
const perPage = ref(props.filters.perPage || 10);

const updateFilters = debounce(() => {
    router.get(route('marketing.events.index'), {
        search: search.value,
        type_filter: type_filter.value,
        status_filter: status_filter.value,
        responsible_filter: responsible_filter.value,
        date_from: date_from.value,
        date_to: date_to.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300);

watch([search, type_filter, status_filter, responsible_filter, date_from, date_to, perPage], () => {
    updateFilters();
});

const clearFilters = () => {
    search.value = '';
    type_filter.value = '';
    status_filter.value = '';
    responsible_filter.value = '';
    date_from.value = '';
    date_to.value = '';
    perPage.value = 10;
};

const deleteEvent = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este evento?')) {
        router.delete(route('marketing.events.destroy', id), {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const datePart = dateString.split('T')[0];
    const [year, month, day] = datePart.split('-');
    return `${day}/${month}/${year}`;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    }).format((value || 0) / 100);
};

const calculateTotal = (items) => {
    return items ? items.reduce((sum, item) => sum + Number(item.total_price || 0), 0) : 0;
};
</script>

<template>
    <AppLayout title="Eventos de Marketing">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Eventos de Marketing</h1>
                    <p class="text-gray-600 mt-1">Gestión de eventos, logística y presupuesto</p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('marketing.events.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nuevo Evento
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
                        placeholder="Nombre o lugar..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Filtro por tipo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                    <select v-model="type_filter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos</option>
                        <option v-for="type in eventTypeOptions" :key="type.id" :value="type.id">{{ type.name }}</option>
                    </select>
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
                        <option v-for="user in responsibleOptions" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                </div>

                <!-- Fecha desde -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                    <input type="date"
                        v-model="date_from"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Fecha hasta -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                    <input type="date"
                        v-model="date_to"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Registros por página -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Registros</label>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lugar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                             <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="event in events.data" :key="event.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ event.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ event.name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ event.type ? event.type.name : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-if="event.status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ event.status.name }}
                                </span>
                                <span v-else class="text-sm text-gray-500">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatDate(event.event_date) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ event.location }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ event.responsible ? event.responsible.name : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ formatCurrency(calculateTotal(event.items)) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                     <Link :href="route('marketing.events.show', event.id)"
                                        class="text-green-600 hover:text-green-900" title="Ver Detalles">
                                        <i class="fa-solid fa-eye mr-1"></i> Ver
                                    </Link>
                                    <Link :href="route('marketing.events.edit', event.id)"
                                        class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                    </Link>
                                    <button @click="deleteEvent(event.id)"
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="events.data.length === 0">
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron eventos
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="events.links && events.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                 <div class="flex items-center justify-between">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                         <div>
                            <p class="text-sm text-gray-700">
                                Mostrando
                                <span class="font-medium">{{ events.from }}</span>
                                a
                                <span class="font-medium">{{ events.to }}</span>
                                de
                                <span class="font-medium">{{ events.total }}</span>
                                resultados
                            </p>
                        </div>
                        <div>
                             <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <template v-for="(link, key) in events.links" :key="key">
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
