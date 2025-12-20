<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { debounce } from 'lodash'

const props = defineProps({
    kits: Object,
    statusOptions: Array,
    filters: Object,
})

const search = ref(props.filters.search || '')
const status_filter = ref(props.filters.status_filter || '')
const date_from = ref(props.filters.date_from || '')
const date_to = ref(props.filters.date_to || '')

const updateParams = debounce(() => {
    router.get(route('rrhh.kits.index'), {
        search: search.value,
        status_filter: status_filter.value,
        date_from: date_from.value,
        date_to: date_to.value,
    }, { preserveState: true, replace: true })
}, 300)

watch([search, status_filter, date_from, date_to], updateParams)

const deleteKit = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este kit?')) {
        router.delete(route('rrhh.kits.destroy', id))
    }
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString()
}
</script>

<template>
    <AppLayout title="Kits">
        <div class="max-w-7xl mx-auto p-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Kits y Dotaciones</h1>
                    <p class="text-gray-600 mt-1">Gestión de entrega de kits y materiales</p>
                </div>
                <Link
                    :href="route('rrhh.kits.create')"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center shadow-sm"
                >
                    <i class="fas fa-plus mr-2"></i> Nuevo Kit
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar por destinatario, cargo o área..."
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destinatario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalles Kit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsables</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="kit in kits.data" :key="kit.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ kit.recipient_name }}</div>
                                    <div class="text-xs text-gray-500">{{ kit.recipient_role }}</div>
                                    <div class="text-xs text-gray-400">{{ kit.position_area }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ kit.kit_type }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-xs">{{ kit.kit_contents }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>Sol: {{ formatDate(kit.request_date) }}</div>
                                    <div v-if="kit.delivery_date">Ent: {{ formatDate(kit.delivery_date) }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="text-xs">Solicitó: {{ kit.requested_by_user?.name }}</div>
                                    <div class="text-xs" v-if="kit.delivery_responsible_user">Entregó: {{ kit.delivery_responsible_user.name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                     <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ kit.status?.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="route('rrhh.kits.edit', kit.id)" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button @click="deleteKit(kit.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!kits.data.length">
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron kits.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200">
                    <Pagination :links="kits.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
