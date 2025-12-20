<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { debounce } from 'lodash'

const props = defineProps({
    offboardings: Object,
    statusOptions: Array,
    filters: Object,
})

const search = ref(props.filters.search || '')
const status_filter = ref(props.filters.status_filter || '')
const date_from = ref(props.filters.date_from || '')
const date_to = ref(props.filters.date_to || '')

const updateParams = debounce(() => {
    router.get(route('rrhh.offboardings.index'), {
        search: search.value,
        status_filter: status_filter.value,
        date_from: date_from.value,
        date_to: date_to.value,
    }, { preserveState: true, replace: true })
}, 300)

watch([search, status_filter, date_from, date_to], updateParams)

const deleteOffboarding = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este proceso de offboarding?')) {
        router.delete(route('rrhh.offboardings.destroy', id))
    }
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString()
}
</script>

<template>
    <AppLayout title="OffBoardings">
        <div class="max-w-7xl mx-auto p-6">
             <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">OffBoardings</h1>
                    <p class="text-gray-600 mt-1">Gestión de procesos de salida de empleados</p>
                </div>
                <Link
                    :href="route('rrhh.offboardings.create')"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center shadow-sm"
                >
                    <i class="fas fa-plus mr-2"></i> Nuevo Proceso
                </Link>
            </div>

            <!-- Filters -->
             <div class="bg-white rounded-lg shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar por empleado o proyecto..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                />
                <select
                    v-model="status_filter"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                >
                    <option value="">Todos los estados</option>
                    <option v-for="status in statusOptions" :key="status.id" :value="status.id">{{ status.name }}</option>
                </select>
                 <input
                    v-model="date_from"
                    type="date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    placeholder="Desde"
                />
                <input
                    v-model="date_to"
                    type="date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    placeholder="Hasta"
                />
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto / Razón</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Salida</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in offboardings.data" :key="item.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                     <div class="text-sm font-medium text-gray-900">{{ item.employee?.full_name }}</div>
                                     <div class="text-xs text-gray-500">{{ item.employee?.document_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ item.project?.name || 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-xs">{{ item.reason }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(item.exit_date) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ item.responsible?.name || 'Sin asignar' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        {{ item.status?.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="route('rrhh.offboardings.show', item.id)" class="text-blue-600 hover:text-blue-900 mr-3" title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <Link :href="route('rrhh.offboardings.edit', item.id)" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button @click="deleteOffboarding(item.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!offboardings.data.length">
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron procesos de offboarding.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                 <div class="p-4 border-t border-gray-200">
                    <Pagination :links="offboardings.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
