<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const props = defineProps({
    worksite: Object,
    users: Array,
    punchItems: Object,
    changes: Object,
    visits: Object,
    
    // Options
    punchStatusOptions: Array,
    changeTypeOptions: Array,
    changeStatusOptions: Array,
    visitStatusOptions: Array,
    
    // Filters applied
    punchFilters: Object,
    changeFilters: Object,
    visitFilters: Object,
})

const activeTab = ref('info')

const deleteWorksite = () => {
    if (confirm('¿Estás seguro de que deseas eliminar esta obra?')) {
        router.delete(route('worksites.destroy', props.worksite.id))
    }
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('es-CO')
}

const getStatusColor = (statusName) => {
    if (!statusName) return 'bg-gray-100 text-gray-800'
    statusName = statusName.toLowerCase()
    if (statusName.includes('activo') || statusName.includes('activa') || statusName.includes('aprobado') || statusName.includes('ejecutada') || statusName.includes('cerrado')) return 'bg-green-100 text-green-800'
    if (statusName.includes('proceso') || statusName.includes('progreso') || statusName.includes('abierto')) return 'bg-yellow-100 text-yellow-800'
    if (statusName.includes('pendiente')) return 'bg-orange-100 text-orange-800'
    if (statusName.includes('rechazado') || statusName.includes('cancelado')) return 'bg-red-100 text-red-800'
    return 'bg-gray-100 text-gray-800'
}

// --- Punch Items Logic ---
const deletePunchItem = (id) => {
    if (confirm('¿Eliminar Punch Item?')) {
        router.delete(route('worksites.punch-items.destroy', id), { preserveScroll: true })
    }
}
// Note: Filters for sub-lists would ideally be separate components or handled via props update. 
// For V1, we will just link to the main show page with updated query params.
// We can emit events or simply use links.

// --- Changes Logic ---
const deleteChange = (id) => {
    if (confirm('¿Eliminar Cambio?')) {
         router.delete(route('worksites.changes.destroy', [props.worksite.id, id]), { preserveScroll: true })
    }
}

// --- Visits Logic ---
const deleteVisit = (id) => {
    if (confirm('¿Eliminar Visita?')) {
        router.delete(route('worksites.visits.destroy', id), { preserveScroll: true })
    }
}

</script>

<template>
    <AppLayout title="Detalle de Obra">
        <div class="p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Detalle de Obra</h1>
                        <p class="text-gray-600 mt-1">{{ worksite.name }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <Link
                            :href="route('worksites.index')"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </Link>
                        <Link
                            :href="route('worksites.edit', worksite.id)"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <i class="fas fa-edit mr-2"></i>Editar
                        </Link>
                        <button
                            @click="deleteWorksite"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
                        >
                            <i class="fas fa-trash mr-2"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button 
                            @click="activeTab = 'info'" 
                            :class="[
                                activeTab === 'info' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors'
                            ]"
                        >
                            <i class="fas fa-info-circle mr-2"></i>Información
                        </button>
                        <button 
                            @click="activeTab = 'punchlist'" 
                            :class="[
                                activeTab === 'punchlist' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors'
                            ]"
                        >
                            <i class="fas fa-list-check mr-2"></i>Punch Items
                        </button>
                        <button 
                            @click="activeTab = 'changes'" 
                            :class="[
                                activeTab === 'changes' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors'
                            ]"
                        >
                            <i class="fas fa-exchange-alt mr-2"></i>Cambios
                        </button>
                        <button 
                            @click="activeTab = 'visits'" 
                            :class="[
                                activeTab === 'visits' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors'
                            ]"
                        >
                            <i class="fas fa-hard-hat mr-2"></i>Visitas de Obra
                        </button>
                    </nav>
                </div>

                <!-- Tab: Información -->
                <div v-if="activeTab === 'info'" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Proyecto</label>
                            <p class="text-lg text-gray-900">{{ worksite.project?.name || '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nombre de Obra</label>
                            <p class="text-lg text-gray-900">{{ worksite.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tipo</label>
                            <p class="text-lg text-gray-900">{{ worksite.type?.name || '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                            <p class="text-lg text-gray-900">
                                <span v-if="worksite.status" :class="['px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full', getStatusColor(worksite.status.name)]">
                                    {{ worksite.status.name }}
                                </span>
                            </p>
                        </div>
                        <div>
                             <label class="block text-sm font-medium text-gray-500 mb-1">Responsable</label>
                             <p class="text-lg text-gray-900">{{ worksite.responsible?.name || '-' }}</p>
                        </div>
                         <div class="lg:col-span-3">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Dirección</label>
                            <p class="text-lg text-gray-900">{{ worksite.address || '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Fecha Inicio</label>
                            <p class="text-lg text-gray-900">{{ formatDate(worksite.start_date) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Fecha Fin</label>
                            <p class="text-lg text-gray-900">{{ formatDate(worksite.end_date) }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Tab: Punch Items -->
                <div v-if="activeTab === 'punchlist'" class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Punch Items</h3>
                        <Link :href="route('worksites.punch-items.create', worksite.id)" class="bg-yellow-600 text-white px-3 py-2 rounded hover:bg-yellow-700 text-sm">
                            <i class="fas fa-plus"></i> Nuevo Punch Item
                        </Link>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Observación</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsable</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="item in punchItems.data" :key="item.id">
                                    <td class="px-6 py-4">{{ item.observations }}</td>
                                    <td class="px-6 py-4">
                                        <span v-if="item.status" :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColor(item.status.name)]">
                                            {{ item.status.name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ item.responsible?.name || '-' }}</td>
                                    <td class="px-6 py-4 flex space-x-2">
                                        <Link :href="route('worksites.punch-items.edit', [worksite.id, item.id])" class="text-blue-600 hover:text-blue-900"><i class="fas fa-edit"></i></Link>
                                        <button @click="deletePunchItem(item.id)" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr v-if="!punchItems.data.length"><td colspan="4" class="text-center py-4 text-gray-500">No hay registros.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: Cambios -->
                <div v-if="activeTab === 'changes'" class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Cambios de Obra</h3>
                        <Link :href="route('worksites.changes.create', worksite.id)" class="bg-yellow-600 text-white px-3 py-2 rounded hover:bg-yellow-700 text-sm">
                            <i class="fas fa-plus"></i> Nuevo Cambio
                        </Link>
                    </div>
                     <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Impacto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="change in changes.data" :key="change.id">
                                    <td class="px-6 py-4">
                                         <Link :href="route('worksites.changes.show', [worksite.id, change.id])" class="text-blue-600 hover:underline">
                                            {{ change.description && change.description.length > 50 ? change.description.substring(0, 50) + '...' : change.description }}
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4">{{ change.type?.name || '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span v-if="change.status" :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColor(change.status.name)]">
                                            {{ change.status.name }}
                                        </span>
                                    </td>
                                     <td class="px-6 py-4">{{ change.budget_impact?.name || '-' }}</td>
                                    <td class="px-6 py-4 flex space-x-2">
                                        <Link :href="route('worksites.changes.edit', [worksite.id, change.id])" class="text-blue-600 hover:text-blue-900"><i class="fas fa-edit"></i></Link>
                                        <button @click="deleteChange(change.id)" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr v-if="!changes.data.length"><td colspan="5" class="text-center py-4 text-gray-500">No hay registros.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: Visitas -->
                <div v-if="activeTab === 'visits'" class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Visitas de Obra</h3>
                        <Link :href="route('worksites.visits.create', worksite.id)" class="bg-yellow-600 text-white px-3 py-2 rounded hover:bg-yellow-700 text-sm">
                            <i class="fas fa-plus"></i> Nueva Visita
                        </Link>
                    </div>
                     <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visitante</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="visit in visits.data" :key="visit.id">
                                    <td class="px-6 py-4">{{ formatDate(visit.visit_date) }}</td>
                                    <td class="px-6 py-4">{{ visit.visitor?.name || '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span v-if="visit.status" :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColor(visit.status.name)]">
                                            {{ visit.status.name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 flex space-x-2">
                                        <Link :href="route('worksites.visits.edit', [worksite.id, visit.id])" class="text-blue-600 hover:text-blue-900"><i class="fas fa-edit"></i></Link>
                                        <button @click="deleteVisit(visit.id)" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr v-if="!visits.data.length"><td colspan="4" class="text-center py-4 text-gray-500">No hay registros.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
