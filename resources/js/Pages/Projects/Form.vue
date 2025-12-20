<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue'
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue'
import { computed, ref, onMounted } from 'vue'

const props = defineProps({
    project: Object,
    statusOptions: Array,
    projectTypeOptions: Array,
    contacts: Array,
    users: Array,
    teams: Array,
    stages: Array,    // All stages available for selection
    projectStages: Array, // Existing stages for this project
})

const isEditing = computed(() => !!props.project)

// Local state for stage management
const managedStages = ref([]) 
const showStageForm = ref(false)
const stageForm = ref({
    type: 'new', // 'new' or 'edit'
    id: null, // real ID or temp ID
    name: '',
    description: ''
})

onMounted(() => {
    // If editing, load existing stages into managedStages
    if (isEditing.value && props.projectStages) {
        managedStages.value = props.projectStages.map(stage => ({
            id: stage.id,
            name: stage.name,
            description: stage.description,
            is_temp: false
        }))
    }
})

const form = useForm({
    _method: isEditing.value ? 'PUT' : 'POST',
    name: props.project?.name || '',
    description: props.project?.description || '',
    direction: props.project?.direction || '',
    contact_id: props.project?.contact_id || '',
    status_id: props.project?.status_id || '',
    project_type_id: props.project?.project_type_id || '',
    current_stage_id: props.project?.current_stage_id || '',
    initial_stage_id: '', // Only for create
    responsible_id: props.project?.responsible_id || '',
    team_id: props.project?.team_id || '',
    
    // Arrays for stage handling
    temp_stages: [], // For Create mode
    managed_stages: [], // For Update mode (sync)
    
    files: [],
    pending_file_ids: [],
})

const submit = () => {
    if (isEditing.value) {
        // Sync managedStages to form
        form.managed_stages = managedStages.value
        form.post(route('projects.update-post', props.project.id), { // Use specific POST route for files
             forceFormData: true,
        })
    } else {
        // Filter temp stages (those with temp_ prefix)
        form.temp_stages = managedStages.value.filter(s => s.is_temp)
        form.post(route('projects.store'), {
            forceFormData: true,
        })
    }
}

const deleteProject = () => {
    if (confirm('¿Estás seguro de que deseas eliminar este proyecto? Esta acción no se puede deshacer.')) {
        router.delete(route('projects.destroy', props.project.id))
    }
}

// Stage Management Functions
const openStageForm = () => {
    stageForm.value = { type: 'new', id: null, name: '', description: '' }
    showStageForm.value = true
}

const editStage = (stage) => {
    stageForm.value = { 
        type: 'edit', 
        id: stage.id, 
        name: stage.name, 
        description: stage.description 
    }
    showStageForm.value = true
}

const saveStage = () => {
    if (!stageForm.value.name) return

    if (stageForm.value.type === 'new') {
        const tempId = 'temp_' + Date.now() + Math.random().toString(36).substr(2, 5)
        managedStages.value.push({
            id: tempId,
            name: stageForm.value.name,
            description: stageForm.value.description,
            is_temp: true
        })
    } else {
        // Update existing in list
        const index = managedStages.value.findIndex(s => s.id === stageForm.value.id)
        if (index !== -1) {
            managedStages.value[index] = {
                ...managedStages.value[index],
                name: stageForm.value.name,
                description: stageForm.value.description
            }
        }
    }
    showStageForm.value = false
}

const removeStage = (id) => {
    if (confirm('¿Estás seguro de eliminar esta etapa? Si es una etapa existente, se eliminará al guardar el proyecto (o contacta al administrador).')) {
        // For Create mode: just remove from array
        // For Edit mode: If we remove it from the array AND use sync logic, it might deleting it.
        // However, the controller logic we wrote primarily handles UPDATES.
        // If the user wants to DELETE an existing stage, we should probably have a direct route or handle strictly.
        // Let's rely on the list update for now. 
        managedStages.value = managedStages.value.filter(s => s.id !== id)
    }
}

const cancelStage = () => {
    showStageForm.value = false
}

// Combine explicit stages + managed stages for stage selection
// In Create mode: we have existing global 'stages' prop + managedStages (temp)
// In Edit mode: we have existing global 'stages' prop + managedStages (which are current project stages)
const selectableStages = computed(() => {
    // If we want to allow selecting newly added temp stages as current stage:
    return [...props.stages, ...managedStages.value.filter(s => s.is_temp)]
})

</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Proyecto' : 'Nuevo Proyecto'">
        <div class="max-w-6xl mx-auto p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEditing ? 'Editar Proyecto' : 'Nuevo Proyecto' }}
                        </h1>
                        <p class="text-gray-600 mt-1">
                            {{ isEditing ? 'Actualiza la información del proyecto' : 'Complete los datos para registrar un nuevo proyecto' }}
                        </p>
                    </div>
                    <Link
                        :href="route('projects.index')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Nombre del Proyecto -->
                        <div class="lg:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Proyecto <span class="text-red-500">*</span></label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                placeholder="Nombre del proyecto"
                            />
                            <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                        </div>

                        <!-- Contacto/Cliente -->
                        <div>
                            <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-2">Contacto/Cliente</label>
                            <select
                                id="contact_id"
                                v-model="form.contact_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar contacto</option>
                                <option v-for="contact in contacts" :key="contact.id" :value="contact.id">
                                    {{ contact.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.contact_id" class="text-red-500 text-sm">{{ form.errors.contact_id }}</span>
                        </div>

                        <!-- Tipo de Proyecto -->
                        <div>
                            <label for="project_type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Proyecto</label>
                            <select
                                id="project_type_id"
                                v-model="form.project_type_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar tipo</option>
                                <option v-for="type in projectTypeOptions" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.project_type_id" class="text-red-500 text-sm">{{ form.errors.project_type_id }}</span>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                            <select
                                id="status_id"
                                v-model="form.status_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar estado</option>
                                <option v-for="status in statusOptions" :key="status.id" :value="status.id">
                                    {{ status.name }}
                                </option>
                            </select>
                             <span v-if="form.errors.status_id" class="text-red-500 text-sm">{{ form.errors.status_id }}</span>
                        </div>

                        <!-- Etapa Inicial / Actual -->
                        <div v-if="!isEditing">
                             <label for="initial_stage_id" class="block text-sm font-medium text-gray-700 mb-2">Etapa Inicial</label>
                            <select
                                id="initial_stage_id"
                                v-model="form.initial_stage_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar etapa inicial</option>
                                <!-- Show global stages -->
                                <optgroup label="Existentes">
                                    <option v-for="stage in stages" :key="stage.id" :value="stage.id">
                                        {{ stage.name }}
                                    </option>
                                </optgroup>
                                <!-- Show temp stages -->
                                <optgroup v-if="managedStages.length > 0" label="Nuevas (Temporales)">
                                    <option v-for="stage in managedStages" :key="stage.id" :value="stage.id">
                                        {{ stage.name }}
                                    </option>
                                </optgroup>
                            </select>
                        </div>
                        <div v-else>
                            <label for="current_stage_id" class="block text-sm font-medium text-gray-700 mb-2">Etapa Actual</label>
                            <select
                                id="current_stage_id"
                                v-model="form.current_stage_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar etapa</option>
                                <option v-for="stage in stages" :key="stage.id" :value="stage.id">
                                        {{ stage.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Responsable -->
                        <div>
                            <label for="responsible_id" class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                            <select
                                id="responsible_id"
                                v-model="form.responsible_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar responsable</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Equipo -->
                        <div>
                            <label for="team_id" class="block text-sm font-medium text-gray-700 mb-2">Equipo</label>
                            <select
                                id="team_id"
                                v-model="form.team_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Sin equipo asignado</option>
                                <option v-for="team in teams" :key="team.id" :value="team.id">
                                    {{ team.name }}
                                </option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Los planes de este proyecto heredarán este equipo</p>
                        </div>

                        <!-- Dirección -->
                        <div class="lg:col-span-3">
                            <label for="direction" class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                            <input
                                id="direction"
                                v-model="form.direction"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                placeholder="Dirección del proyecto"
                            />
                        </div>

                         <!-- Descripción -->
                        <div class="lg:col-span-3">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                placeholder="Descripción detallada..."
                            ></textarea>
                        </div>
                    </div>

                    <!-- Archivos -->
                    <div class="mt-8 pt-6 border-t">
                        <h3 class="text-lg font-semibold mb-4">Archivos Adjuntos</h3>
                        <div v-if="isEditing">
                             <ModelAttachments
                                :model-id="project.id"
                                model-type="App\Models\Project"
                                area-slug="proyectos"
                                :files="project.files || []"
                            />
                        </div>
                        <div v-else>
                            <ModelAttachmentsCreator
                                model-type="App\Models\Project"
                                area-slug="proyectos"
                                v-model:files="form.files"
                                v-model:pendingFileIds="form.pending_file_ids"
                            />
                        </div>
                    </div>
                
                     <!-- Stage Management Section -->
                    <div class="mt-8 pt-6 border-t bg-gray-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-bold text-gray-800">Gestión de Etapas</h2>
                            <button
                                type="button"
                                @click="openStageForm"
                                class="bg-yellow-600 text-white px-3 py-2 rounded-lg hover:bg-yellow-700 transition-colors text-sm"
                            >
                                <i class="fas fa-plus mr-2"></i>Nueva Etapa
                            </button>
                        </div>

                        <!-- Stage Form Inline -->
                        <div v-if="showStageForm" class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de Etapa</label>
                                    <input v-model="stageForm.name" type="text" class="w-full px-3 py-2 border rounded-lg" placeholder="Ej: Planificación">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                                    <input v-model="stageForm.description" type="text" class="w-full px-3 py-2 border rounded-lg" placeholder="Breve descripción">
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 mt-4">
                                <button type="button" @click="cancelStage" class="px-3 py-1 text-gray-600 hover:text-gray-800">Cancelar</button>
                                <button type="button" @click="saveStage" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Explícitamente Guardar en Lista</button>
                            </div>
                        </div>

                        <!-- Stage List -->
                        <div v-if="managedStages.length > 0" class="space-y-2">
                            <div v-for="stage in managedStages" :key="stage.id" class="flex items-center justify-between p-3 bg-white rounded border">
                                <div>
                                    <p class="font-medium text-gray-800">{{ stage.name }}</p>
                                    <p class="text-xs text-gray-500">{{ stage.description }}</p>
                                    <span v-if="stage.is_temp" class="text-xs text-blue-500 font-semibold">Nueva (Se guardará al guardar el proyecto)</span>
                                </div>
                                <div class="flex space-x-2">
                                    <button type="button" @click="editStage(stage)" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                                    <button type="button" @click="removeStage(stage.id)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center text-gray-500 py-4">
                            No hay etapas específicas definidas.
                        </div>
                    </div>


                    <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                        <button
                            v-if="isEditing"
                            type="button"
                            @click="deleteProject"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors mr-auto"
                        >
                            Eliminar Proyecto
                        </button>

                        <Link
                            :href="route('projects.index')"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50"
                        >
                            {{ isEditing ? 'Actualizar Proyecto' : 'Guardar Proyecto' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
