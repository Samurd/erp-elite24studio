<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue'

const props = defineProps({
    holiday: Object,
    historicalData: Object,
    stats: Object,
    filters: Object,
    yearOptions: Array,
})

const year = ref(props.filters.year || new Date().getFullYear())

watch(year, (val) => {
    router.get(route('rrhh.holidays.show', props.holiday.id), {
        year: val,
    }, { preserveState: true, replace: true, preserveScroll: true })
})

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString()
}

const getDuration = (start, end) => {
    if (!start || !end) return '-'
    const s = new Date(start)
    const e = new Date(end)
    const diffTime = Math.abs(e - s)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1 
    return diffDays
}
</script>

<template>
    <AppLayout :title="'Solicitud - ' + holiday.employee.full_name">
         <div class="max-w-6xl mx-auto p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                     <h1 class="text-2xl font-bold text-gray-800">Detalles de Solicitud</h1>
                     <p class="text-gray-600 mt-1">{{ holiday.employee.full_name }}</p>
                </div>
                <div class="flex gap-3">
                     <Link
                        :href="route('rrhh.holidays.index')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                    <Link
                        :href="route('rrhh.holidays.edit', holiday.id)"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors"
                    >
                        <i class="fas fa-pencil-alt mr-2"></i>Editar
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Info Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 md:col-span-1 h-fit">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Información Actual</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="block text-xs text-gray-500 uppercase">Empleado</span>
                            <span class="block text-gray-900 font-medium">{{ holiday.employee.full_name }}</span>
                        </div>
                         <div>
                            <span class="block text-xs text-gray-500 uppercase">Tipo</span>
                            <span class="block text-gray-900 font-medium">{{ holiday.type?.name || 'N/A' }}</span>
                        </div>
                         <div>
                            <span class="block text-xs text-gray-500 uppercase">Fechas</span>
                            <span class="block text-gray-900 font-medium">{{ formatDate(holiday.start_date) }} - {{ formatDate(holiday.end_date) }}</span>
                        </div>
                         <div>
                            <span class="block text-xs text-gray-500 uppercase">Duración</span>
                            <span class="block text-gray-900 font-medium">{{ getDuration(holiday.start_date, holiday.end_date) }} días</span>
                        </div>
                          <div>
                            <span class="block text-xs text-gray-500 uppercase">Estado</span>
                            <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ holiday.status?.name }}
                            </span>
                        </div>
                         <div>
                            <span class="block text-xs text-gray-500 uppercase">Aprobador</span>
                            <span class="block text-gray-900 font-medium">{{ holiday.approver?.name || 'Sin aprobar' }}</span>
                        </div>
                    </div>
                </div>
                
                 <!-- Files Card -->
                 <div class="bg-white rounded-lg shadow-sm p-6 md:col-span-2 h-fit">
                    <div class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Soportes Adjuntos</div>
                    <ModelAttachments 
                        :model-id="holiday.id"
                        model-type="App\Models\Holiday"
                        area-slug="rrhh"
                        :files="holiday.files" 
                    />
                 </div>

                <!-- Historical Card -->
                 <div class="bg-white rounded-lg shadow-sm p-6 md:col-span-3">
                    <div class="flex justify-between items-center mb-4 pb-2 border-b">
                         <h3 class="text-lg font-bold text-gray-800">Histórico de Ausencias</h3>
                         <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Año:</span>
                             <select
                                v-model="year"
                                class="px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-yellow-500"
                            >
                                <option v-for="y in yearOptions" :key="y" :value="y">{{ y }}</option>
                            </select>
                         </div>
                    </div>

                     <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-xs text-gray-500 uppercase">Total Días ({{ year }})</div>
                            <div class="text-2xl font-bold text-gray-900">{{ stats.totalDays }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-xs text-gray-500 uppercase">Total Solicitudes</div>
                            <div class="text-2xl font-bold text-gray-900">{{ stats.requestsInYear }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-xs text-gray-500 uppercase">Última Fecha Ausencia</div>
                            <div class="text-xl font-bold text-gray-900">{{ formatDate(stats.lastDate) }}</div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                         <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fechas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Días</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="item in historicalData.data" :key="item.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(item.start_date) }} - {{ formatDate(item.end_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                         {{ item.type?.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ getDuration(item.start_date, item.end_date) }}
                                    </td>
                                     <td class="px-6 py-4">
                                         <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ item.status?.name }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!historicalData.data.length">
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay registros para este año.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                     <div class="p-4 border-t border-gray-200">
                        <Pagination :links="historicalData.links" />
                    </div>
                 </div>
            </div>
         </div>
    </AppLayout>
</template>
