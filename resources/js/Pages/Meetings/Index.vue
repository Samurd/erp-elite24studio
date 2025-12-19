<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { debounce } from 'lodash';

const props = defineProps({
    meetings: Object,
    statusOptions: Array,
    teamOptions: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status_filter = ref(props.filters.status_filter || '');
const team_filter = ref(props.filters.team_filter || '');
const goal_filter = ref(props.filters.goal_filter || '');
const date_from = ref(props.filters.date_from || '');
const date_to = ref(props.filters.date_to || '');
const perPage = ref(props.filters.perPage || 10);

const updateFilters = debounce(() => {
    router.get(route('meetings.index'), {
        search: search.value,
        status_filter: status_filter.value,
        team_filter: team_filter.value,
        goal_filter: goal_filter.value,
        date_from: date_from.value,
        date_to: date_to.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, 300);

watch([search, status_filter, team_filter, goal_filter, date_from, date_to, perPage], () => {
    updateFilters();
});

const clearFilters = () => {
    search.value = '';
    status_filter.value = '';
    team_filter.value = '';
    goal_filter.value = '';
    date_from.value = '';
    date_to.value = '';
    perPage.value = 10;
};

const deleteMeeting = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta reunión?')) {
        router.delete(route('meetings.destroy', id));
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    // Adjust for timezone offset to display correct date
    const offset = date.getTimezoneOffset();
    const adjustedDate = new Date(date.getTime() + offset * 60000);
    return adjustedDate.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const formatTime = (timeString) => {
    if (!timeString) return '';
    // timeString is ISO format or HH:MM:SS. We need just HH:MM
    // If it's a full ISO string
    if (timeString.includes('T')) {
          const date = new Date(timeString);
          return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    }
    // If it's just HH:MM:SS
     return timeString.substring(0, 5);
};

const isPast = (dateString) => {
    if (!dateString) return false;
    const date = new Date(dateString);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return date < today;
};

</script>

<template>
    <AppLayout title="Reuniones">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reuniones
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Reuniones</h1>
                            <p class="text-gray-600 mt-1">Gestión de reuniones del sistema</p>
                        </div>
                        <div class="flex space-x-3">
                            <Link :href="route('meetings.create')"
                               class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>Nueva Reunión
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
                            <input type="text" v-model="search"
                                   placeholder="Título, notas u observaciones..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>

                        <!-- Status Filter -->
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

                        <!-- Team Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Equipo</label>
                            <select v-model="team_filter"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                <option value="">Todos</option>
                                <option v-for="team in teamOptions" :key="team.id" :value="team.id">
                                    {{ team.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Goal Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Cumplida</label>
                            <select v-model="goal_filter"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                <option value="">Todos</option>
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                            <input type="date" v-model="date_from"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>

                        <!-- Date To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                            <input type="date" v-model="date_to"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>

                        <!-- Per Page -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Registros por página</label>
                            <select v-model="perPage"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                <option :value="10">10</option>
                                <option :value="25">25</option>
                                <option :value="50">50</option>
                                <option :value="100">100</option>
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        <div class="flex items-end">
                            <button @click="clearFilters"
                                    class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                                <i class="fas fa-times mr-2"></i>Limpiar Filtros
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-lg shadow-sm ">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsables</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="meeting in meetings.data" :key="meeting.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">#{{ meeting.id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ meeting.title }}</div>
                                        <div v-if="meeting.notes" class="text-xs text-gray-500 mt-1">
                                            {{ meeting.notes.substring(0, 50) }}{{ meeting.notes.length > 50 ? '...' : '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatDate(meeting.date) }}</div>
                                        <div v-if="isPast(meeting.date)" class="text-xs text-red-600">Pasada</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <span v-if="meeting.start_time">{{ formatTime(meeting.start_time) }}</span>
                                            <span v-if="meeting.end_time"> - {{ formatTime(meeting.end_time) }}</span>
                                            <span v-if="!meeting.start_time">-</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ meeting.team ? meeting.team.name : '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="meeting.status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-blue-100 text-blue-800': meeting.status.name == 'Programada',
                                                'bg-green-100 text-green-800': meeting.status.name == 'Realizada',
                                                'bg-red-100 text-red-800': meeting.status.name == 'Cancelada',
                                                'bg-yellow-100 text-yellow-800': meeting.status.name == 'Postergada',
                                                'bg-purple-100 text-purple-800': meeting.status.name == 'En Progreso',
                                                'bg-gray-100 text-gray-800': !['Programada', 'Realizada', 'Cancelada', 'Postergada', 'En Progreso'].includes(meeting.status.name)
                                            }">
                                            {{ meeting.status.name }}
                                        </span>
                                        <span v-else class="text-sm text-gray-500">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a v-if="meeting.url" :href="meeting.url" target="_blank" class="text-blue-600 hover:text-blue-900 text-sm" title="Abrir URL">
                                            <i class="fas fa-external-link-alt mr-1"></i> {{ meeting.url.substring(0, 30) }}{{ meeting.url.length > 30 ? '...' : '' }}
                                        </a>
                                        <span v-else class="text-sm text-gray-500">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                              :class="meeting.goal ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                            {{ meeting.goal ? 'Sí' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div v-if="meeting.responsibles && meeting.responsibles.length > 0" class="flex flex-wrap gap-1">
                                            <span v-for="resp in meeting.responsibles" :key="resp.id" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                {{ resp.name }}
                                            </span>
                                        </div>
                                        <span v-else class="text-sm text-gray-500">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <Link :href="route('meetings.show', meeting.id)" class="text-green-600 hover:text-green-900" title="Ver">
                                                <i class="fa-solid fa-eye mr-1"></i> Ver
                                            </Link>
                                            <Link :href="route('meetings.edit', meeting.id)" class="text-blue-600 hover:text-blue-900" title="Editar">
                                                <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                            </Link>
                                            <button @click="deleteMeeting(meeting.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="meetings.data.length === 0">
                                    <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron reuniones
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <Pagination :links="meetings.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
