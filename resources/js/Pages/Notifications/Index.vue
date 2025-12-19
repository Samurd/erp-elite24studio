<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    notifications: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const status_filter = ref(props.filters.status_filter || '');
const type_filter = ref(props.filters.type_filter || '');
const perPage = ref(props.filters.perPage || '10');

const clearFilters = () => {
    search.value = '';
    status_filter.value = '';
    type_filter.value = '';
    perPage.value = '10';
    // Watchers will trigger the update
};

const update = debounce(() => {
    router.get(route('notifications.index'), {
        search: search.value,
        status_filter: status_filter.value,
        type_filter: type_filter.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, 300);

watch([search, status_filter, type_filter, perPage], update);

const toggleStatus = (id) => {
    router.post(route('notifications.toggle-status', id), {}, {
        preserveScroll: true,
    });
};

const destroy = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta notificación?')) {
        router.delete(route('notifications.destroy', id));
    }
};

const typeColors = {
    recurring: 'bg-purple-100 text-purple-800',
    reminder: 'bg-blue-100 text-blue-800',
    scheduled: 'bg-yellow-100 text-yellow-800',
};

const typeLabels = {
    recurring: 'Recurrente',
    reminder: 'Recordatorio',
    scheduled: 'Programada',
};

// Helper for date formatting, can use a library or just simple JS
const formatDate = (dateString, format = 'date-time') => {
    if (!dateString) return '-';
    // Simple format for now, better to use date-fns or moment if available
    const date = new Date(dateString);
    if (format === 'diff') {
        const now = new Date();
        const diff = Math.round((date - now) / 1000 / 60 / 60 / 24); // days
        return diff === 0 ? 'Hoy' : (diff > 0 ? `En ${diff} días` : `Hace ${Math.abs(diff)} días`); 
    }
    return date.toLocaleString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

</script>

<template>
    <AppLayout title="Plantillas de Notificaciones">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Plantillas de Notificaciones
            </h2>
        </template>

        <div>
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Plantillas de Notificaciones</h1>
                        <p class="text-gray-600 mt-1">Gestión de notificaciones programadas y recurrentes</p>
                    </div>
                    <div class="flex space-x-3">
                        <Link :href="route('notifications.create')"
                            class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center">
                            <i class="fas fa-plus mr-2"></i>Nueva Notificación
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
                        <input type="text" v-model="search" placeholder="Título o mensaje..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>

                    <!-- Filtro por estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select v-model="status_filter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Todos</option>
                            <option value="active">Activas</option>
                            <option value="inactive">Inactivas</option>
                        </select>
                    </div>

                    <!-- Filtro por tipo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select v-model="type_filter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Todos</option>
                            <option value="recurring">Recurrente</option>
                            <option value="scheduled">Programada</option>
                            <option value="reminder">Recordatorio</option>
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
                    <div class="flex items-end lg:col-start-4">
                        <button @click="clearFilters"
                            class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center justify-center">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Título / Mensaje
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Destinatario
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Relacionado con
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Próximo Envío
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="notifications.data.length === 0">
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron notificaciones
                                </td>
                            </tr>
                            <tr v-else v-for="notification in notifications.data" :key="notification.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        #{{ notification.id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ notification.title }}
                                    </div>
                                    <div class="text-xs text-gray-500 truncate max-w-xs" :title="notification.message">
                                        {{ notification.message.length > 50 ? notification.message.substring(0, 50) + '...' : notification.message }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="typeColors[notification.type] || 'bg-gray-100 text-gray-800'">
                                        {{ typeLabels[notification.type] || notification.type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="notification.user">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ notification.user.name }}
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ notification.user.email }}
                                        </div>
                                    </div>
                                    <span v-else class="text-sm text-gray-500">Sin usuario</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <!-- Simplified logic for notifiable as we don't have polymorphic relation full info -->
                                     <!-- Assuming notifiable_type is the class name -->
                                    <template v-if="notification.notifiable_type">
                                         <div class="text-sm text-gray-900">
                                            {{ notification.notifiable_type.split('\\').pop() }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            ID: {{ notification.notifiable_id }}
                                        </div>
                                    </template>
                                    <span v-else class="text-sm text-gray-500">General</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="notification.next_send_at">
                                        <div class="text-sm text-gray-900">
                                            {{ formatDate(notification.next_send_at) }}
                                        </div>
                                        <!-- diffForHumans is PHP, here we use simple JS helper or ignore -->
                                    </div>
                                    <div v-else-if="notification.scheduled_at">
                                        <div class="text-sm text-gray-900">
                                            {{ formatDate(notification.scheduled_at) }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Programada
                                        </div>
                                    </div>
                                    <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="notification.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                        {{ notification.is_active ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button @click="toggleStatus(notification.id)"
                                            :class="notification.is_active ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900'"
                                            :title="notification.is_active ? 'Pausar' : 'Activar'">
                                            <p>{{ notification.is_active ? 'Pausar' : 'Activar' }}</p>
                                        </button>
                                        
                                        <Link :href="route('notifications.edit', { notification: notification.id })" class="text-blue-600 hover:text-blue-900" title="Editar">
                                            <i class="fas fa-pen-to-square"></i> Editar
                                        </Link>

                                        <button @click="destroy(notification.id)"
                                            class="text-red-600 hover:text-red-900" title="Eliminar">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <Pagination :links="notifications.links" />
                </div>
            </div>

            <!-- Success Message -->
            <div v-if="$page.props.flash?.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 mt-4">
                {{ $page.props.flash.success }}
            </div>
        </div>
    </AppLayout>
</template>
