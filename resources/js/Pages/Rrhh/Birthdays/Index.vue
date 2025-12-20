<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { debounce } from 'lodash'

const props = defineProps({
    birthdays: Object,
    monthOptions: Array,
    filters: Object,
})

const search = ref(props.filters.search || '')
const type_filter = ref(props.filters.type_filter || '')
const month_filter = ref(props.filters.month_filter || '')

const updateParams = debounce(() => {
    router.get(route('rrhh.birthdays.index'), {
        search: search.value,
        type_filter: type_filter.value,
        month_filter: month_filter.value,
    }, { preserveState: true, replace: true })
}, 300)

watch([search, type_filter, month_filter], updateParams)

const deleteBirthday = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este cumpleaños?')) {
        router.delete(route('rrhh.birthdays.destroy', id))
    }
}

const formatDate = (date) => {
    if (!date) return '-'
    // Format only DD/MM as year can be irrelevant for birthdays list sometimes, but let's stick to full date if stored
    // Actually, users usually want to see the birth date
    return new Date(date).toLocaleDateString()
}
</script>

<template>
    <AppLayout title="Cumpleaños">
        <div class="max-w-7xl mx-auto p-6">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Cumpleaños</h1>
                    <p class="text-gray-600 mt-1">Gestión de cumpleaños y recordatorios</p>
                </div>
                <Link
                    :href="route('rrhh.birthdays.create')"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center shadow-sm"
                >
                    <i class="fas fa-plus mr-2"></i> Nuevo Cumpleaños
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar por nombre..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                />
                <select
                    v-model="type_filter"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                >
                    <option value="">Todos los tipos</option>
                    <option value="employee">Empleados</option>
                    <option value="contact">Contactos</option>
                </select>
                <select
                    v-model="month_filter"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                >
                     <option value="">Todos los meses</option>
                    <option v-for="month in monthOptions" :key="month.id" :value="month.id">{{ month.name }}</option>
                </select>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">WhatsApp</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="birthday in birthdays.data" :key="birthday.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ birthday.employee ? birthday.employee.full_name : (birthday.contact ? birthday.contact.name : birthday.name) }}
                                    </div>
                                    <div class="text-xs text-gray-500" v-if="birthday.comments">{{ birthday.comments }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <span v-if="birthday.employee_id" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        Empleado
                                    </span>
                                    <span v-else class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        Contacto
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(birthday.date) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ birthday.responsible?.name || 'Sin asignar' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ birthday.whatsapp || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link :href="route('rrhh.birthdays.edit', birthday.id)" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </Link>
                                    <button @click="deleteBirthday(birthday.id)" class="text-red-600 hover:text-red-900" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!birthdays.data.length">
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron cumpleaños.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200">
                    <Pagination :links="birthdays.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
