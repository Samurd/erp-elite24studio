<script setup>
import { useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'

const props = defineProps({
    vacancy: Object, // Null if creating
    contractTypes: Array,
    statuses: Array,
    rrhhUsers: Array,
})

const isEditing = computed(() => !!props.vacancy)

const form = useForm({
    title: props.vacancy?.title || '',
    area: props.vacancy?.area || '',
    contract_type_id: props.vacancy?.contract_type_id || '',
    published_at: props.vacancy?.published_at ? props.vacancy.published_at.substring(0, 10) : new Date().toISOString().substring(0, 10),
    status_id: props.vacancy?.status_id || '',
    user_id: props.vacancy?.user_id || props.rrhhUsers?.[0]?.id || '', // Default to first user if new
    description: props.vacancy?.description || '',
})

const submit = () => {
    if (isEditing.value) {
        form.put(route('rrhh.vacancies.update', props.vacancy.id))
    } else {
        form.post(route('rrhh.vacancies.store'))
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Vacante' : 'Nueva Vacante'">
        <div class="max-w-4xl mx-auto p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEditing ? 'Editar Vacante' : 'Nueva Vacante' }}
                        </h1>
                        <p class="text-gray-600 mt-1">Información de la oportunidad laboral</p>
                    </div>
                    <Link
                        :href="route('rrhh.vacancies.index')"
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
                             <label class="block text-sm font-medium text-gray-700 mb-2">Título de la Vacante <span class="text-red-500">*</span></label>
                             <input
                                v-model="form.title"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                             <span v-if="form.errors.title" class="text-red-500 text-sm">{{ form.errors.title }}</span>
                        </div>

                        <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Área / Departamento <span class="text-red-500">*</span></label>
                             <input
                                v-model="form.area"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                             <span v-if="form.errors.area" class="text-red-500 text-sm">{{ form.errors.area }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Contrato <span class="text-red-500">*</span></label>
                            <select
                                v-model="form.contract_type_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar tipo</option>
                                <option v-for="type in contractTypes" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.contract_type_id" class="text-red-500 text-sm">{{ form.errors.contract_type_id }}</span>
                        </div>

                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado <span class="text-red-500">*</span></label>
                            <select
                                v-model="form.status_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar estado</option>
                                <option v-for="status in statuses" :key="status.id" :value="status.id">
                                    {{ status.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.status_id" class="text-red-500 text-sm">{{ form.errors.status_id }}</span>
                        </div>

                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Responsable <span class="text-red-500">*</span></label>
                            <select
                                v-model="form.user_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar responsable</option>
                                <option v-for="user in rrhhUsers" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.user_id" class="text-red-500 text-sm">{{ form.errors.user_id }}</span>
                        </div>

                        <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Publicación</label>
                            <input
                                v-model="form.published_at"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.published_at" class="text-red-500 text-sm">{{ form.errors.published_at }}</span>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                            <textarea
                                v-model="form.description"
                                rows="6"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            ></textarea>
                            <span v-if="form.errors.description" class="text-red-500 text-sm">{{ form.errors.description }}</span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                        <Link
                            :href="route('rrhh.vacancies.index')"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50"
                        >
                            {{ isEditing ? 'Actualizar Vacante' : 'Guardar Vacante' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
