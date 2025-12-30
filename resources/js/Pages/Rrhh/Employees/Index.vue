<script setup>
import { ref, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import DialogModal from '@/Components/DialogModal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import { debounce } from 'lodash'

const props = defineProps({
    employees: Object,
    departments: Array,
    totalEmployees: Number,
    filters: Object,
    genderOptions: Array,
    educationOptions: Array,
    maritalStatusOptions: Array,
})

const search = ref(props.filters.search || '')
const department_id = ref(props.filters.department_id || '')
const job_title = ref(props.filters.job_title || '')

watch([search, department_id, job_title], debounce(() => {
    router.get(route('rrhh.employees.index'), {
        search: search.value,
        department_id: department_id.value,
        job_title: job_title.value,
    }, { preserveState: true, replace: true })
}, 300))

const deleteEmployee = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este empleado?')) {
        router.delete(route('rrhh.employees.destroy', id))
    }
}

// Department Modal
const showDepartmentModal = ref(false)
const departmentForm = useForm({
    name: '',
    description: '',
})

const openDepartmentModal = () => {
    departmentForm.reset()
    showDepartmentModal.value = true
}

const submitDepartment = () => {
    departmentForm.post(route('rrhh.departments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showDepartmentModal.value = false
            departmentForm.reset()
        }
    })
}
</script>

<template>
    <AppLayout title="Empleados">
        <div class="max-w-7xl mx-auto p-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Empleados</h1>
                    <p class="text-gray-600 mt-1">Gestión de personal - Total: {{ totalEmployees }}</p>
                </div>
                <div class="flex gap-2">
                    <button
                        @click="openDepartmentModal"
                        class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center"
                    >
                        <i class="fas fa-building mr-2"></i> Nuevo Depto
                    </button>
                    <Link
                    :href="route('rrhh.employees.create')"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center"
                >
                    <i class="fas fa-plus mr-2"></i> Nuevo Empleado
                </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Buscar por nombre, email o identificación..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    />
                </div>
                <div>
                    <input
                        v-model="job_title"
                        type="text"
                        placeholder="Filtrar por cargo..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    />
                </div>
                <div>
                     <select
                        v-model="department_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    >
                        <option value="">Todos los departamentos</option>
                        <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                            {{ dept.name }} ({{ dept.employees_count }})
                        </option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo / Dept</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="employee in employees.data" :key="employee.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-bold text-sm uppercase">
                                            {{ employee.full_name?.substring(0,2) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ employee.full_name }}</div>
                                            <div class="text-sm text-gray-500">{{ employee.identification_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ employee.work_email }}</div>
                                    <div class="text-sm text-gray-500">{{ employee.mobile_phone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ employee.job_title }}</div>
                                    <div class="text-xs text-gray-500">{{ employee.department?.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activo <!-- Placeholder as Status not explicit in model here -->
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-3">
                                    <Link :href="route('rrhh.employees.edit', employee.id)" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button @click="deleteEmployee(employee.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                             <tr v-if="!employees.data.length">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No se encontraron empleados.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200">
                     <Pagination :links="employees.links" />
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Create Department Modal -->
    <DialogModal :show="showDepartmentModal" @close="showDepartmentModal = false">
        <template #title>
             Crear Nuevo Departamento
        </template>
        <template #content>
            <div class="space-y-4">
                <div>
                    <InputLabel for="dept_name" value="Nombre del Departamento" />
                    <TextInput id="dept_name" v-model="departmentForm.name" type="text" class="mt-1 block w-full" placeholder="Ej. Recursos Humanos" />
                    <InputError :message="departmentForm.errors.name" class="mt-2" />
                </div>
                 <div>
                    <InputLabel for="dept_desc" value="Descripción (Opcional)" />
                    <textarea id="dept_desc" v-model="departmentForm.description" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" rows="3"></textarea>
                    <InputError :message="departmentForm.errors.description" class="mt-2" />
                </div>
            </div>
        </template>
        <template #footer>
            <SecondaryButton @click="showDepartmentModal = false">
                Cancelar
            </SecondaryButton>
             <PrimaryButton class="ml-3" @click="submitDepartment" :disabled="departmentForm.processing">
                Crear Departamento
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
