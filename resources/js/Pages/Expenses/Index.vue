<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import BarChart from './Components/BarChart.vue'
import { ref, computed } from 'vue'

const props = defineProps({
    expenses: Object,
    search: String,
    totals: Object,
    chartData: Object,
})

const searchQuery = ref(props.search || '')
const yearChart1 = ref(props.chartData.yearChart1)
const monthChart2 = ref(props.chartData.monthChart2)
const yearTable = ref(props.chartData.yearTable)

const formatMoney = (amount, showSymbol = true) => {
    const formatted = new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount / 100)
    return showSymbol ? `$${formatted}` : formatted
}

const monthLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']

const chart1Data = computed(() => Object.values(props.chartData.expenseByMonth))
const chart2Labels = computed(() => Object.keys(props.chartData.expenseByCategory))
const chart2Data = computed(() => Object.values(props.chartData.expenseByCategory))

const years = computed(() => {
    const currentYear = new Date().getFullYear()
    return Array.from({ length: 6 }, (_, i) => currentYear - i)
})

const handleSearch = () => {
    router.get(route('finances.expenses.index'), { search: searchQuery.value }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const updateChart1 = () => {
    router.get(route('finances.expenses.index'), {
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
    router.get(route('finances.expenses.index'), {
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
    router.get(route('finances.expenses.index'), {
        search: searchQuery.value,
        yearChart1: yearChart1.value,
        monthChart2: monthChart2.value,
        yearTable: yearTable.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const deleteExpense = (id) => {
    if (confirm('¿Estás seguro de eliminar este gasto?')) {
        router.delete(route('finances.expenses.destroy', id))
    }
}
</script>

<template>
    <AppLayout title="Costos y Gastos">
        <div class="p-4">
            <div>
                <h4 class="text-xl font-semibold">Costos y Gastos</h4>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-[1fr_2fr_1fr] gap-2 p-6">
                <!-- Columna Izquierda: Totals -->
                <div class="space-y-4">
                    <!-- Tarjeta 1 -->
                    <div class="bg-[#252525] text-white p-4 rounded-2xl shadow-md">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="w-3 h-3 rounded-sm bg-gradient-to-r from-purple-500 to-pink-400"></span>
                            <h3 class="text-sm font-semibold">Costos Directos</h3>
                        </div>
                        <p class="text-xs text-gray-300 mb-3">
                            Materiales usados, Pagos de contratistas, Costos de Equipos
                        </p>
                        <p class="text-3xl font-bold mb-2 flex gap-2">
                            <span class="text-sm">$</span>
                            {{ formatMoney(totals.totalDirect, false) }}
                        </p>
                    </div>

                    <!-- Tarjeta 2 -->
                    <div class="bg-[#252525] text-white p-4 rounded-2xl shadow-md">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="w-3 h-3 rounded-sm bg-gradient-to-r from-purple-500 to-pink-400"></span>
                            <h3 class="text-sm font-semibold">Gastos Indirectos</h3>
                        </div>
                        <p class="text-xs text-gray-300 mb-3">
                            Sueldos Administrativos, Servicios públicos, transporte, Oficinas, Viáticos.
                        </p>
                        <p class="text-3xl font-bold mb-2 flex gap-2">
                            <span class="text-sm">$</span>
                            {{ formatMoney(totals.totalIndirect, false) }}
                        </p>
                    </div>

                    <!-- Tarjeta 3 -->
                    <div class="bg-[#252525] text-white p-4 rounded-2xl shadow-md">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="w-3 h-3 rounded-sm bg-gradient-to-r from-purple-500 to-pink-400"></span>
                            <h3 class="text-sm font-semibold">Impuestos</h3>
                        </div>
                        <p class="text-xs text-gray-300 mb-3">
                            IVA, Retenciones en la Fuente, Renta, Seguridad Social, Prestaciones
                        </p>
                        <p class="text-3xl font-bold mb-2 flex gap-2">
                            <span class="text-sm">$</span>
                            {{ formatMoney(totals.totalTaxes, false) }}
                        </p>
                    </div>

                    <!-- Tarjeta 4 -->
                    <div class="bg-[#252525] text-white p-4 rounded-2xl shadow-md">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="w-3 h-3 rounded-sm bg-gradient-to-r from-purple-500 to-pink-400"></span>
                            <h3 class="text-sm font-semibold">Gastos Financieros</h3>
                        </div>
                        <p class="text-xs text-gray-300 mb-3">
                            Pagos o entidades por créditos, Comisiones Bancarias, Mantenimiento de cuentas
                        </p>
                        <p class="text-3xl font-bold mb-2 flex gap-2">
                            <span class="text-sm">$</span>
                            {{ formatMoney(totals.totalFinance, false) }}
                        </p>
                    </div>
                </div>

                <!-- Columna Medio: Charts -->
                <div class="space-y-6">
                    <!-- Chart 1: Gastos por Mes-Año -->
                    <div class="bg-[#252525] text-white p-4 rounded-xl shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-sm font-semibold">Gastos por Mes–Año</h3>
                            <select v-model="yearChart1" @change="updateChart1" class="bg-gray-800 text-sm rounded-md px-2 py-1">
                                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                            </select>
                        </div>
                        <div class="max-h-[14rem]">
                            <BarChart
                                :labels="monthLabels"
                                :data="chart1Data"
                                label="Gastos por mes"
                                backgroundColor="rgba(236,72,153,0.7)"
                            />
                        </div>
                    </div>

                    <!-- Chart 2: Gastos por Categoría (Mes) -->
                    <div class="bg-[#252525] text-white p-4 rounded-xl shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-sm font-semibold">Gastos por Categoría (Mes)</h3>
                            <select v-model="monthChart2" @change="updateChart2" class="bg-gray-800 text-sm rounded-md px-2 py-1">
                                <option v-for="(m, i) in monthNames" :key="i" :value="i + 1">{{ m }}</option>
                            </select>
                        </div>
                        <div class="max-h-[14rem]">
                            <BarChart
                                :labels="chart2Labels"
                                :data="chart2Data"
                                label="Gastos por categoría"
                                backgroundColor="rgba(216,180,254,0.7)"
                            />
                        </div>
                    </div>

                    <!-- Tabla TOP 5 -->
                    <div class="bg-[#252525] text-white p-4 rounded-xl shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-sm font-semibold">TOP 5 Proveedores con mayores transacciones.</h3>
                            <select v-model="yearTable" @change="updateTable" class="bg-gray-800 text-sm rounded-md px-2 py-1">
                                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                            </select>
                        </div>
                        <table class="w-full text-xs">
                            <thead class="border-b border-gray-700 text-gray-400 uppercase">
                                <tr>
                                    <th class="p-2 text-left">N.Proyecto</th>
                                    <th class="p-2 text-left">Proyecto</th>
                                    <th class="p-2 text-center">Monto</th>
                                    <th class="p-2 text-center">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in chartData.topProjects" :key="item.id" class="border-b border-gray-800 hover:bg-gray-800/50">
                                    <td class="p-2">{{ item.name }}</td>
                                    <td class="p-2">{{ item.category?.name || 'Sin categoría' }}</td>
                                    <td class="p-2 text-red-400 text-center">{{ formatMoney(item.amount) }}</td>
                                    <td class="p-2 text-center">{{ new Date(item.date).toLocaleDateString('es-CO') }}</td>
                                </tr>
                                <tr v-if="!chartData.topProjects || chartData.topProjects.length === 0">
                                    <td colspan="4" class="text-center text-gray-500 py-2">No hay datos</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Columna Derecha: Tabla de Gastos -->
                <div class="bg-white rounded-2xl shadow-lg p-6 max-h-[50rem]">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
                        <h3 class="text-xl font-bold">Gastos Transacción</h3>

                        <div class="flex items-center gap-2 w-full md:w-auto">
                            <div class="relative w-full md:w-64">
                                <input
                                    v-model="searchQuery"
                                    @keyup.enter="handleSearch"
                                    type="text"
                                    placeholder="Buscar gastos..."
                                    class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:border-yellow-500"
                                />
                                <div class="absolute left-3 top-2.5 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>

                            <Link :href="route('finances.expenses.create')" class="px-4 py-2 bg-yellow-700 hover:bg-yellow-800 text-white rounded-md whitespace-nowrap">
                                Nuevo Gasto
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
                                <tr v-for="expense in expenses.data" :key="expense.id" class="hover:bg-gray-50">
                                    <td class="p-2">
                                        {{ expense.name }}
                                        <span v-if="expense.description" class="block text-xs text-gray-500">{{ expense.description }}</span>
                                    </td>
                                    <td class="p-2 text-center">{{ expense.date }}</td>
                                    <td class="p-2 text-center text-red-600 font-semibold">
                                        {{ formatMoney(expense.amount) }}
                                    </td>
                                    <td class="p-2 text-center">
                                        <span class="bg-gray-100 text-xs px-2 py-1 rounded">{{ expense.result?.name }}</span>
                                    </td>
                                    <td class="p-2 text-center">
                                        <Link :href="route('finances.expenses.edit', expense.id)" class="text-yellow-600 hover:text-yellow-800 font-semibold text-xs mr-2">
                                            Editar
                                        </Link>
                                        <button @click="deleteExpense(expense.id)" class="text-red-600 hover:text-red-800 font-semibold text-xs">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!expenses.data || expenses.data.length === 0">
                                    <td colspan="5" class="text-center text-gray-500 py-4">No hay gastos</td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-if="expenses.links" class="mt-2 flex justify-center gap-1">
                            <template v-for="(link, index) in expenses.links" :key="index">
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
