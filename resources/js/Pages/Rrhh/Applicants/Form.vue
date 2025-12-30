<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'

const props = defineProps({
    applicant: Object,
    vacancies: Array,
    statusOptions: Array,
})

const isEditing = computed(() => !!props.applicant)

const form = useForm({
    vacancy_id: props.applicant?.vacancy_id || '',
    full_name: props.applicant?.full_name || '',
    email: props.applicant?.email || '',
    status_id: props.applicant?.status_id || '',
    notes: props.applicant?.notes || '',
})

const submit = () => {
    if (isEditing.value) {
        form.put(route('rrhh.applicants.update', props.applicant.id))
    } else {
        form.post(route('rrhh.applicants.store'))
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Postulante' : 'Nuevo Postulante'">
        <div class="max-w-7xl mx-auto p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEditing ? 'Editar Postulante' : 'Nuevo Postulante' }}
                        </h1>
                        <p class="text-gray-600 mt-1">
                            {{ isEditing ? 'Modificar información del postulante' : 'Registrar nuevo postulante' }}
                        </p>
                    </div>
                    <Link :href="route('rrhh.vacancies.index')" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <!-- Vacante -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vacante <span class="text-red-500">*</span></label>
                            <select v-model="form.vacancy_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                <option value="">Seleccionar vacante</option>
                                <option v-for="vacancy in vacancies" :key="vacancy.id" :value="vacancy.id">{{ vacancy.title }}</option>
                            </select>
                            <div v-if="form.errors.vacancy_id" class="text-red-500 text-sm mt-1">{{ form.errors.vacancy_id }}</div>
                        </div>

                        <!-- Nombre Completo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo <span class="text-red-500">*</span></label>
                            <input v-model="form.full_name" type="text" placeholder="Ingrese el nombre completo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                            <div v-if="form.errors.full_name" class="text-red-500 text-sm mt-1">{{ form.errors.full_name }}</div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                            <input v-model="form.email" type="email" placeholder="correo@ejemplo.com" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500" />
                            <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado <span class="text-red-500">*</span></label>
                            <select v-model="form.status_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                <option value="">Seleccionar estado</option>
                                <option v-for="status in statusOptions" :key="status.id" :value="status.id">{{ status.name }}</option>
                            </select>
                            <div v-if="form.errors.status_id" class="text-red-500 text-sm mt-1">{{ form.errors.status_id }}</div>
                        </div>

                         <!-- Notas -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                            <textarea v-model="form.notes" rows="4" placeholder="Notas adicionales sobre el postulante..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"></textarea>
                            <div v-if="form.errors.notes" class="text-red-500 text-sm mt-1">{{ form.errors.notes }}</div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3 mt-6">
                        <Link :href="route('rrhh.vacancies.index')" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </Link>
                        <button type="submit" :disabled="form.processing" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50">
                            <i class="fas fa-save mr-2"></i>
                            {{ isEditing ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
