<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'

const props = defineProps({
    offboarding: Object, // Null if creating
    employees: Array,
    projects: Array,
    users: Array,
    statusOptions: Array,
})

const isEditing = computed(() => !!props.offboarding)

const form = useForm({
    employee_id: props.offboarding?.employee_id || '',
    project_id: props.offboarding?.project_id || '',
    reason: props.offboarding?.reason || '',
    exit_date: props.offboarding?.exit_date ? props.offboarding.exit_date.substring(0, 10) : new Date().toISOString().substring(0, 10),
    status_id: props.offboarding?.status_id || '',
    responsible_id: props.offboarding?.responsible_id || '',
})

const submit = () => {
    if (isEditing.value) {
        form.put(route('rrhh.offboardings.update', props.offboarding.id))
    } else {
        form.post(route('rrhh.offboardings.store'))
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar OffBoarding' : 'Nuevo OffBoarding'">
        <div class="max-w-4xl mx-auto p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEditing ? 'Editar OffBoarding' : 'Nuevo OffBoarding' }}
                        </h1>
                        <p class="text-gray-600 mt-1">Gestión del proceso de salida</p>
                    </div>
                    <Link
                        :href="route('rrhh.offboardings.index')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Empleado <span class="text-red-500">*</span></label>
                            <select
                                v-model="form.employee_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar empleado</option>
                                <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                    {{ employee.full_name }}
                                </option>
                            </select>
                            <span v-if="form.errors.employee_id" class="text-red-500 text-sm">{{ form.errors.employee_id }}</span>
                        </div>

                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Proyecto</label>
                            <select
                                v-model="form.project_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar proyecto</option>
                                <option v-for="project in projects" :key="project.id" :value="project.id">
                                    {{ project.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.project_id" class="text-red-500 text-sm">{{ form.errors.project_id }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Salida <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.exit_date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.exit_date" class="text-red-500 text-sm">{{ form.errors.exit_date }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                             <select
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

                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Responsable de Gestión</label>
                             <select
                                v-model="form.responsible_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar responsable</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                             <span v-if="form.errors.responsible_id" class="text-red-500 text-sm">{{ form.errors.responsible_id }}</span>
                        </div>

                        <div class="md:col-span-2">
                             <label class="block text-sm font-medium text-gray-700 mb-2">Motivo / Razón de Salida</label>
                            <textarea
                                v-model="form.reason"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            ></textarea>
                            <span v-if="form.errors.reason" class="text-red-500 text-sm">{{ form.errors.reason }}</span>
                        </div>

                    </div>

                    <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                        <Link
                            :href="route('rrhh.offboardings.index')"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50"
                        >
                            {{ isEditing ? 'Actualizar Proceso' : 'Guardar Proceso' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
