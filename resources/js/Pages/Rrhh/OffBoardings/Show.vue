<script setup>
import { ref } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    offboarding: Object,
    teams: Array,
})

// Task Form
const taskForm = useForm({
    content: '',
    team_id: '',
})

const addTask = () => {
    taskForm.post(route('rrhh.offboardings.tasks.store', props.offboarding.id), {
        onSuccess: () => taskForm.reset(),
        preserveScroll: true,
    })
}

const toggleTask = (taskId) => {
    router.put(route('rrhh.offboardings.tasks.toggle', taskId), {}, {
        preserveScroll: true,
    })
}

const deleteTask = (taskId) => {
    if (confirm('¿Estás seguro de eliminar esta tarea?')) {
        router.delete(route('rrhh.offboardings.tasks.destroy', taskId), {
            preserveScroll: true,
        })
    }
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString()
}
</script>

<template>
    <AppLayout :title="'OffBoarding - ' + offboarding.employee.full_name">
         <div class="max-w-5xl mx-auto p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                     <h1 class="text-2xl font-bold text-gray-800">Detalles de OffBoarding</h1>
                     <p class="text-gray-600 mt-1">{{ offboarding.employee.full_name }}</p>
                </div>
                <div class="flex gap-3">
                     <Link
                        :href="route('rrhh.offboardings.index')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                    <Link
                        :href="route('rrhh.offboardings.edit', offboarding.id)"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors"
                    >
                        <i class="fas fa-pencil-alt mr-2"></i>Editar
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Info Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 md:col-span-1 h-fit">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Información General</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="block text-xs text-gray-500 uppercase">Empleado</span>
                            <span class="block text-gray-900 font-medium">{{ offboarding.employee.full_name }}</span>
                        </div>
                         <div>
                            <span class="block text-xs text-gray-500 uppercase">Fecha Salida</span>
                            <span class="block text-gray-900 font-medium">{{ formatDate(offboarding.exit_date) }}</span>
                        </div>
                         <div>
                            <span class="block text-xs text-gray-500 uppercase">Proyecto</span>
                            <span class="block text-gray-900 font-medium">{{ offboarding.project?.name || 'N/A' }}</span>
                        </div>
                          <div>
                            <span class="block text-xs text-gray-500 uppercase">Estado</span>
                            <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                {{ offboarding.status?.name }}
                            </span>
                        </div>
                         <div>
                            <span class="block text-xs text-gray-500 uppercase">Responsable</span>
                            <span class="block text-gray-900 font-medium">{{ offboarding.responsible?.name || 'Sin asignar' }}</span>
                        </div>
                         <div>
                            <span class="block text-xs text-gray-500 uppercase">Motivo</span>
                            <p class="text-gray-600 text-sm mt-1 bg-gray-50 p-2 rounded">{{ offboarding.reason || 'No especificado' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tasks Section -->
                 <div class="bg-white rounded-lg shadow-sm p-6 md:col-span-2">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b">Lista de Chequeo / Tareas</h3>

                    <!-- Add Task Form -->
                    <form @submit.prevent="addTask" class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Agregar Nueva Tarea</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div class="md:col-span-3">
                                <input
                                    v-model="taskForm.content"
                                    type="text"
                                    placeholder="Descripción de la tarea..."
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-yellow-500"
                                />
                            </div>
                            <div>
                                <select
                                    v-model="taskForm.team_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-yellow-500"
                                >
                                    <option value="">Equipo (Opcional)</option>
                                    <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                                </select>
                            </div>
                            <div class="md:col-span-4 text-right">
                                <button
                                    type="submit"
                                    :disabled="taskForm.processing"
                                    class="bg-blue-600 text-white text-sm px-4 py-2 rounded hover:bg-blue-700 transition-colors"
                                >
                                    Agregar Tarea
                                </button>
                            </div>
                        </div>
                         <p v-if="taskForm.errors.content" class="text-red-500 text-xs mt-1">{{ taskForm.errors.content }}</p>
                    </form>

                    <!-- Tasks List -->
                    <div class="space-y-3">
                         <div
                            v-for="task in offboarding.tasks"
                            :key="task.id"
                            class="flex items-start p-3 border rounded-lg transition-colors"
                            :class="{'bg-green-50 border-green-200': task.completed, 'bg-white border-gray-200': !task.completed}"
                        >
                            <div class="flex items-center h-5 mt-1">
                                <input
                                    type="checkbox"
                                    :checked="task.completed"
                                    @change="toggleTask(task.id)"
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded cursor-pointer"
                                />
                            </div>
                            <div class="ml-3 flex-1">
                                <p :class="{'text-gray-500 line-through': task.completed, 'text-gray-900': !task.completed}" class="text-sm font-medium">
                                    {{ task.content }}
                                </p>
                                <div class="flex gap-2 mt-1 text-xs text-gray-500">
                                    <span v-if="task.team" class="bg-gray-100 px-1.5 py-0.5 rounded">
                                        <i class="fas fa-users mr-1"></i>{{ task.team.name }}
                                    </span>
                                    <span v-if="task.completed_by">
                                        <i class="fas fa-check-circle mr-1 text-green-600"></i>
                                        Completado por {{ task.completed_by.name }} el {{ formatDate(task.completed_at) }}
                                    </span>
                                </div>
                            </div>
                             <button @click="deleteTask(task.id)" class="text-gray-400 hover:text-red-500 ml-2">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                         <div v-if="!offboarding.tasks.length" class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            <i class="fas fa-tasks text-2xl mb-2 text-gray-400"></i>
                            <p>No hay tareas registradas para este proceso.</p>
                        </div>
                    </div>
                 </div>
            </div>
         </div>
    </AppLayout>
</template>
