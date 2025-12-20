<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import BarChart from '../Expenses/Components/BarChart.vue'
import { ref, computed } from 'vue'

const props = defineProps({
    incomes: Object,
    search: String,
    chartData: Object,
    filters: Object,
})

const searchQuery = ref(props.search || '')
const yearChart1 = ref(props.filters.yearChart1)
const monthChart2 = ref(props.filters.monthChart2)
const yearTable = ref(props.filters.yearTable)

const formatMoney = (amount, showSymbol = true) => {
    const formatted = new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount / 100)
    return showSymbol ? `$${formatted}` : formatted
}

const monthLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']

const years = computed(() => {
    const currentYear = new Date().getFullYear()
    return Array.from({ length: 6 }, (_, i) => currentYear - i)
})

const handleSearch = () => {
    router.get(route('finances.gross.index'), { search: searchQuery.value }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const updateChart1 = () => {
    router.get(route('finances.gross.index'), {
        search: searchQuery.value,
        yearChart1: yearChart1.value,
        monthChart2: monthChart2.value,
        yearTable: yearTable.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const updateChart2 = () => {
    router.get(route('finances.gross.index'), {
        search: searchQuery.value,
        yearChart1: yearChart1.value,
        monthChart2: monthChart2.value,
        yearTable: yearTable.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const updateTable = () => {
    router.get(route('finances.gross.index'), {
        search: searchQuery.value,
        yearChart1: yearChart1.value,
        monthChart2: monthChart2.value,
        yearTable: yearTable.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const deleteIncome = (id) => {
    if (confirm('¿Estás seguro de eliminar este ingreso?')) {
        router.delete(route('finances.gross.destroy', id))
    }
}
</script>

<template>
    <AppLayout title="Ingreso Bruto">
        <div class="p-4">
            <div>
                <h4 class="text-xl font-semibold">Ingreso Bruto</h4>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                <!-- Columna Izquierda: Charts -->
                <div class="space-y-6">
                    <!-- Chart 1: Ingreso por Mes-Año -->
                    <div class="bg-[#252525] text-white p-4 rounded-xl shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-sm font-semibold">Ingreso por Mes–Año</h3>
                            <select v-model="yearChart1" @change="updateChart1" class="bg-gray-800 text-sm rounded-md px-2 py-1">
                                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                            </select>
                        </div>
                        <div class="max-h-[14rem]">
                            <BarChart
                                :labels="monthLabels"
                                :data="chartData.grossByMonth"
                                label="Ingreso por mes"
                                backgroundColor="rgba(134,239,172,0.7)"
                            />
                        </div>
                    </div>

                    <!-- Chart 2: Ingreso por Categoría (Mes) -->
                    <div class="bg-[#252525] text-white p-4 rounded-xl shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-sm font-semibold">Ingreso por Categoría (Mes)</h3>
                            <select v-model="monthChart2" @change="updateChart2" class="bg-gray-800 text-sm rounded-md px-2 py-1">
                                <option v-for="(m, i) in monthNames" :key="i" :value="i + 1">{{ m }}</option>
                            </select>
                        </div>
                        <div class="max-h-[14rem]">
                            <BarChart
                                :labels="chartData.grossByCategoryLabels"
                                :data="chartData.grossByCategoryData"
                                label="Ingreso por categoría"
                                backgroundColor="rgba(134,239,172,0.7)"
                            />
                        </div>
                    </div>

                    <!-- Tabla TOP 5 -->
                    <div class="bg-[#252525] text-white p-4 rounded-xl shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-sm font-semibold">TOP 5 Ingresos con mayores transacciones.</h3>
                            <select v-model="yearTable" @change="updateTable" class="bg-gray-800 text-sm rounded-md px-2 py-1">
                                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                            </select>
                        </div>
                        <table class="w-full text-xs">
                            <thead class="border-b border-gray-700 text-gray-400 uppercase">
                                <tr>
                                    <th class="p-2 text-left">Nombre</th>
                                    <th class="p-2 text-left">Categoría</th>
                                    <th class="p-2 text-center">Monto</th>
                                    <th class="p-2 text-center">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in chartData.topProjects" :key="item.id" class="border-b border-gray-800 hover:bg-gray-800/50">
                                    <td class="p-2">{{ item.name }}</td>
                                    <td class="p-2">{{ item.category?.name || 'Sin categoría' }}</td>
                                    <td class="p-2 text-green-400 text-center">{{ formatMoney(item.amount) }}</td>
                                    <td class="p-2 text-center">{{ new Date(item.date).toLocaleDateString('es-CO') }}</td>
                                </tr>
                                <tr v-if="!chartData.topProjects || chartData.topProjects.length === 0">
                                    <td colspan="4" class="text-center text-gray-500 py-2">No hay datos</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Columna Derecha: Tabla de Ingresos -->
                <div class="bg-white rounded-2xl shadow-lg p-6 max-h-[50rem]">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
                        <h3 class="text-xl font-bold">Ingresos Transacción</h3>

                        <div class="flex items-center gap-2 w-full md:w-auto">
                            <div class="relative w-full md:w-64">
                                <input
                                    v-model="searchQuery"
                                    @keyup.enter="handleSearch"
                                    type="text"
                                    placeholder="Buscar ingresos..."
                                    class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:border-yellow-500"
                                />
                                <div class="absolute left-3 top-2.5 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>

                            <Link :href="route('finances.gross.create')" class="px-4 py-2 bg-yellow-700 hover:bg-yellow-800 text-white rounded-md whitespace-nowrap">
                                Nuevo Ingreso
                            </Link>
                        </div>
                    </div>

                    <div class="overflow-y-auto max-h-[40rem] rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gradient-to-r from-black to-yellow-600 text-white uppercase font-semibold sticky top-0">
                                <tr>
                                    <th class="p-2 text-left">Descripción</th>
                                    <th class="p-2 text-center">Fecha</th>
                                    <th class="p-2 text-center">Monto</th>
                                    <th class="p-2 text-center">Resultado</th>
                                    <th class="p-2 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-800">
                                <tr v-for="income in incomes.data" :key="income.id" class="hover:bg-gray-50">
                                    <td class="p-2">
                                        {{ income.name }}
                                        <span v-if="income.description" class="block text-xs text-gray-500">{{ income.description }}</span>
                                    </td>
                                    <td class="p-2 text-center">{{ income.date }}</td>
                                    <td class="p-2 text-center text-green-600 font-semibold">
                                        {{ formatMoney(income.amount) }}
                                    </td>
                                    <td class="p-2 text-center">
                                        <span class="bg-gray-100 text-xs px-2 py-1 rounded">{{ income.result?.name }}</span>
                                    </td>
                                    <td class="p-2 text-center">
                                        <Link :href="route('finances.gross.edit', income.id)" class="text-yellow-600 hover:text-yellow-800 font-semibold text-xs mr-2">
                                            Editar
                                        </Link>
                                        <button @click="deleteIncome(income.id)" class="text-red-600 hover:text-red-800 font-semibold text-xs">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!incomes.data || incomes.data.length === 0">
                                    <td colspan="5" class="text-center text-gray-500 py-4">No hay ingresos</td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-if="incomes.links" class="mt-2 flex justify-center gap-1">
                            <template v-for="(link, index) in incomes.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-1 border rounded',
                                        link.active ? 'bg-yellow-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'
                                    ]"
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
