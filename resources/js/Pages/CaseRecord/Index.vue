<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import Pagination from '@/Components/Pagination.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    records: Object,
    states: Array,
    case_types: Array,
    users: Array,
    filters: Object,
});

// Filters
const search = ref(props.filters.search || '');
const canal = ref(props.filters.canal || '');
const estado = ref(props.filters.estado || '');
const asesor = ref(props.filters.asesor || '');
const tipo_caso = ref(props.filters.tipo_caso || '');
const fecha = ref(props.filters.fecha || '');

// Watch for filter changes
const updateFilters = debounce(() => {
    router.get(route('case-record.index'), {
        search: search.value,
        canal: canal.value,
        estado: estado.value,
        asesor: asesor.value,
        tipo_caso: tipo_caso.value,
        fecha: fecha.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300);

watch([search, canal, estado, asesor, tipo_caso, fecha], updateFilters);

const resetFilters = () => {
    search.value = '';
    canal.value = '';
    estado.value = '';
    asesor.value = '';
    tipo_caso.value = '';
    fecha.value = '';
};

// Deletion Logic
const showDeleteModal = ref(false);
const recordToDelete = ref(null);

const confirmDelete = (record) => {
    recordToDelete.value = record;
    showDeleteModal.value = true;
};

const deleteRecord = () => {
    if (recordToDelete.value) {
        router.delete(route('case-record.destroy', recordToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false;
                recordToDelete.value = null;
            }
        });
    }
};

const getStatusColor = (name) => {
    const colors = {
        'Abierto': 'bg-green-500',
        'En proceso': 'bg-yellow-500',
        'Escalado': 'bg-red-500',
        'Cerrado': 'bg-gray-500'
    };
    return colors[name] || 'bg-black';
};

const formatTypeName = (name) => {
    return name.replace(/-/g, ' ').replace(/^\w/, c => c.toUpperCase());
};
</script>

<template>
    <AppLayout title="Registro de Casos">
        <main class="flex-1 p-10 bg-gray-100 font-sans">
            
            <!-- Filters Section -->
            <div class="bg-white shadow-md rounded-lg p-4 mb-10">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Filtros de búsqueda</h2>
                
                <input v-model="search" type="text" placeholder="Buscar por asesor, contacto..." class="w-full border border-gray-300 rounded-lg px-3 py-2 mb-4 focus:ring-yellow-500 focus:border-yellow-500" />
                
                <div class="flex flex-wrap items-center gap-3">
                    <input v-model="canal" type="text" placeholder="Canal" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-yellow-500 focus:border-yellow-500" />
                    
                    <select v-model="tipo_caso" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">Tipo de caso</option>
                        <option v-for="type in case_types" :key="type.id" :value="type.id">{{ type.name }}</option>
                    </select>

                    <select v-model="estado" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">Estado</option>
                        <option v-for="state in states" :key="state.id" :value="state.id">{{ state.name }}</option>
                    </select>

                    <select v-model="asesor" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">Asesor</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>

                    <input v-model="fecha" type="date" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-yellow-500 focus:border-yellow-500" />

                    <button @click="resetFilters" class="px-4 py-2 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition">
                        Limpiar filtros
                    </button>
                </div>
            </div>

            <!-- Content Section -->
            <div class="bg-white w-full p-4 rounded-xl shadow">
                
                <!-- Header -->
                <div class="flex justify-between items-center mb-6 px-2">
                    <div>
                        <h3 class="text-2xl font-bold leading-none">Área comercial</h3>
                        <h3 class="text-lg font-bold leading-none text-gray-500 mt-1">Elite 24 STUDIO S.A.S</h3>
                    </div>
                    
                    <!-- Create Button Toggle -->
                     <div class="rounded-full p-2 bg-gradient-to-r from-black via-amber-900 to-amber-500">
                        <Link :href="route('case-record.create')" class="flex items-center cursor-pointer group">
                            <span class="ml-3 text-sm font-medium text-white mr-2">Nuevo registro</span>
                            <div class="relative w-10 h-5 bg-gray-300 rounded-full group-hover:bg-gray-400 transition">
                                <!-- Simulated Toggle Switch Appearance -->
                                <div class="absolute w-4 h-4 bg-white rounded-full top-0.5 left-0.5 transform translate-x-5 transition-transform"></div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto w-full rounded-xl bg-white shadow">
                    <table class="w-full text-sm text-left text-gray-500 whitespace-nowrap">
                        <thead class="text-xs text-white uppercase bg-gradient-to-r from-black via-amber-900 to-amber-500">
                            <tr>
                                <th class="px-6 py-5">Cod</th>
                                <th class="px-6 py-5">Fecha</th>
                                <th class="px-6 py-5">Nombre Contacto</th>
                                <th class="px-6 py-5">Nombre Asesor</th>
                                <th class="px-6 py-5">Tipo de caso</th>
                                <th class="px-6 py-5">Estado</th>
                                <th class="px-6 py-5">Canal</th>
                                <th class="px-6 py-5">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="records.data.length === 0">
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">No hay registros disponibles.</td>
                            </tr>
                            <tr v-for="record in records.data" :key="record.id" class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ record.id }}</td>
                                <td class="px-6 py-4">{{ record.date }}</td>
                                <td class="px-6 py-4">
                                     <!-- Link to Contacts Index with Search -->
                                     <a v-if="record.contact" :href="route('contacts.index', { search: record.contact.name })" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                                        <span>{{ record.contact.name }}</span>
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                     </a>
                                     <span v-else>No contacto relacionado</span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ record.assigned_to ? record.assigned_to.name : 'No asesor relacionado' }}
                                </td>
                                <td class="px-6 py-4">{{ record.type ? formatTypeName(record.type.name) : '-' }}</td>
                                <td class="px-6 py-4 flex items-center">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full mr-2" :class="getStatusColor(record.status ? record.status.name : '')"></span>
                                    {{ record.status ? record.status.name : '' }}
                                </td>
                                <td class="px-6 py-4">{{ record.channel }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <Link :href="route('case-record.edit', record.id)" class="text-gray-600 hover:text-yellow-700 uppercase font-bold text-xs">
                                            Editar
                                        </Link>
                                        <button @click="confirmDelete(record)" class="text-red-500 hover:text-red-700 text-xs uppercase font-bold">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                 <div class="mt-4">
                    <Pagination :links="records.links" />
                </div>

            </div>
        </main>

        <!-- Delete Confirmation Modal -->
        <DialogModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>Eliminar Registro de Caso</template>
            <template #content>
                <p>¿Estás seguro de que deseas eliminar el registro con el código: <strong>{{ recordToDelete?.id }}</strong>?</p>
                <p class="text-sm text-gray-500 mt-2">Esta acción no se puede deshacer.</p>
            </template>
            <template #footer>
                <SecondaryButton @click="showDeleteModal = false">Cancelar</SecondaryButton>
                <DangerButton @click="deleteRecord" class="ml-3">Eliminar</DangerButton>
            </template>
        </DialogModal>

    </AppLayout>
</template>
