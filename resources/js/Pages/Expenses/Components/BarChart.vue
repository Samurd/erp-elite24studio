<script setup>
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
)

const props = defineProps({
    labels: {
        type: Array,
        required: true
    },
    data: {
        type: Array,
        required: true
    },
    label: {
        type: String,
        default: 'Data'
    },
    backgroundColor: {
        type: String,
        default: 'rgba(236,72,153,0.7)'
    }
})

const formatMoney = (value) => {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 2
    }).format(value)
}

const chartData = {
    labels: props.labels,
    datasets: [{
        label: props.label,
        data: props.data,
        backgroundColor: props.backgroundColor,
        borderRadius: 6,
        borderSkipped: false
    }]
}

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: { color: '#fff' }
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                color: '#fff',
                callback: function (value) {
                    return formatMoney(value)
                }
            },
            grid: { color: 'rgba(255,255,255,0.1)' }
        },
        x: {
            ticks: { color: '#fff' },
            grid: { color: 'rgba(255,255,255,0.1)' }
        }
    }
}
</script>

<template>
    <div class="w-full h-full">
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>
