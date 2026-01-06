<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MoneyInput from '@/Components/MoneyInput.vue'
import RichSelect from '@/Components/RichSelect.vue'
import { computed, ref, watch } from 'vue'

const props = defineProps({
    birthday: Object, // Null if creating
    users: Array,
    employees: Array,
    contacts: Array,
})

const isEditing = computed(() => !!props.birthday)

const form = useForm({
    is_employee: props.birthday ? (props.birthday.employee_id ? true : false) : true,
    employee_id: props.birthday?.employee_id || '',
    contact_id: props.birthday?.contact_id || '',
    date: props.birthday?.date ? props.birthday.date.substring(0, 10) : '',
    whatsapp: props.birthday?.whatsapp || '',
    comments: props.birthday?.comments || '',
    responsible_id: props.birthday?.responsible_id || '',
})

// Auto-fill date if an employee is selected and has a birth date
const selectEmployee = () => {
    if (form.employee_id) {
        const emp = props.employees.find(e => e.id === form.employee_id);
        if (emp && emp.birth_date) {
            form.date = emp.birth_date.substring(0, 10);
        }
    }
}

watch(() => form.employee_id, selectEmployee)


const submit = () => {
    if (isEditing.value) {
        form.put(route('rrhh.birthdays.update', props.birthday.id))
    } else {
        form.post(route('rrhh.birthdays.store'))
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Cumpleaños' : 'Nuevo Cumpleaños'">
        <div class="max-w-4xl mx-auto p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEditing ? 'Editar Cumpleaños' : 'Nuevo Cumpleaños' }}
                        </h1>
                        <p class="text-gray-600 mt-1">Registrar fecha especial</p>
                    </div>
                    <Link
                        :href="route('rrhh.birthdays.index')"
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
                             <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Registro</label>
                             <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" v-model="form.is_employee" :value="true" class="form-radio text-yellow-600 focus:ring-yellow-500">
                                    <span class="ml-2">Empleado</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" v-model="form.is_employee" :value="false" class="form-radio text-yellow-600 focus:ring-yellow-500">
                                    <span class="ml-2">Contacto Externo</span>
                                </label>
                             </div>
                        </div>
                        
                        <!-- Employee Select -->
                         <div v-if="form.is_employee" class="md:col-span-2">
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

                         <!-- Contact Select -->
                         <div v-else class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contacto <span class="text-red-500">*</span></label>
                            <select
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

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Cumpleaños <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.date" class="text-red-500 text-sm">{{ form.errors.date }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Responsable de Gestión</label>
                            <RichSelect
                                v-model="form.responsible_id"
                                :options="users"
                                placeholder="Seleccionar responsable..."
                                class="w-full"
                            />
                            <span v-if="form.errors.responsible_id" class="text-red-500 text-sm">{{ form.errors.responsible_id }}</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp (Opcional)</label>
                            <input
                                v-model="form.whatsapp"
                                type="text"
                                placeholder="+57 300 123 4567"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.whatsapp" class="text-red-500 text-sm">{{ form.errors.whatsapp }}</span>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios / Mensaje</label>
                            <textarea
                                v-model="form.comments"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            ></textarea>
                            <span v-if="form.errors.comments" class="text-red-500 text-sm">{{ form.errors.comments }}</span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                        <Link
                            :href="route('rrhh.birthdays.index')"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50"
                        >
                            {{ isEditing ? 'Actualizar Cumpleaños' : 'Guardar Cumpleaños' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
