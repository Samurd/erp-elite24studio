<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue'
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue'
import { computed } from 'vue'

const props = defineProps({
    holiday: Object, // Null if creating
    employees: Array,
    users: Array,
    typeOptions: Array,
    statusOptions: Array,
})

const isEditing = computed(() => !!props.holiday)

const form = useForm({
    employee_id: props.holiday?.employee_id || '',
    type_id: props.holiday?.type_id || '',
    start_date: props.holiday?.start_date ? props.holiday.start_date.substring(0, 10) : new Date().toISOString().substring(0, 10),
    end_date: props.holiday?.end_date ? props.holiday.end_date.substring(0, 10) : new Date().toISOString().substring(0, 10),
    status_id: props.holiday?.status_id || '',
    approver_id: props.holiday?.approver_id || '',
    files: [],
    pending_file_ids: [],
})

const submit = () => {
    if (isEditing.value) {
        form.put(route('rrhh.holidays.update', props.holiday.id))
    } else {
        form.post(route('rrhh.holidays.store'), {
            forceFormData: true,
        })
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Solicitud' : 'Nueva Solicitud'">
        <div class="max-w-4xl mx-auto p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEditing ? 'Editar Solicitud' : 'Nueva Solicitud' }}
                        </h1>
                        <p class="text-gray-600 mt-1">Permiso, Licencia o Vacaciones</p>
                    </div>
                    <Link
                        :href="route('rrhh.holidays.index')"
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
                             <h3 class="text-lg font-medium text-gray-800 border-b pb-2 mb-4">Información General</h3>
                        </div>

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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo <span class="text-red-500">*</span></label>
                            <select
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

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.start_date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.start_date" class="text-red-500 text-sm">{{ form.errors.start_date }}</span>
                        </div>

                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.end_date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.end_date" class="text-red-500 text-sm">{{ form.errors.end_date }}</span>
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Aprobador</label>
                             <select
                                v-model="form.approver_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar aprobador</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                             <span v-if="form.errors.approver_id" class="text-red-500 text-sm">{{ form.errors.approver_id }}</span>
                        </div>
                    </div>

                      <!-- Files Section -->
                    <div class="mt-8">
                        <div class="text-lg font-medium text-gray-800 border-b pb-2 mb-4">Soportes / Archivos Adjuntos</div>
                        
                         <ModelAttachmentsCreator 
                            v-if="!isEditing"
                            model-type="App\Models\Holiday"
                            area-slug="rrhh"
                            v-model:files="form.files"
                            v-model:pendingFileIds="form.pending_file_ids"
                        />
                        <ModelAttachments 
                            v-else
                            :model-id="holiday.id"
                            model-type="App\Models\Holiday"
                            area-slug="rrhh"
                            :files="holiday.files" 
                        />
                     </div>

                    <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                        <Link
                            :href="route('rrhh.holidays.index')"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50"
                        >
                            {{ isEditing ? 'Actualizar Solicitud' : 'Guardar Solicitud' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
