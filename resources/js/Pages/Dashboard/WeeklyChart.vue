<script setup>
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
)

const props = defineProps({
    data: Object
});

const chartData = {
  labels: props.data.labels,
  datasets: [
    {
      label: 'Ingresos',
      backgroundColor: '#f87979',
      borderColor: '#c3933b',
      data: props.data.ingresos
    },
    {
      label: 'Gastos',
      backgroundColor: '#000000',
      borderColor: '#000000',
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
      text: 'Evolución semanal del ingreso neto'
    },
    legend: { display: false }
  },
  scales: {
    y: { beginAtZero: true }
  }
}
</script>

<template>
  <div class="w-full">
    <div class="flex justify-between flex-wrap items-center gap-2 mb-4">
        <h3 class="font-bold">Ingreso Neto Semanal por Trimestre</h3>
    </div>
    <div class="bg-white rounded-md p-4 h-64">
        <Line :data="chartData" :options="chartOptions" />
    </div>
  </div>
</template>
