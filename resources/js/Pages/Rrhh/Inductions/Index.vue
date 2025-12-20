<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { debounce } from 'lodash'

const props = defineProps({
    inductions: Object,
    typeBondOptions: Array,
    statusOptions: Array,
    confirmationOptions: Array,
    employees: Array,
    responsibles: Array,
    filters: Object,
})

const search = ref(props.filters.search || '')
const employee_filter = ref(props.filters.employee_filter || '')
const type_bond_filter = ref(props.filters.type_bond_filter || '')
const status_filter = ref(props.filters.status_filter || '')
const confirmation_filter = ref(props.filters.confirmation_filter || '')
const responsible_filter = ref(props.filters.responsible_filter || '')
const date_from = ref(props.filters.date_from || '')
const date_to = ref(props.filters.date_to || '')

const updateParams = debounce(() => {
    router.get(route('rrhh.inductions.index'), {
        search: search.value,
        employee_filter: employee_filter.value,
        type_bond_filter: type_bond_filter.value,
        status_filter: status_filter.value,
        confirmation_filter: confirmation_filter.value,
        responsible_filter: responsible_filter.value,
        date_from: date_from.value,
        date_to: date_to.value,
    }, { preserveState: true, replace: true })
}, 300)

watch([search, employee_filter, type_bond_filter, status_filter, confirmation_filter, responsible_filter, date_from, date_to], updateParams)

const deleteInduction = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta inducción?')) {
        router.delete(route('rrhh.inductions.destroy', id))
    }
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString()
}
</script>

<template>
    <AppLayout title="Inducciones">
        <div class="max-w-7xl mx-auto p-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Inducciones</h1>
                    <p class="text-gray-600 mt-1">Gestión de procesos de inducción</p>
                </div>
                <Link
                    :href="route('rrhh.inductions.create')"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center shadow-sm"
                >
                    <i class="fas fa-plus mr-2"></i> Nueva Inducción
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                     <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar por observaciones o empleado..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    />
                    <select
                        v-model="employee_filter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    >
                        <option value="">Todos los empleados</option>
                        <option v-for="employee in employees" :key="employee.id" :value="employee.id">{{ employee.full_name }}</option>
                    </select>
                     <select
                        v-model="status_filter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    >
                        <option value="">Todos los estados</option>
                        <option v-for="status in statusOptions" :key="status.id" :value="status.id">{{ status.name }}</option>
                    </select>
                     <select
                        v-model="confirmation_filter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    >
                        <option value="">Todas las confirmaciones</option>
                        <option v-for="confirmation in confirmationOptions" :key="confirmation.id" :value="confirmation.id">{{ confirmation.name }}</option>
                    </select>
                </div>
                 <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                     <select
                        v-model="type_bond_filter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    >
                        <option value="">Todos los vínculos</option>
                        <option v-for="type in typeBondOptions" :key="type.id" :value="type.id">{{ type.name }}</option>
                    </select>
                    <select
                        v-model="responsible_filter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    >
                        <option value="">Todos los responsables</option>
                        <option v-for="user in responsibles" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                 </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado / Confirmación</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="induction in inductions.data" :key="induction.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ induction.employee?.full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ induction.type_bond?.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>Ingreso: {{ formatDate(induction.entry_date) }}</div>
                                    <div>Inducción: {{ formatDate(induction.date) }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ induction.responsible?.name || 'Sin asignar' }}
                                </td>
                                <td class="px-6 py-4">
                                     <div class="flex flex-col gap-1">
                                         <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 w-fit">
                                            {{ induction.status?.name }}
                                        </span>
                                        <span v-if="induction.confirmation" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 w-fit">
                                            {{ induction.confirmation.name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ induction.duration ? induction.duration.substring(0, 5) + ' hs' : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="route('rrhh.inductions.edit', induction.id)" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button @click="deleteInduction(induction.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!inductions.data.length">
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron inducciones.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200">
                    <Pagination :links="inductions.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
