<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'

const props = defineProps({
    induction: Object, // Null if creating
    typeBondOptions: Array,
    statusOptions: Array,
    confirmationOptions: Array,
    employees: Array,
    responsibles: Array,
})

const isEditing = computed(() => !!props.induction)

const form = useForm({
    employee_id: props.induction?.employee_id || '',
    type_bond_id: props.induction?.type_bond_id || '',
    entry_date: props.induction?.entry_date ? props.induction.entry_date.substring(0, 10) : new Date().toISOString().substring(0, 10),
    responsible_id: props.induction?.responsible_id || '',
    date: props.induction?.date ? props.induction.date.substring(0, 10) : new Date().toISOString().substring(0, 10),
    status_id: props.induction?.status_id || '',
    confirmation_id: props.induction?.confirmation_id || '',
    resource: props.induction?.resource || '',
    duration: props.induction?.duration ? props.induction.duration.substring(0, 5) : '',
    observations: props.induction?.observations || '',
})

const submit = () => {
    if (isEditing.value) {
        form.put(route('rrhh.inductions.update', props.induction.id))
    } else {
        form.post(route('rrhh.inductions.store'))
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Inducción' : 'Nueva Inducción'">
        <div class="max-w-4xl mx-auto p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEditing ? 'Editar Inducción' : 'Nueva Inducción' }}
                        </h1>
                        <p class="text-gray-600 mt-1">Detalles del proceso de inducción</p>
                    </div>
                    <Link
                        :href="route('rrhh.inductions.index')"
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Vínculo</label>
                            <select
                                v-model="form.type_bond_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar tipo</option>
                                <option v-for="type in typeBondOptions" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.type_bond_id" class="text-red-500 text-sm">{{ form.errors.type_bond_id }}</span>
                        </div>

                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Ingreso <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.entry_date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.entry_date" class="text-red-500 text-sm">{{ form.errors.entry_date }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                            <select
                                v-model="form.responsible_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar responsable</option>
                                <option v-for="user in responsibles" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.responsible_id" class="text-red-500 text-sm">{{ form.errors.responsible_id }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Inducción</label>
                            <input
                                v-model="form.date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.date" class="text-red-500 text-sm">{{ form.errors.date }}</span>
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirmación</label>
                            <select
                                v-model="form.confirmation_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar confirmación</option>
                                <option v-for="conf in confirmationOptions" :key="conf.id" :value="conf.id">
                                    {{ conf.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.confirmation_id" class="text-red-500 text-sm">{{ form.errors.confirmation_id }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Recurso</label>
                            <input
                                v-model="form.resource"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.resource" class="text-red-500 text-sm">{{ form.errors.resource }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Duración (HH:MM)</label>
                            <input
                                v-model="form.duration"
                                type="time"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.duration" class="text-red-500 text-sm">{{ form.errors.duration }}</span>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                            <textarea
                                v-model="form.observations"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            ></textarea>
                            <span v-if="form.errors.observations" class="text-red-500 text-sm">{{ form.errors.observations }}</span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                        <Link
                            :href="route('rrhh.inductions.index')"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50"
                        >
                            {{ isEditing ? 'Actualizar Inducción' : 'Guardar Inducción' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
