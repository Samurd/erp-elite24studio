<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { debounce } from 'lodash'

const props = defineProps({
    contracts: Object,
    statusOptions: Array,
    filters: Object,
})

const search = ref(props.filters.search || '')
const status_id = ref(props.filters.status_id || '')

watch([search, status_id], debounce(() => {
    router.get(route('rrhh.contracts.index'), {
        search: search.value,
        status_id: status_id.value,
    }, { preserveState: true, replace: true })
}, 300))

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('es-CO')
}

const getStatusColor = (statusName) => {
    if (!statusName) return 'bg-gray-100 text-gray-800'
    const name = statusName.toLowerCase()
    if (name.includes('activo') || name.includes('vigente')) return 'bg-green-100 text-green-800'
    if (name.includes('inactivo') || name.includes('terminado') || name.includes('finalizado')) return 'bg-red-100 text-red-800'
    return 'bg-gray-100 text-gray-800'
}

const deleteContract = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este contrato?')) {
        router.delete(route('rrhh.contracts.destroy', id))
    }
}
</script>

<template>
    <AppLayout title="Contratos">
        <div class="max-w-7xl mx-auto p-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Contratos</h1>
                    <p class="text-gray-600 mt-1">Gestión de contratos de empleados</p>
                </div>
                <Link
                    :href="route('rrhh.contracts.create')"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center"
                >
                    <i class="fas fa-plus mr-2"></i> Nuevo Contrato
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar por nombre o identificación..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    />
                </div>
                <div>
                     <select
                        v-model="status_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    >
                        <option value="">Todos los estados</option>
                        <option v-for="status in statusOptions" :key="status.id" :value="status.id">
                            {{ status.name }}
                        </option>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inicio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fin</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="contract in contracts.data" :key="contract.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ contract.employee?.full_name || 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ contract.employee?.identification_number || '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ contract.type?.name || '-' }}
                                    <div class="text-xs text-gray-400">{{ contract.category?.name || '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="contract.status" :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColor(contract.status.name)]">
                                        {{ contract.status.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(contract.start_date) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(contract.end_date) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-3">
                                    <Link :href="route('rrhh.contracts.show', contract.id)" class="text-blue-600 hover:text-blue-900" title="Ver Detalle">
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <Link :href="route('rrhh.contracts.edit', contract.id)" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button @click="deleteContract(contract.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!contracts.data.length">
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron contratos.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200">
                     <Pagination :links="contracts.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
