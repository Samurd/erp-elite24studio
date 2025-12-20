<script setup>
import { useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue'
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue'
import MoneyInput from '@/Components/MoneyInput.vue'
import { computed } from 'vue'

const props = defineProps({
    contract: Object, // Null if creating
    employees: Array,
    typeOptions: Array,
    categoryOptions: Array,
    statusOptions: Array,
})

const isEditing = computed(() => !!props.contract)

const form = useForm({
    employee_id: props.contract?.employee_id || '',
    type_id: props.contract?.type_id || '',
    category_id: props.contract?.category_id || '',
    status_id: props.contract?.status_id || '',
    start_date: props.contract?.start_date ? props.contract.start_date.substring(0, 10) : new Date().toISOString().substring(0, 10),
    end_date: props.contract?.end_date ? props.contract.end_date.substring(0, 10) : '',
    amount: props.contract?.amount || '',
    schedule: props.contract?.schedule || '',
    files: [],
    pending_file_ids: [],
})

const submit = () => {
    if (isEditing.value) {
        form.put(route('rrhh.contracts.update', props.contract.id))
    } else {
        form.post(route('rrhh.contracts.store'), {
            forceFormData: true,
        })
    }
}

const deleteContract = () => {
    if (confirm('¿Estás seguro de que deseas eliminar este contrato?')) {
        router.delete(route('rrhh.contracts.destroy', props.contract.id))
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Contrato' : 'Nuevo Contrato'">
        <div class="max-w-4xl mx-auto p-6">
             <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEditing ? 'Editar Contrato' : 'Nuevo Contrato' }}
                        </h1>
                        <p class="text-gray-600 mt-1">Diligencie la información del contrato</p>
                    </div>
                    <Link
                        :href="route('rrhh.contracts.index')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Empleado -->
                        <div class="md:col-span-2">
                             <label class="block text-sm font-medium text-gray-700 mb-2">Empleado <span class="text-red-500">*</span></label>
                             <select
                                v-model="form.employee_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar empleado</option>
                                <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                    {{ employee.full_name }} ({{ employee.identification_number }})
                                </option>
                            </select>
                             <span v-if="form.errors.employee_id" class="text-red-500 text-sm">{{ form.errors.employee_id }}</span>
                        </div>

                        <!-- Tipo de Contrato -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Contrato <span class="text-red-500">*</span></label>
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

                        <!-- Categoría / Tipo Relación -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categoría / Tipo Relación <span class="text-red-500">*</span></label>
                            <select
                                v-model="form.category_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar categoría</option>
                                <option v-for="category in categoryOptions" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.category_id" class="text-red-500 text-sm">{{ form.errors.category_id }}</span>
                        </div>

                         <!-- Estado -->
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado <span class="text-red-500">*</span></label>
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

                         <!-- Salario -->
                        <div>
                             <MoneyInput
                                id="amount"
                                v-model="form.amount"
                                label="Salario / Monto"
                                :error="form.errors.amount"
                            />
                        </div>

                        <!-- Fecha Inicio -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.start_date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.start_date" class="text-red-500 text-sm">{{ form.errors.start_date }}</span>
                        </div>

                        <!-- Fecha Fin -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                            <input
                                v-model="form.end_date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.end_date" class="text-red-500 text-sm">{{ form.errors.end_date }}</span>
                        </div>
                        
                         <!-- Horario -->
                        <div class="md:col-span-2">
                             <label class="block text-sm font-medium text-gray-700 mb-2">Horario Laboral</label>
                             <input
                                v-model="form.schedule"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                placeholder="Ej: Lunes a Viernes 8am - 5pm"
                            />
                             <span v-if="form.errors.schedule" class="text-red-500 text-sm">{{ form.errors.schedule }}</span>
                        </div>

                        <!-- Archivos Adjuntos -->
                        <div class="md:col-span-2 mt-4 pt-4 border-t">
                            <label class="block text-lg font-medium text-gray-800 mb-4">Archivos Adjuntos</label>
                            
                            <ModelAttachmentsCreator 
                                v-if="!isEditing"
                                model-type="App\Models\Contract"
                                area-slug="rrhh"
                                v-model:files="form.files"
                                v-model:pendingFileIds="form.pending_file_ids"
                            />

                            <ModelAttachments 
                                v-else
                                :model-id="contract.id"
                                model-type="App\Models\Contract"
                                area-slug="rrhh"
                                :files="contract.files" 
                            />
                        </div>

                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                        <button
                            v-if="isEditing"
                            type="button"
                            @click="deleteContract"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors mr-auto"
                        >
                            Eliminar
                        </button>
                        <Link
                            :href="route('rrhh.contracts.index')"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50"
                        >
                            {{ isEditing ? 'Actualizar Contrato' : 'Guardar Contrato' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
