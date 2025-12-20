<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'

const props = defineProps({
    attendance: Object, // Null if creating
    employees: Array,
    statusOptions: Array,
    modalityOptions: Array,
})

const isEditing = computed(() => !!props.attendance)

const form = useForm({
    employee_id: props.attendance?.employee_id || '',
    date: props.attendance?.date ? props.attendance.date.substring(0, 10) : new Date().toISOString().substring(0, 10),
    check_in: props.attendance?.check_in ? props.attendance.check_in.substring(0, 5) : '',
    check_out: props.attendance?.check_out ? props.attendance.check_out.substring(0, 5) : '',
    status_id: props.attendance?.status_id || '',
    modality_id: props.attendance?.modality_id || '',
    observations: props.attendance?.observations || '',
})

const submit = () => {
    if (isEditing.value) {
        form.put(route('rrhh.attendances.update', props.attendance.id))
    } else {
        form.post(route('rrhh.attendances.store'))
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Asistencia' : 'Nueva Asistencia'">
        <div class="max-w-4xl mx-auto p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEditing ? 'Editar Asistencia' : 'Registrar Asistencia' }}
                        </h1>
                        <p class="text-gray-600 mt-1">Detalles del registro diario</p>
                    </div>
                    <Link
                        :href="route('rrhh.attendances.index')"
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

                        <div class="md:col-span-2">
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.date" class="text-red-500 text-sm">{{ form.errors.date }}</span>
                        </div>

                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Modalidad</label>
                            <select
                                v-model="form.modality_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar modalidad</option>
                                <option v-for="modality in modalityOptions" :key="modality.id" :value="modality.id">
                                    {{ modality.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.modality_id" class="text-red-500 text-sm">{{ form.errors.modality_id }}</span>
                        </div>

                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hora Entrada <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.check_in"
                                type="time"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.check_in" class="text-red-500 text-sm">{{ form.errors.check_in }}</span>
                        </div>

                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hora Salida <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.check_out"
                                type="time"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.check_out" class="text-red-500 text-sm">{{ form.errors.check_out }}</span>
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


                        <div class="md:col-span-2">
                             <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                            <textarea
                                v-model="form.observations"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            ></textarea>
                            <span v-if="form.errors.observations" class="text-red-500 text-sm">{{ form.errors.observations }}</span>
                        </div>

                    </div>

                    <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                        <Link
                            :href="route('rrhh.attendances.index')"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50"
                        >
                            {{ isEditing ? 'Actualizar Asistencia' : 'Registrar Asistencia' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
