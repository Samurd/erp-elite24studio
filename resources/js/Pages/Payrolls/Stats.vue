<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Bar, Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js'
import { ref, computed } from 'vue'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement)

const props = defineProps({
    statusLabels: Array,
    statusData: Array,
    genderLabels: Array,
    genderData: Array,
    deductionsData: Array,
    recentPayrolls: Array,
    recentTaxes: Array,
    filters: Object,
})

const yearStats = ref(props.filters.yearStats)
const yearTaxes = ref(props.filters.yearTaxes)
const yearPayrolls = ref(props.filters.yearPayrolls)
const yearDeductions = ref(props.filters.yearDeductions)

const years = computed(() => {
    const currentYear = new Date().getFullYear()
    return Array.from({ length: 6 }, (_, i) => currentYear - i)
})

const monthLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']

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

const updateStats = () => {
    router.get(route('finances.payrolls.stats'), {
        yearStats: yearStats.value,
        yearTaxes: yearTaxes.value,
        yearPayrolls: yearPayrolls.value,
        yearDeductions: yearDeductions.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

// Chart configurations
const statusChartData = computed(() => ({
    labels: props.statusLabels,
    datasets: [{
        label: 'Nóminas por Estado',
        data: props.statusData,
        backgroundColor: ['rgba(59, 130, 246, 0.7)', 'rgba(34, 197, 94, 0.7)', 'rgba(251, 146, 60, 0.7)'],
    }]
}))

const genderChartData = computed(() => ({
    labels: props.genderLabels,
    datasets: [{
        label: 'Nóminas por Género',
        data: props.genderData,
        backgroundColor: ['rgba(236, 72, 153, 0.7)', 'rgba(59, 130, 246, 0.7)'],
    }]
}))

const deductionsChartData = computed(() => ({
    labels: monthLabels,
    datasets: [{
        label: 'Deducciones Mensuales',
        data: props.deductionsData,
        backgroundColor: 'rgba(239, 68, 68, 0.7)',
    }]
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top',
        }
    }
}

const barChartOptions = {
    ...chartOptions,
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: function(value) {
                    return '$' + new Intl.NumberFormat('es-CO').format(value)
                }
            }
        }
    }
}
</script>

<template>
    <AppLayout title="Estadísticas de Nóminas">
        <div class="p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Estadísticas de Nóminas</h1>
                        <p class="text-gray-600 mt-1">Análisis y métricas de nóminas</p>
                    </div>
                    <Link
                        :href="route('finances.payrolls.index')"
                        class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>Volver a Nóminas
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Status Chart -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Nóminas por Estado</h3>
                        <select v-model="yearStats" @change="updateStats" class="px-3 py-1 border rounded-lg text-sm">
                            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                        </select>
                    </div>
                    <div class="h-64">
                        <Doughnut :data="statusChartData" :options="chartOptions" />
                    </div>
                </div>

                <!-- Gender Chart -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Nóminas por Género</h3>
                        <span class="text-sm text-gray-500">Año: {{ yearStats }}</span>
                    </div>
                    <div class="h-64">
                        <Doughnut :data="genderChartData" :options="chartOptions" />
                    </div>
                </div>

                <!-- Deductions Chart -->
                <div class="bg-white rounded-lg shadow-sm p-6 lg:col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Deducciones Mensuales</h3>
                        <select v-model="yearDeductions" @change="updateStats" class="px-3 py-1 border rounded-lg text-sm">
                            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                        </select>
                    </div>
                    <div class="h-80">
                        <Bar :data="deductionsChartData" :options="barChartOptions" />
                    </div>
                </div>

                <!-- Recent Payrolls -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Control Salarial Reciente</h3>
                        <select v-model="yearPayrolls" @change="updateStats" class="px-3 py-1 border rounded-lg text-sm">
                            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                        </select>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="border-b">
                                <tr>
                                    <th class="text-left py-2">Empleado</th>
                                    <th class="text-center py-2">Total</th>
                                    <th class="text-center py-2">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="payroll in recentPayrolls" :key="payroll.id" class="border-b hover:bg-gray-50">
                                    <td class="py-2">{{ payroll.employee?.full_name || 'Sin empleado' }}</td>
                                    <td class="text-center py-2 font-semibold">{{ formatMoney(payroll.total) }}</td>
                                    <td class="text-center py-2">
                                        <span v-if="payroll.status" class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                            {{ payroll.status.name }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!recentPayrolls || recentPayrolls.length === 0">
                                    <td colspan="3" class="text-center text-gray-500 py-4">No hay datos</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Taxes -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Impuestos Laborales Recientes</h3>
                        <select v-model="yearTaxes" @change="updateStats" class="px-3 py-1 border rounded-lg text-sm">
                            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                        </select>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="border-b">
                                <tr>
                                    <th class="text-left py-2">Entidad</th>
                                    <th class="text-center py-2">Monto</th>
                                    <th class="text-center py-2">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="tax in recentTaxes" :key="tax.id" class="border-b hover:bg-gray-50">
                                    <td class="py-2">{{ tax.entity }}</td>
                                    <td class="text-center py-2 font-semibold text-red-600">{{ formatMoney(tax.amount) }}</td>
                                    <td class="text-center py-2">{{ formatDate(tax.date) }}</td>
                                </tr>
                                <tr v-if="!recentTaxes || recentTaxes.length === 0">
                                    <td colspan="3" class="text-center text-gray-500 py-4">No hay datos</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
