<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { debounce } from 'lodash'

const props = defineProps({
    interviews: Object,
    statusOptions: Array,
    interviewTypeOptions: Array,
    resultOptions: Array,
    interviewerOptions: Array,
    filters: Object,
})

const search = ref(props.filters.search || '')
const status_filter = ref(props.filters.status_filter || '')
const date_from = ref(props.filters.date_from || '')
const date_to = ref(props.filters.date_to || '')

const updateParams = debounce(() => {
    router.get(route('rrhh.interviews.index'), {
        search: search.value,
        status_filter: status_filter.value,
        date_from: date_from.value,
        date_to: date_to.value,
    }, { preserveState: true, replace: true })
}, 300)

watch([search, status_filter, date_from, date_to], updateParams)

const deleteInterview = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta entrevista?')) {
        router.delete(route('rrhh.interviews.destroy', id))
    }
}

const formatDateTime = (date, time) => {
    if (!date) return '-'
    let str = new Date(date).toLocaleDateString()
    if (time) str += ' ' + time.substring(0, 5)
    return str
}
</script>

<template>
    <AppLayout title="Entrevistas">
        <div class="max-w-7xl mx-auto p-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Entrevistas</h1>
                    <p class="text-gray-600 mt-1">Gestión y seguimiento de entrevistas</p>
                </div>
                <div class="flex gap-2">
                     <Link
                        :href="route('rrhh.interviews.calendar')"
                        class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center shadow-sm"
                    >
                        <i class="fas fa-calendar-alt mr-2"></i> Calendario
                    </Link>
                    <Link
                        :href="route('rrhh.interviews.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center shadow-sm"
                    >
                        <i class="fas fa-plus mr-2"></i> Agendar Entrevista
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar por postulante o email..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                />
                <select
                    v-model="status_filter"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                >
                    <option value="">Todos los estados</option>
                    <option v-for="status in statusOptions" :key="status.id" :value="status.id">{{ status.name }}</option>
                </select>
                <input
                    v-model="date_from"
                    type="date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    placeholder="Desde"
                />
                <input
                    v-model="date_to"
                    type="date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    placeholder="Hasta"
                />
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Postulante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha y Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entrevistador</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo / Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resultado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="interview in interviews.data" :key="interview.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ interview.applicant?.full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ interview.applicant?.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDateTime(interview.date, interview.time) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ interview.interviewer?.name || 'Sin asignar' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="text-xs text-gray-500">{{ interview.interview_type?.name }}</div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 w-fit">
                                            {{ interview.status?.name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                     <span v-if="interview.result" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ interview.result.name }}
                                    </span>
                                    <span v-else class="text-gray-400 italic">Pendiente</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="route('rrhh.interviews.edit', interview.id)" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button @click="deleteInterview(interview.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!interviews.data.length">
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron entrevistas.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200">
                    <Pagination :links="interviews.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
