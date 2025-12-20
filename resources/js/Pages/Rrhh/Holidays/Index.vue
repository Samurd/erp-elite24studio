<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { debounce } from 'lodash'

const props = defineProps({
    holidays: Object,
    typeOptions: Array,
    statusOptions: Array,
    yearOptions: Array,
    filters: Object,
})

const search = ref(props.filters.search || '')
const type_filter = ref(props.filters.type_filter || '')
const status_filter = ref(props.filters.status_filter || '')
const year = ref(props.filters.year || new Date().getFullYear())

const updateParams = debounce(() => {
    router.get(route('rrhh.holidays.index'), {
        search: search.value,
        type_filter: type_filter.value,
        status_filter: status_filter.value,
        year: year.value,
    }, { preserveState: true, replace: true })
}, 300)

watch([search, type_filter, status_filter, year], updateParams)

const deleteHoliday = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
        router.delete(route('rrhh.holidays.destroy', id))
    }
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString()
}

const getDuration = (start, end) => {
    if (!start || !end) return '-'
    const s = new Date(start)
    const e = new Date(end)
    const diffTime = Math.abs(e - s)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1 
    return diffDays + ' días'
}
</script>

<template>
    <AppLayout title="Festivos y Vacaciones">
        <div class="max-w-7xl mx-auto p-6">
             <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Festivos y Vacaciones</h1>
                    <p class="text-gray-600 mt-1">Gestión de ausencias, permisos y vacaciones</p>
                </div>
                <Link
                    :href="route('rrhh.holidays.create')"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center shadow-sm"
                >
                    <i class="fas fa-plus mr-2"></i> Nueva Solicitud
                </Link>
            </div>

            <!-- Filters -->
             <div class="bg-white rounded-lg shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar empleado..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                />
                 <select
                    v-model="type_filter"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                >
                    <option value="">Tipo Ausencia</option>
                    <option v-for="type in typeOptions" :key="type.id" :value="type.id">{{ type.name }}</option>
                </select>
                <select
                    v-model="status_filter"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                >
                    <option value="">Estado</option>
                    <option v-for="status in statusOptions" :key="status.id" :value="status.id">{{ status.name }}</option>
                </select>
                 <select
                    v-model="year"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                >
                    <option v-for="y in yearOptions" :key="y" :value="y">{{ y }}</option>
                </select>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in holidays.data" :key="item.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                     <div class="text-sm font-medium text-gray-900">{{ item.employee?.full_name }}</div>
                                     <div class="text-xs text-gray-500">{{ item.employee?.document_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="item.type" class="px-2 py-1 text-xs font-medium bg-gray-100 rounded text-gray-700">
                                        {{ item.type.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ formatDate(item.start_date) }}</div>
                                    <div class="text-xs text-gray-400">a</div>
                                    <div>{{ formatDate(item.end_date) }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ getDuration(item.start_date, item.end_date) }}
                                </td>
                                <td class="px-6 py-4">
                                     <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ item.status?.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="route('rrhh.holidays.show', item.id)" class="text-blue-600 hover:text-blue-900 mr-3" title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <Link :href="route('rrhh.holidays.edit', item.id)" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button @click="deleteHoliday(item.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!holidays.data.length">
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron registros.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                 <div class="p-4 border-t border-gray-200">
                    <Pagination :links="holidays.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
