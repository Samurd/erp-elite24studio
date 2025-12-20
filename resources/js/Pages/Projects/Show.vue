<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue'
import ProjectPlanner from './Partials/ProjectPlanner.vue'
import { ref } from 'vue'

const deleteProject = () => {
    if (confirm('¿Estás seguro de que deseas eliminar este proyecto? Esta acción no se puede deshacer.')) {
        router.delete(route('projects.destroy', props.project.id))
    }
}

const props = defineProps({
    project: Object,
    selectedPlan: Object,
    buckets: Array,
    priorities: Array,
    states: Array,
    teams: Array,
})

const activeTab = ref('info')

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleString('es-CO')
}

// Helper for styling
const getStatusColor = (statusName) => {
    if (!statusName) return 'bg-gray-100 text-gray-800'
    statusName = statusName.toLowerCase()
    if (statusName.includes('activo')) return 'bg-green-100 text-green-800'
    if (statusName.includes('proceso') || statusName.includes('progreso')) return 'bg-yellow-100 text-yellow-800'
    if (statusName.includes('completado')) return 'bg-blue-100 text-blue-800'
    if (statusName.includes('pausado')) return 'bg-orange-100 text-orange-800'
    if (statusName.includes('cancelado')) return 'bg-red-100 text-red-800'
    return 'bg-gray-100 text-gray-800'
}
</script>

<template>
    <AppLayout title="Detalles del Proyecto">
        <div class="p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Detalles del Proyecto</h1>
                        <p class="text-gray-600 mt-1">{{ project.name }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <Link
                            :href="route('projects.index')"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </Link>
                        <Link
                            :href="route('projects.edit', project.id)"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <i class="fas fa-edit mr-2"></i>Editar
                        </Link>
                        <button
                            @click="deleteProject"
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
                            <i class="fas fa-info-circle mr-2"></i>Información del Proyecto
                        </button>
                        <button 
                            @click="activeTab = 'plans'" 
                            :class="[
                                activeTab === 'plans' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'py-4 px-1 border-b-2 font-medium text-sm transition-colors'
                            ]"
                        >
                            <i class="fas fa-tasks mr-2"></i>Planes ({{ project.plans?.length || 0 }})
                        </button>
                    </nav>
                </div>

                <!-- Tab: Información -->
                <div v-if="activeTab === 'info'" class="p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Información General</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">ID</label>
                            <p class="text-lg text-gray-900">#{{ project.id }}</p>
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nombre</label>
                            <p class="text-lg text-gray-900">{{ project.name }}</p>
                        </div>
                        <div class="lg:col-span-3">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Descripción</label>
                            <p class="text-lg text-gray-900">{{ project.description || 'No especificada' }}</p>
                        </div>
                        <div class="lg:col-span-3">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Dirección</label>
                            <p class="text-lg text-gray-900">{{ project.direction || 'No especificada' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Contacto/Cliente</label>
                            <p class="text-lg text-gray-900">
                                <span v-if="project.contact" class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    {{ project.contact.name }}
                                </span>
                                <span v-else class="text-gray-500">No asignado</span>
                            </p>
                        </div>

                         <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tipo de Proyecto</label>
                            <p class="text-lg text-gray-900">
                                <span v-if="project.project_type" class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ project.project_type.name }}
                                </span>
                                <span v-else class="text-gray-500">No asignado</span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                            <p class="text-lg text-gray-900">
                                <span v-if="project.status" :class="['px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full', getStatusColor(project.status.name)]">
                                    {{ project.status.name }}
                                </span>
                                <span v-else class="text-gray-500">No asignado</span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Etapa Actual</label>
                            <p class="text-lg text-gray-900">
                                <span v-if="project.current_stage" class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ project.current_stage.name }}
                                </span>
                                <span v-else class="text-gray-500">No asignada</span>
                            </p>
                        </div>

                        <div>
                             <label class="block text-sm font-medium text-gray-500 mb-1">Responsable</label>
                             <p class="text-lg text-gray-900">
                                <span v-if="project.responsible" class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-teal-100 text-teal-800">
                                    {{ project.responsible.name }}
                                </span>
                                <span v-else class="text-gray-500">No asignado</span>
                             </p>
                        </div>

                        <div>
                             <label class="block text-sm font-medium text-gray-500 mb-1">Equipo</label>
                             <p class="text-lg text-gray-900">
                                <span v-if="project.team" class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    {{ project.team.name }}
                                </span>
                                <span v-else class="text-gray-500">No asignado</span>
                             </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Creación</label>
                            <p class="text-lg text-gray-900">{{ formatDate(project.created_at) }}</p>
                        </div>
                    </div>

                    <!-- Stages List -->
                    <div v-if="project.stages && project.stages.length > 0" class="mt-8 pt-6 border-t">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Etapas del Proyecto</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="stage in project.stages" :key="stage.id" :class="project.current_stage_id == stage.id ? 'bg-purple-50' : ''">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ stage.name }}
                                                <span v-if="project.current_stage_id == stage.id" class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Actual</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ stage.description || '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t">
                         <ModelAttachments 
                            :model-id="project.id"
                            model-type="App\Models\Project"
                            area-slug="proyectos"
                            :files="project.files" 
                        />
                    </div>
                </div>

                <!-- Tab: Planes -->
                <div v-if="activeTab === 'plans'" class="p-6 w-full overflow-hidden">
                    <ProjectPlanner 
                        :project="project"
                        :selected-plan="selectedPlan"
                        :buckets="buckets"
                        :priorities="priorities"
                        :states="states"
                        :teams="teams"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
