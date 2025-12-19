<script setup>
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

const props = defineProps({
    data: Object
});

const chartData = {
  labels: props.data.labels,
  datasets: [
    {
      label: 'Ingresos',
      backgroundColor: '#c3933b',
      data: props.data.ingresos
    },
    {
      label: 'Gastos',
      backgroundColor: '#000000',
      data: props.data.gastos
    }
  ]
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    title: {
      display: true,
      text: 'Comparación mensual: Ingresos vs Gastos',
      color: '#ffffff'
    },
    legend: {
      labels: {
        color: '#ffffff'
      }
    }
  },
  scales: {
    x: {
      ticks: { color: '#ffffff' },
      grid: { color: 'rgba(255, 255, 255, 0.1)' }
    },
    y: {
      beginAtZero: true,
      ticks: { color: '#ffffff' },
      grid: { color: 'rgba(255, 255, 255, 0.1)' }
    }
  }
}
</script>

<template>
  <div class="bg-[#252525] rounded-md p-4 w-full md:h-[455px]">
    <div class="flex justify-between items-center mb-2">
      <h4 class="text-white font-semibold text-sm">Ingresos vs Gastos Mensuales</h4>
      <!-- Selects placeholder for visual fidelity -->
    </div>
    <div class="w-full h-[90%]">
      <Bar :data="chartData" :options="chartOptions" />
    </div>
  </div>
</template>
