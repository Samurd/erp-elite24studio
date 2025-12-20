<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const props = defineProps({
    invoices: Object,
    statusOptions: Array,
    clientContacts: Array,
    filters: Object,
})

const search = ref(props.filters.search || '')
const statusFilter = ref(props.filters.status_filter || '')
const contactFilter = ref(props.filters.contact_filter || '')
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
    router.get(route('finances.invoices.clients.billing-accounts.index'), {
        search: search.value,
        status_filter: statusFilter.value,
        contact_filter: contactFilter.value,
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
    statusFilter.value = ''
    contactFilter.value = ''
    dateFrom.value = ''
    dateTo.value = ''
    router.get(route('finances.invoices.clients.billing-accounts.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    })
}
</script>

<template>
    <AppLayout title="Cuentas de Cobro de ELITE">
        <div class="p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Cuentas de Cobro de ELITE</h1>
                        <p class="text-gray-600 mt-1">Cuentas de cobro emitidas por ELITE 24 STUDIO</p>
                    </div>
                    <Link
                        :href="route('finances.invoices.clients.billing-accounts.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors"
                    >
                        <i class="fas fa-plus mr-2"></i>Nueva Cuenta de Cobro
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Búsqueda -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                        <input
                            v-model="search"
                            @keyup.enter="applyFilters"
                            type="text"
                            placeholder="Código o cliente..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
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

                    <!-- Cliente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
                        <select
                            v-model="contactFilter"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        >
                            <option value="">Todos</option>
                            <option v-for="contact in clientContacts" :key="contact.id" :value="contact.id">
                                {{ contact.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Registros por página -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Por página</label>
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

                    <!-- Fecha desde -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                        <input
                            v-model="dateFrom"
                            @change="applyFilters"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                    </div>

                    <!-- Fecha hasta -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                        <input
                            v-model="dateTo"
                            @change="applyFilters"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                    </div>

                    <!-- Botón limpiar -->
                    <div class="flex items-end md:col-span-2">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="invoice in invoices.data" :key="invoice.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ invoice.code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ invoice.contact?.name || '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="invoice.status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ invoice.status.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ formatMoney(invoice.total_amount || 0) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ formatDate(invoice.invoice_date) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <Link
                                        :href="route('finances.invoices.clients.billing-accounts.show', invoice.id)"
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        <i class="fa-solid fa-eye mr-1"></i> Ver
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="!invoices.data || invoices.data.length === 0">
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron cuentas de cobro
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="invoices.links && invoices.data.length > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Mostrando <span class="font-medium">{{ invoices.from }}</span> a
                                <span class="font-medium">{{ invoices.to }}</span> de
                                <span class="font-medium">{{ invoices.total }}</span> resultados
                            </p>
                        </div>
                        <div class="flex gap-1">
                            <template v-for="(link, index) in invoices.links" :key="index">
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
