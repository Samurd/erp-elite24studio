<script setup>
import {
    Chart as ChartJS,
    ArcElement,
    Tooltip,
    Legend
} from 'chart.js'
import { Doughnut } from 'vue-chartjs'
import { computed } from 'vue'

ChartJS.register(ArcElement, Tooltip, Legend)

const props = defineProps({
    labels: {
        type: Array,
        default: () => []
    },
    data: {
        type: Array,
        default: () => []
    },
    colors: {
        type: Array,
        default: () => []
    }
})

const hasData = computed(() => props.data && props.data.length > 0)

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [{
        data: props.data,
        backgroundColor: props.colors
    }]
}))

const chartOptions = {
    responsive: false,
    maintainAspectRatio: false,
    cutout: '70%',
    plugins: {
        legend: { display: false },
        tooltip: { 
            enabled: true,
            callbacks: {
                label: (context) => {
                    const label = context.label || ''
                    const value = context.raw || 0
                    return `${label}: ${value}%`
                }
            }
        }
    }
}
</script>

<template>
    <div class="flex justify-center items-center">
        <template v-if="hasData">
            <Doughnut :data="chartData" :options="chartOptions" :width="150" :height="150" />
        </template>
        <template v-else>
            <div class="text-center text-gray-500 py-8">
                <p>No hay datos disponibles</p>
            </div>
        </template>
    </div>
</template>
