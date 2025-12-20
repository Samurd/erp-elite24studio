<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { debounce } from 'lodash'

const props = defineProps({
    vacancies: Object,
    applicants: Object,
    contractTypes: Array,
    vacancyStatuses: Array,
    applicantStatuses: Array,
    vacanciesForSidebar: Array,
    totalApplicants: Number,
    filters: Object,
})

const activeTab = ref(props.filters.activeTab || 'vacancies')

// Vacancy Filters
const vacancySearch = ref(props.filters.vacancySearch || '')
const contractTypeFilter = ref(props.filters.contractTypeFilter || '')
const statusFilter = ref(props.filters.statusFilter || '')

// Applicant Filters
const applicantSearch = ref(props.filters.applicantSearch || '')
const applicantStatusFilter = ref(props.filters.applicantStatusFilter || '')
const vacancyFilter = ref(props.filters.vacancyFilter || '')

// Watchers
const updateParams = debounce(() => {
    router.get(route('rrhh.vacancies.index'), {
        activeTab: activeTab.value,
        vacancySearch: vacancySearch.value,
        contractTypeFilter: contractTypeFilter.value,
        statusFilter: statusFilter.value,
        applicantSearch: applicantSearch.value,
        applicantStatusFilter: applicantStatusFilter.value,
        vacancyFilter: vacancyFilter.value,
    }, { preserveState: true, replace: true })
}, 300)

watch([activeTab, vacancySearch, contractTypeFilter, statusFilter, applicantSearch, applicantStatusFilter, vacancyFilter], updateParams)

const deleteVacancy = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta vacante?')) {
        router.delete(route('rrhh.vacancies.destroy', id))
    }
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString()
}
</script>

<template>
    <AppLayout title="Vacantes y Selección">
        <div class="max-w-7xl mx-auto p-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Vacantes y Selección</h1>
                    <p class="text-gray-600 mt-1">Gestión de oportunidades y postulantes</p>
                </div>
                <!-- Conditional Action Button based on Tab -->
                <div v-if="activeTab === 'vacancies'">
                     <Link
                        :href="route('rrhh.vacancies.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center"
                    >
                        <i class="fas fa-plus mr-2"></i> Nueva Vacante
                    </Link>
                </div>
                <div v-else>
                     <Link
                        :href="route('rrhh.applicants.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center"
                    >
                        <i class="fas fa-plus mr-2"></i> Nuevo Postulante
                    </Link>
                </div>
            </div>

            <!-- Tabs Navigation -->
             <div class="mb-6 border-b border-gray-200 bg-white rounded-t-lg px-4 pt-4">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button 
                        @click="activeTab = 'vacancies'" 
                        :class="[activeTab === 'vacancies' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center']"
                    >
                        <i class="fas fa-briefcase mr-2"></i> Vacantes
                    </button>
                    <button 
                        @click="activeTab = 'applicants'" 
                        :class="[activeTab === 'applicants' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center']"
                    >
                         <i class="fas fa-users mr-2"></i> Postulantes
                         <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2.5 rounded-full text-xs font-medium">{{ totalApplicants }}</span>
                    </button>
                </nav>
            </div>

            <!-- Content Area -->
            <div class="flex flex-col lg:flex-row gap-6">
                
                <!-- Sidebar (Visible only on larger screens or maybe collapsible) -->
                <div class="w-full lg:w-1/4 bg-white rounded-lg shadow-sm p-4 h-fit">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Vacantes Activas</h3>
                    <div class="space-y-2">
                         <button 
                            @click="vacancyFilter = ''"
                             :class="['w-full text-left px-3 py-2 rounded-md text-sm transition-colors', !vacancyFilter ? 'bg-yellow-50 text-yellow-700 font-medium' : 'text-gray-600 hover:bg-gray-50']"
                         >
                            Todas las vacantes
                         </button>
                         <button 
                            v-for="v in vacanciesForSidebar" 
                            :key="v.id"
                            @click="vacancyFilter = v.id; activeTab = 'applicants'" 
                            :class="['w-full text-left px-3 py-2 rounded-md text-sm transition-colors flex justify-between items-center', vacancyFilter == v.id ? 'bg-yellow-50 text-yellow-700 font-medium' : 'text-gray-600 hover:bg-gray-50']"
                        >
                            <span class="truncate">{{ v.title }}</span>
                            <span class="bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs ml-2">{{ v.applicants_count }}</span>
                         </button>
                    </div>
                </div>

                <!-- Main Listing -->
                <div class="w-full lg:w-3/4">
                    
                    <!-- Vacancies Tab -->
                    <div v-if="activeTab === 'vacancies'">
                        <!-- Filters -->
                         <div class="bg-white rounded-lg shadow-sm p-4 mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <input
                                v-model="vacancySearch"
                                type="text"
                                placeholder="Buscar vacante..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                             <select
                                v-model="contractTypeFilter"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Todos los contratos</option>
                                <option v-for="type in contractTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                            </select>
                            <select
                                v-model="statusFilter"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Todos los estados</option>
                                <option v-for="status in vacancyStatuses" :key="status.id" :value="status.id">{{ status.name }}</option>
                            </select>
                        </div>

                        <!-- Table -->
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vacante</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contrato / Estado</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Publicación</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Postulantes</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="vacancy in vacancies.data" :key="vacancy.id" class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ vacancy.title }}</div>
                                                <div class="text-sm text-gray-500">{{ vacancy.area }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                 <div class="text-sm text-gray-900">{{ vacancy.contract_type?.name }}</div>
                                                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    {{ vacancy.status?.name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ formatDate(vacancy.published_at) }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                <div class="flex items-center">
                                                    <i class="fas fa-users text-gray-400 mr-2"></i>
                                                    {{ vacancy.applicants_count }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <Link :href="route('rrhh.vacancies.edit', vacancy.id)" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </Link>
                                                <button @click="deleteVacancy(vacancy.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                         <tr v-if="!vacancies.data.length">
                                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No se encontraron vacantes.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                             <div class="p-4 border-t border-gray-200">
                                <Pagination :links="vacancies.links" />
                            </div>
                        </div>
                    </div>

                    <!-- Applicants Tab -->
                    <div v-else>
                         <!-- Filters -->
                         <div class="bg-white rounded-lg shadow-sm p-4 mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input
                                v-model="applicantSearch"
                                type="text"
                                placeholder="Buscar postulante..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <select
                                v-model="applicantStatusFilter"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Todos los estados</option>
                                <option v-for="status in applicantStatuses" :key="status.id" :value="status.id">{{ status.name }}</option>
                            </select>
                        </div>
                        
                        <!-- Filter Badge if Vacancy selected -->
                        <div v-if="vacancyFilter" class="mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                Filtrado por vacante
                                <button @click="vacancyFilter = ''" class="ml-2 focus:outline-none">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        </div>

                         <!-- Table -->
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Postulante</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vacante</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="applicant in applicants.data" :key="applicant.id" class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ applicant.full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ applicant.email }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ applicant.vacancy?.title }}</div>
                                            </td>
                                             <td class="px-6 py-4">
                                                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ applicant.status?.name }}
                                                </span>
                                            </td>
                                             <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ formatDate(applicant.created_at) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                 <Link :href="route('rrhh.applicants.edit', applicant.id)" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </Link>
                                            </td>
                                        </tr>
                                         <tr v-if="!applicants.data.length">
                                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No se encontraron postulantes.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                             <div class="p-4 border-t border-gray-200">
                                <Pagination :links="applicants.links" />
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
