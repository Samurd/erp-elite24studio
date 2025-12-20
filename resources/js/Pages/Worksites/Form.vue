<script setup>
import { useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'

const props = defineProps({
    worksite: Object,
    typeOptions: Array,
    statusOptions: Array,
    projects: Array,
    users: Array,
})

const isEditing = computed(() => !!props.worksite)

const form = useForm({
    _method: isEditing.value ? 'PUT' : 'POST',
    project_id: props.worksite?.project_id || '',
    name: props.worksite?.name || '',
    type_id: props.worksite?.type_id || '',
    status_id: props.worksite?.status_id || '',
    responsible_id: props.worksite?.responsible_id || '',
    address: props.worksite?.address || '',
    start_date: props.worksite?.start_date ? props.worksite.start_date.substring(0, 10) : '',
    end_date: props.worksite?.end_date ? props.worksite.end_date.substring(0, 10) : '',
})

const submit = () => {
    if (isEditing.value) {
        form.post(route('worksites.update', props.worksite.id))
    } else {
        form.post(route('worksites.store'))
    }
}

const deleteWorksite = () => {
    if (confirm('¿Estás seguro de que deseas eliminar esta obra?')) {
        router.delete(route('worksites.destroy', props.worksite.id))
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Obra' : 'Nueva Obra'">
        <div class="max-w-4xl mx-auto p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEditing ? 'Editar Obra' : 'Nueva Obra' }}
                        </h1>
                        <p class="text-gray-600 mt-1">
                            {{ isEditing ? 'Actualiza la información de la obra' : 'Complete los datos para registrar una nueva obra' }}
                        </p>
                    </div>
                    <Link
                        :href="route('worksites.index')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Proyecto -->
                        <div class="md:col-span-2">
                            <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Proyecto <span class="text-red-500">*</span></label>
                            <select
                                id="project_id"
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

                         <!-- Nombre -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Obra <span class="text-red-500">*</span></label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                placeholder="Nombre de la obra"
                            />
                            <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                        </div>

                        <!-- Tipo -->
                        <div>
                            <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Obra</label>
                            <select
                                id="type_id"
                                v-model="form.type_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar tipo</option>
                                <option v-for="type in typeOptions" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.type_id" class="text-red-500 text-sm">{{ form.errors.type_id }}</span>
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

                        <!-- Responsable -->
                        <div class="md:col-span-2">
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
                             <span v-if="form.errors.responsible_id" class="text-red-500 text-sm">{{ form.errors.responsible_id }}</span>
                        </div>

                        <!-- Dirección -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                            <textarea
                                id="address"
                                v-model="form.address"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                placeholder="Dirección de la obra"
                            ></textarea>
                            <span v-if="form.errors.address" class="text-red-500 text-sm">{{ form.errors.address }}</span>
                        </div>

                        <!-- Fecha Inicio -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Inicio</label>
                            <input
                                id="start_date"
                                v-model="form.start_date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.start_date" class="text-red-500 text-sm">{{ form.errors.start_date }}</span>
                        </div>

                        <!-- Fecha Fin -->
                         <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Fin</label>
                            <input
                                id="end_date"
                                v-model="form.end_date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.end_date" class="text-red-500 text-sm">{{ form.errors.end_date }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                        <button
                            v-if="isEditing"
                            type="button"
                            @click="deleteWorksite"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors mr-auto"
                        >
                            Eliminar Obra
                        </button>
                        <Link
                            :href="route('worksites.index')"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50"
                        >
                            {{ isEditing ? 'Actualizar Obra' : 'Guardar Obra' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
