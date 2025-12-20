<script setup>
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, watch } from 'vue'
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js'
import { Bar, Doughnut } from 'vue-chartjs'

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    ArcElement,
    Title,
    Tooltip,
    Legend
)

const props = defineProps({
    totalIncome: Number,
    incomeByType: Object,
    growthPercentage: Number,
    previousPeriodIncome: Number,
    chartMonthlyData: Array,
    chartCategoryLabels: Array,
    chartCategoryData: Array,
    chartAreaLabels: Array,
    chartAreaData: Array,
    chartComparisonCurrent: Array,
    chartComparisonLast: Array,
    stackedChartData: Array,
    filters: Object,
})

const year = ref(props.filters.year)
const month = ref(props.filters.month)
const monthlyChartYear = ref(props.filters.monthlyChartYear)
const categoryChartMonth = ref(props.filters.categoryChartMonth)
const comparisonChartYear = ref(props.filters.comparisonChartYear)

const years = Array.from({ length: new Date().getFullYear() - 2019 }, (_, i) => new Date().getFullYear() - i)
const months = [
    { value: '', label: 'Todo el año' },
    { value: 1, label: 'Enero' },
    { value: 2, label: 'Febrero' },
    { value: 3, label: 'Marzo' },
    { value: 4, label: 'Abril' },
    { value: 5, label: 'Mayo' },
    { value: 6, label: 'Junio' },
    { value: 7, label: 'Julio' },
    { value: 8, label: 'Agosto' },
    { value: 9, label: 'Septiembre' },
    { value: 10, label: 'Octubre' },
    { value: 11, label: 'Noviembre' },
    { value: 12, label: 'Diciembre' },
]

const formatMoney = (value) => {
    return new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value)
}

const updateFilters = (filterType) => {
    const params = {
        year: year.value,
        month: month.value,
        monthlyChartYear: monthlyChartYear.value,
        categoryChartMonth: categoryChartMonth.value,
        comparisonChartYear: comparisonChartYear.value,
    }
    
    router.get(route('finances.net.index'), params, {
        preserveState: true,
        preserveScroll: true,
    })
}

// Chart configurations
const monthLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']

const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { labels: { color: '#94a3b8' } }
    },
    scales: {
        y: { grid: { color: '#334155' }, ticks: { color: '#94a3b8' } },
        x: { grid: { color: '#334155' }, ticks: { color: '#94a3b8' } }
    }
}

const noGridOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { labels: { color: '#94a3b8' } } },
    scales: {
        y: { display: false },
        x: { display: false }
    }
}

// Growth Chart Data
const growthChartData = {
    labels: ['Periodo Anterior', 'Periodo Actual'],
    datasets: [{
        data: [props.previousPeriodIncome, props.totalIncome],
        backgroundColor: ['#475569', '#3b82f6'],
        borderWidth: 0
    }]
}

const growthChartOptions = {
    ...noGridOptions,
    cutout: '70%',
    plugins: {
        legend: { position: 'bottom', labels: { color: '#94a3b8' } }
    }
}

// Monthly Chart Data
const monthlyChartData = {
    labels: monthLabels,
    datasets: [{
        label: 'Ingreso Neto',
        data: props.chartMonthlyData,
        backgroundColor: '#fce7f3',
        borderRadius: 4
    }]
}

// Category Chart Data
const categoryChartData = {
    labels: props.chartCategoryLabels,
    datasets: [{
        label: 'Ingreso',
        data: props.chartCategoryData,
        backgroundColor: '#818cf8',
        borderRadius: 4
    }]
}

const categoryChartOptions = {
    ...commonOptions,
    indexAxis: 'y',
}

// Area Chart Data
const areaChartData = {
    labels: props.chartAreaLabels,
    datasets: [{
        data: props.chartAreaData,
        backgroundColor: ['#818cf8', '#c084fc', '#f472b6', '#fb7185', '#38bdf8'],
        borderWidth: 0
    }]
}

const areaChartOptions = {
    ...noGridOptions,
    cutout: '60%',
    plugins: {
        legend: { position: 'right', labels: { color: '#94a3b8' } }
    }
}

// Comparison Chart Data
const comparisonChartData = {
    labels: monthLabels,
    datasets: [
        {
            label: 'Año Actual',
            data: props.chartComparisonCurrent,
            backgroundColor: '#38bdf8',
            order: 1
        },
        {
            label: 'Año Anterior',
            data: props.chartComparisonLast,
            backgroundColor: '#475569',
            order: 2
        }
    ]
}

// Stacked Chart Data
const stackedChartData = {
    labels: monthLabels,
    datasets: props.stackedChartData
}

const stackedChartOptions = {
    ...commonOptions,
    scales: {
        x: { stacked: true, grid: { color: '#334155' }, ticks: { color: '#94a3b8' } },
        y: { stacked: true, grid: { color: '#334155' }, ticks: { color: '#94a3b8' } }
    }
}
</script>

<template>
    <AppLayout title="Finanzas Netas">
        <div class="p-6 space-y-6 bg-slate-900 text-white min-h-screen">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Finanzas Netas</h1>
                <div class="flex gap-2">
                    <select v-model="year" @change="updateFilters('global')" class="bg-slate-800 border-slate-700 rounded-lg text-sm px-3 py-2">
                        <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                    </select>
                    <select v-model="month" @change="updateFilters('global')" class="bg-slate-800 border-slate-700 rounded-lg text-sm px-3 py-2">
                        <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                    </select>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Total Income -->
                <div class="bg-slate-800 p-4 rounded-xl border border-slate-700">
                    <div class="text-slate-400 text-sm mb-1">INGRESO NETO EMPRESARIAL</div>
                    <div class="text-2xl font-bold">${{ formatMoney(totalIncome) }}</div>
                </div>

                <!-- Dynamic KPI Cards based on Types -->
                <div v-for="(amount, type) in incomeByType" :key="type" class="bg-slate-800 p-4 rounded-xl border border-slate-700">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-slate-400 text-sm mb-1">{{ type }}</div>
                            <div class="text-2xl font-bold">${{ formatMoney(amount) }}</div>
                        </div>
                        <div class="p-2 bg-slate-700 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Growth Chart -->
                <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex flex-col">
                    <h3 class="text-lg font-semibold mb-4">Porcentaje de Crecimiento</h3>
                    <div class="flex-1 relative" style="min-height: 200px;">
                        <Doughnut :data="growthChartData" :options="growthChartOptions" />
                    </div>
                    <div class="mt-4 flex justify-between text-sm text-slate-400">
                        <span>Periodo Anterior: {{ formatMoney(previousPeriodIncome) }}</span>
                        <span>Actual: {{ formatMoney(totalIncome) }}</span>
                    </div>
                </div>

                <!-- Monthly Income Chart -->
                <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex flex-col col-span-1 md:col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Ingreso Neto por Mes-Año</h3>
                        <select v-model="monthlyChartYear" @change="updateFilters('monthly')" class="bg-slate-700 border-none text-xs rounded px-2 py-1">
                            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                        </select>
                    </div>
                    <div class="flex-1 relative" style="min-height: 250px;">
                        <Bar :data="monthlyChartData" :options="commonOptions" />
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Category Chart -->
                <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex flex-col col-span-1 md:col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Ingreso Neto por Categoría</h3>
                        <select v-model="categoryChartMonth" @change="updateFilters('category')" class="bg-slate-700 border-none text-xs rounded px-2 py-1">
                            <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                        </select>
                    </div>
                    <div class="flex-1 relative" style="min-height: 250px;">
                        <Bar :data="categoryChartData" :options="categoryChartOptions" />
                    </div>
                </div>

                <!-- Area Donut Chart -->
                <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex flex-col">
                    <h3 class="text-lg font-semibold mb-4">Ingreso Neto por Áreas</h3>
                    <div class="flex-1 relative" style="min-height: 200px;">
                        <Doughnut :data="areaChartData" :options="areaChartOptions" />
                    </div>
                </div>
            </div>

            <!-- Charts Row 3 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Comparison Chart -->
                <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex flex-col">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Comparativa Anual</h3>
                        <select v-model="comparisonChartYear" @change="updateFilters('comparison')" class="bg-slate-700 border-none text-xs rounded px-2 py-1">
                            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                        </select>
                    </div>
                    <div class="flex-1 relative" style="min-height: 250px;">
                        <Bar :data="comparisonChartData" :options="commonOptions" />
                    </div>
                </div>

                <!-- Stacked Category Chart -->
                <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex flex-col">
                    <h3 class="text-lg font-semibold mb-4">Categoría por Mes</h3>
                    <div class="flex-1 relative" style="min-height: 250px;">
                        <Bar :data="stackedChartData" :options="stackedChartOptions" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
