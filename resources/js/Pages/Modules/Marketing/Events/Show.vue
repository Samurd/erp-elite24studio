<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    event: Object,
    items: Object,
    unitOptions: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const unitFilter = ref(props.filters.unitFilter || '');
const perPage = ref(props.filters.perPage || 10);

const updateFilters = debounce(() => {
    router.get(route('marketing.events.show', props.event.id), {
        search: search.value,
        unitFilter: unitFilter.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
    });
}, 300);

watch([search, unitFilter, perPage], () => {
    updateFilters();
});

const deleteItem = (id) => {
     if (confirm('¿Estás seguro de que quieres eliminar este ítem?')) {
        router.delete(route('marketing.events.items.destroy', [props.event.id, id]), {
            preserveScroll: true,
        });
    }
}

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
</script>

<template>
    <AppLayout :title="`Evento: ${event.name}`">
        <!-- Header & Event Details -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
             <div class="flex justify-between items-start mb-6">
                <div>
                     <Link :href="route('marketing.events.index')" class="text-yellow-600 hover:underline mb-2 inline-block">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a Eventos
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-800">{{ event.name }}</h1>
                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                         <span v-if="event.status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                            {{ event.status.name }}
                        </span>
                         <span><i class="far fa-calendar-alt mr-1"></i> {{ formatDate(event.event_date) }}</span>
                         <span><i class="fas fa-map-marker-alt mr-1"></i> {{ event.location }}</span>
                    </div>
                </div>
                 <div class="flex space-x-3">
                     <Link :href="route('marketing.events.edit', event.id)"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-pen mr-2"></i>Editar Evento
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-t pt-4">
                 <div>
                    <h3 class="text-sm font-medium text-gray-500">Tipo</h3>
                    <p class="mt-1 text-gray-900">{{ event.type?.name || '-' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Responsable</h3>
                    <p class="mt-1 text-gray-900">{{ event.responsible?.name || '-' }}</p>
                </div>
                 <div>
                    <h3 class="text-sm font-medium text-gray-500">Archivos Adjuntos</h3>
                     <div v-if="event.files && event.files.length" class="mt-1">
                        <ul class="space-y-1">
                            <li v-for="file in event.files" :key="file.id">
                                <a :href="'/storage/' + file.path" target="_blank" class="text-blue-600 hover:underline flex items-center gap-2 text-sm">
                                    <i class="fas fa-paperclip"></i> {{ file.name }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <p v-else class="mt-1 text-gray-500 text-sm">No hay archivos adjuntos.</p>
                </div>
                 <div class="col-span-full" v-if="event.observations">
                    <h3 class="text-sm font-medium text-gray-500">Observaciones</h3>
                    <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ event.observations }}</p>
                </div>
            </div>
        </div>

        <!-- Items Section -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Ítems / Presupuesto</h2>
                 <Link :href="route('marketing.events.items.create', event.id)"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Agregar Ítem
                </Link>
            </div>

             <!-- Filters for Items -->
             <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                 <div>
                    <input type="text"
                        v-model="search"
                        placeholder="Buscar ítem..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>
                 <div>
                    <select v-model="unitFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todas las unidades</option>
                        <option v-for="unit in unitOptions" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                    </select>
                </div>
             </div>

             <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vr. Unitario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                         <tr v-for="item in items.data" :key="item.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ item.description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.unit ? item.unit.name : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency(item.unit_price) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ formatCurrency(item.total_price) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <Link :href="route('marketing.events.items.edit', [event.id, item.id])"
                                        class="text-blue-600 hover:text-blue-900" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </Link>
                                    <button @click="deleteItem(item.id)"
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                         </tr>
                         <tr v-if="items.data.length === 0">
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No hay ítems registrados para este evento.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="items.links && items.links.length > 3" class="mt-4">
                 <div class="flex items-center justify-between">
                     <div>
                         <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <template v-for="(link, key) in items.links" :key="key">
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
    </AppLayout>
</template>
