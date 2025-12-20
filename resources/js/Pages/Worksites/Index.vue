<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const props = defineProps({
    worksites: Object,
    typeOptions: Array,
    statusOptions: Array,
    projectOptions: Array, // Stores list of projects
    responsibleOptions: Array,
    filters: Object,
})

const search = ref(props.filters.search || '')
const typeFilter = ref(props.filters.type_filter || '')
const statusFilter = ref(props.filters.status_filter || '')
const projectFilter = ref(props.filters.project_filter || '')
const responsibleFilter = ref(props.filters.responsible_filter || '')
const dateFrom = ref(props.filters.date_from || '')
const dateTo = ref(props.filters.date_to || '')
const perPage = ref(props.filters.perPage || 10)

const applyFilters = () => {
    router.get(route('worksites.index'), {
        search: search.value,
        type_filter: typeFilter.value,
        status_filter: statusFilter.value,
        project_filter: projectFilter.value,
        responsible_filter: responsibleFilter.value,
        date_from: dateFrom.value,
        date_to: dateTo.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const clearFilters = () => {
    search.value = ''
    typeFilter.value = ''
    statusFilter.value = ''
    projectFilter.value = ''
    responsibleFilter.value = ''
    dateFrom.value = ''
    dateTo.value = ''
    router.get(route('worksites.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    })
}

const deleteWorksite = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta obra?')) {
        router.delete(route('worksites.destroy', id))
    }
}

// Helper for styling
const getStatusColor = (statusName) => {
    if (!statusName) return 'bg-gray-100 text-gray-800'
    statusName = statusName.toLowerCase()
    if (statusName.includes('activa') || statusName.includes('activo')) return 'bg-green-100 text-green-800'
    if (statusName.includes('proceso') || statusName.includes('progreso')) return 'bg-yellow-100 text-yellow-800'
    if (statusName.includes('finalizada') || statusName.includes('completado')) return 'bg-blue-100 text-blue-800'
    if (statusName.includes('detenida') || statusName.includes('pausado')) return 'bg-orange-100 text-orange-800'
    if (statusName.includes('cancelada')) return 'bg-red-100 text-red-800'
    return 'bg-gray-100 text-gray-800'
}

const formatDate = (date) => {
    if (!date) return '-'
    // Assuming backend sends YYYY-MM-DD or standard SQL datetime
    return new Date(date).toLocaleDateString('es-CO')
}
</script>

<template>
    <AppLayout title="Obras">
        <div class="p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Obras</h1>
                        <p class="text-gray-600 mt-1">Gestión de obras y proyectos</p>
                    </div>
                    <Link
                        :href="route('worksites.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors"
                    >
                        <i class="fas fa-plus mr-2"></i>Nueva Obra
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Búsqueda -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                        <input
                            v-model="search"
                            @keyup.enter="applyFilters"
                            type="text"
                            placeholder="Nombre, dirección..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                    </div>

                    <!-- Proyecto -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Proyecto</label>
                        <select
                            v-model="projectFilter"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        >
                            <option value="">Todos</option>
                            <option v-for="project in projectOptions" :key="project.id" :value="project.id">
                                {{ project.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Tipo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select
                            v-model="typeFilter"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        >
                            <option value="">Todos</option>
                            <option v-for="type in typeOptions" :key="type.id" :value="type.id">
                                {{ type.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select
                            v-model="statusFilter"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        >
                            <option value="">Todos</option>
                            <option v-for="status in statusOptions" :key="status.id" :value="status.id">
                                {{ status.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Responsable -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                        <select
                            v-model="responsibleFilter"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        >
                            <option value="">Todos</option>
                            <option v-for="user in responsibleOptions" :key="user.id" :value="user.id">
                                {{ user.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Fecha desde (Inicio) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio (Desde)</label>
                        <input
                            v-model="dateFrom"
                            @change="applyFilters"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                    </div>

                    <!-- Fecha hasta (Fin) -->
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin (Hasta)</label>
                        <input
                            v-model="dateTo"
                            @change="applyFilters"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                    </div>
                </div>
                 <!-- Botón limpiar -->
                <div class="flex justify-end mt-4">
                    <button
                        @click="clearFilters"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                    >
                        <i class="fas fa-times mr-2"></i>Limpiar Filtros
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Obra</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyecto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsable</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fechas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="worksite in worksites.data" :key="worksite.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ worksite.name }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ worksite.type?.name }}
                                        <span v-if="worksite.address" class="ml-1">• {{ worksite.address }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ worksite.project?.name || '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="worksite.status" :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColor(worksite.status.name)]">
                                        {{ worksite.status.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center" v-if="worksite.responsible">
                                        <span class="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-medium mr-2">
                                            {{ worksite.responsible.name.substring(0, 2).toUpperCase() }}
                                        </span>
                                        <span class="text-sm text-gray-900">{{ worksite.responsible.name }}</span>
                                    </div>
                                    <span v-else class="text-gray-400 text-sm">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    <div v-if="worksite.start_date">
                                        <span class="font-medium">I:</span> {{ formatDate(worksite.start_date) }}
                                    </div>
                                    <div v-if="worksite.end_date">
                                        <span class="font-medium">F:</span> {{ formatDate(worksite.end_date) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <Link
                                            :href="route('worksites.show', worksite.id)"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Ver detalles"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </Link>
                                        <Link
                                            :href="route('worksites.edit', worksite.id)"
                                            class="text-yellow-600 hover:text-yellow-900"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </Link>
                                        <button
                                            @click="deleteWorksite(worksite.id)"
                                            class="text-red-600 hover:text-red-900"
                                            title="Eliminar"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!worksites.data || worksites.data.length === 0">
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron obras
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                 <!-- Pagination -->
                <div v-if="worksites.links && worksites.data.length > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                         <div>
                            <p class="text-sm text-gray-700">
                                Mostrando <span class="font-medium">{{ worksites.from }}</span> a
                                <span class="font-medium">{{ worksites.to }}</span> de
                                <span class="font-medium">{{ worksites.total }}</span> resultados
                            </p>
                        </div>
                         <div class="flex gap-1">
                            <template v-for="(link, index) in worksites.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="['px-3 py-1 border rounded', link.active ? 'bg-yellow-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100']"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    :class="['px-3 py-1 border rounded bg-gray-100 text-gray-400 cursor-not-allowed']"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
