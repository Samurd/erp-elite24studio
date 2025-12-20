<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const props = defineProps({
    taxes: Object,
    typeOptions: Array,
    statusOptions: Array,
    filters: Object,
})

const search = ref(props.filters.search || '')
const typeFilter = ref(props.filters.type_filter || '')
const statusFilter = ref(props.filters.status_filter || '')
const dateFrom = ref(props.filters.date_from || '')
const dateTo = ref(props.filters.date_to || '')
const perPage = ref(props.filters.perPage || 10)

const formatMoney = (amount) => {
    const formatted = new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount / 100)
    return `$${formatted}`
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('es-CO')
}

const applyFilters = () => {
    router.get(route('finances.taxes.index'), {
        search: search.value,
        type_filter: typeFilter.value,
        status_filter: statusFilter.value,
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
    dateFrom.value = ''
    dateTo.value = ''
    router.get(route('finances.taxes.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    })
}

const deleteTax = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este impuesto?')) {
        router.delete(route('finances.taxes.destroy', id))
    }
}
</script>

<template>
    <AppLayout title="Impuestos">
        <div class="p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Impuestos</h1>
                        <p class="text-gray-600 mt-1">Gestión de impuestos y retenciones</p>
                    </div>
                    <div class="flex space-x-3">
                        <Link
                            :href="route('finances.taxes.create')"
                            class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors"
                        >
                            <i class="fas fa-plus mr-2"></i>Nuevo Impuesto
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Búsqueda general -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda (Entidad)</label>
                        <input
                            v-model="search"
                            @keyup.enter="applyFilters"
                            type="text"
                            placeholder="Entidad..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                    </div>

                    <!-- Filtro por tipo -->
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

                    <!-- Filtro por estado -->
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

                    <!-- Filtro por fecha desde -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                        <input
                            v-model="dateFrom"
                            @change="applyFilters"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                    </div>

                    <!-- Filtro por fecha hasta -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                        <input
                            v-model="dateTo"
                            @change="applyFilters"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                    </div>

                    <!-- Registros por página -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Registros por página</label>
                        <select
                            v-model="perPage"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        >
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    <!-- Botón limpiar filtros -->
                    <div class="flex items-end">
                        <button
                            @click="clearFilters"
                            class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                        >
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Porcentaje</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="tax in taxes.data" :key="tax.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">#{{ tax.id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ tax.entity }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="tax.type" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ tax.type.name }}
                                    </span>
                                    <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="tax.status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ tax.status.name }}
                                    </span>
                                    <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ formatMoney(tax.base) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ tax.porcentage }}%</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ formatMoney(tax.amount) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ formatDate(tax.date) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ tax.files?.length || 0 }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <Link
                                            :href="route('finances.taxes.show', tax.id)"
                                            class="text-gray-600 hover:text-gray-900"
                                            title="Ver"
                                        >
                                            <i class="fa-solid fa-eye mr-1"></i> Ver
                                        </Link>
                                        <Link
                                            :href="route('finances.taxes.edit', tax.id)"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Editar"
                                        >
                                            <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                        </Link>
                                        <button
                                            @click="deleteTax(tax.id)"
                                            class="text-red-600 hover:text-red-900"
                                            title="Eliminar"
                                        >
                                            <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!taxes.data || taxes.data.length === 0">
                                <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron impuestos
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="taxes.links && taxes.data.length > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <template v-for="(link, index) in taxes.links" :key="index">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                :class="['relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md', link.active ? 'bg-yellow-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50']"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Mostrando
                                <span class="font-medium">{{ taxes.from }}</span>
                                a
                                <span class="font-medium">{{ taxes.to }}</span>
                                de
                                <span class="font-medium">{{ taxes.total }}</span>
                                resultados
                            </p>
                        </div>
                        <div class="flex gap-1">
                            <template v-for="(link, index) in taxes.links" :key="index">
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
