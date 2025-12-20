<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { debounce } from 'lodash'

const props = defineProps({
    view: String, // 'daily' or 'consolidated'
    attendances: Object, // For daily view
    consolidated: Array, // For consolidated view
    statusTags: Array, // For consolidated columns
    employees: Array,
    statusOptions: Array,
    modalityOptions: Array,
    filters: Object,
    currentMonth: [String, Number],
    currentYear: [String, Number],
})

// Common Filters
const currentView = ref(props.view || 'daily')
const search = ref(props.filters.search || '')
const employee_filter = ref(props.filters.employee_filter || '')
const status_filter = ref(props.filters.status_filter || '')
const modality_filter = ref(props.filters.modality_filter || '')
const date_from = ref(props.filters.date_from || '')
const date_to = ref(props.filters.date_to || '')

// Consolidated Filters
const month = ref(props.currentMonth || new Date().getMonth() + 1)
const year = ref(props.currentYear || new Date().getFullYear())

const updateParams = debounce(() => {
    const params = {
        view: currentView.value,
    }

    if (currentView.value === 'daily') {
        params.search = search.value
        params.employee_filter = employee_filter.value
        params.status_filter = status_filter.value
        params.modality_filter = modality_filter.value
        params.date_from = date_from.value
        params.date_to = date_to.value
    } else {
        params.month = month.value
        params.year = year.value
    }

    router.get(route('rrhh.attendances.index'), params, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
    })
}, 300)

watch([currentView, search, employee_filter, status_filter, modality_filter, date_from, date_to, month, year], updateParams)

const deleteAttendance = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este registro de asistencia?')) {
        router.delete(route('rrhh.attendances.destroy', id))
    }
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString()
}

// Helpers for formatted display
const getMonthName = (m) => {
    const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    return months[parseInt(m) - 1] || '';
}

const years = Array.from({length: 6}, (_, i) => new Date().getFullYear() - 4 + i) // Last 4 years + next year
</script>

<template>
    <AppLayout title="Asistencias">
        <div class="max-w-7xl mx-auto p-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Control de Asistencias</h1>
                    <p class="text-gray-600 mt-1">Gestión de entradas, salidas y novedades</p>
                </div>
                <div class="flex items-center gap-3">
                     <!-- View Toggle -->
                     <div class="bg-gray-100 p-1 rounded-lg flex text-sm">
                        <button
                            @click="currentView = 'daily'"
                            :class="{'bg-white shadow-sm text-gray-900': currentView === 'daily', 'text-gray-500 hover:text-gray-700': currentView !== 'daily'}"
                            class="px-3 py-1.5 rounded-md transition-all font-medium"
                        >
                            Vista Diaria
                        </button>
                         <button
                            @click="currentView = 'consolidated'"
                            :class="{'bg-white shadow-sm text-gray-900': currentView === 'consolidated', 'text-gray-500 hover:text-gray-700': currentView !== 'consolidated'}"
                            class="px-3 py-1.5 rounded-md transition-all font-medium"
                        >
                            Vista Mensual
                        </button>
                    </div>

                    <Link
                        :href="route('rrhh.attendances.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center shadow-sm"
                    >
                        <i class="fas fa-plus mr-2"></i> Nuevo Registro
                    </Link>
                </div>
            </div>

            <!-- DAILY VIEW -->
            <div v-if="currentView === 'daily'">
                <!-- Filters -->
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                     <div class="md:col-span-1">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Buscar empleado..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                    </div>
                     <div class="md:col-span-3 grid grid-cols-2 md:grid-cols-4 gap-4">
                        <select
                            v-model="status_filter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        >
                            <option value="">Estado</option>
                            <option v-for="status in statusOptions" :key="status.id" :value="status.id">{{ status.name }}</option>
                        </select>
                         <select
                            v-model="modality_filter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        >
                            <option value="">Modalidad</option>
                            <option v-for="modality in modalityOptions" :key="modality.id" :value="modality.id">{{ modality.name }}</option>
                        </select>
                        <input
                            v-model="date_from"
                            type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                         <input
                            v-model="date_to"
                            type="date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                     </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalles</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="item in attendances.data" :key="item.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(item.date) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ item.employee?.full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ item.employee?.document_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div><span class="font-medium">Entrada:</span> {{ item.check_in?.substring(0, 5) }}</div>
                                        <div><span class="font-medium">Salida:</span> {{ item.check_out?.substring(0, 5) }}</div>
                                    </td>
                                     <td class="px-6 py-4 text-sm text-gray-500">
                                        <div v-if="item.modality" class="text-xs mb-1">
                                            <span class="bg-gray-100 px-1.5 py-0.5 rounded">{{ item.modality.name }}</span>
                                        </div>
                                        <div class="truncate max-w-xs text-xs">{{ item.observations }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            {{ item.status?.name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('rrhh.attendances.edit', item.id)" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </Link>
                                        <button @click="deleteAttendance(item.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!attendances.data.length">
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron registros.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-200">
                        <Pagination :links="attendances.links" />
                    </div>
                </div>
            </div>

             <!-- CONSOLIDATED VIEW -->
            <div v-else>
                 <!-- Consolidated Filters -->
                 <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex gap-4 items-center">
                    <select
                        v-model="month"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    >
                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                    </select>
                     <select
                        v-model="year"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    >
                        <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                    </select>

                    <div class="ml-auto text-sm text-gray-500">
                        Mostrando resumen de {{ getMonthName(month) }} {{ year }}
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10 w-24">Fecha</th>
                                    <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Total Emp.</th>
                                    <th v-for="tag in statusTags" :key="tag.id" class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">
                                        {{ tag.name }}
                                    </th>
                                     <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Notas</th>
                                </tr>
                            </thead>
                             <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(day, index) in consolidated" :key="index" :class="{'bg-gray-50': day.special_day}">
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-900 font-medium sticky left-0 z-10" :class="{'bg-gray-50': day.special_day, 'bg-white': !day.special_day}">
                                        {{ day.date }} <span class="text-xs text-gray-500 ml-1">({{ day.day_name.substring(0,3) }})</span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-500">
                                        {{ day.total_employees }}
                                    </td>
                                     <td v-for="tag in statusTags" :key="tag.id" class="px-4 py-3 text-center font-medium">
                                        <span v-if="day.status_counts[tag.id] > 0" class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                            {{ day.status_counts[tag.id] }}
                                        </span>
                                        <span v-else class="text-gray-300">-</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 italic">
                                        {{ day.special_day }}
                                    </td>
                                </tr>
                             </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </AppLayout>
</template>
