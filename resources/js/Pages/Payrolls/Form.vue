<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MoneyInput from '@/Components/MoneyInput.vue'
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue'
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue'
import { computed, watch } from 'vue'

const props = defineProps({
    payroll: Object,
    employees: Array,
    statusOptions: Array,
})

const isEditing = computed(() => !!props.payroll)

const form = useForm({
    _method: isEditing.value ? 'POST' : 'POST',
    employee_id: props.payroll?.employee_id || '',
    subtotal: props.payroll?.subtotal || '',
    bonos: props.payroll?.bonos || '',
    deductions: props.payroll?.deductions || '',
    total: props.payroll?.total || '',
    status_id: props.payroll?.status_id || '',
    observations: props.payroll?.observations || '',
    files: [],
    pending_file_ids: [],
})

// Auto-calculate total
watch([() => form.subtotal, () => form.bonos, () => form.deductions], () => {
    const subtotal = parseInt(form.subtotal) || 0
    const bonos = parseInt(form.bonos) || 0
    const deductions = parseInt(form.deductions) || 0
    form.total = subtotal + bonos - deductions
})

const submit = () => {
    if (isEditing.value) {
        form.post(route('finances.payrolls.update', props.payroll.id), {
            forceFormData: true,
        })
    } else {
        form.post(route('finances.payrolls.store'), {
            forceFormData: true,
        })
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Nómina' : 'Crear Nómina'">
        <div class="max-w-4xl mx-auto p-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center border-b pb-4 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ isEditing ? 'Editar Nómina' : 'Crear Nómina' }}
                    </h2>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Empleado -->
                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Empleado</label>
                        <select
                            id="employee_id"
                            v-model="form.employee_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        >
                            <option value="">Seleccionar</option>
                            <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                                {{ employee.full_name }}
                            </option>
                        </select>
                        <span v-if="form.errors.employee_id" class="text-red-500 text-sm">{{ form.errors.employee_id }}</span>
                    </div>

                    <!-- Subtotal -->
                    <MoneyInput
                        id="subtotal"
                        v-model="form.subtotal"
                        label="Subtotal"
                        placeholder="$0.00"
                        :error="form.errors.subtotal"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Bonos -->
                        <MoneyInput
                            id="bonos"
                            v-model="form.bonos"
                            label="Bonos"
                            placeholder="$0.00"
                            :error="form.errors.bonos"
                        />

                        <!-- Deducciones -->
                        <MoneyInput
                            id="deductions"
                            v-model="form.deductions"
                            label="Deducciones"
                            placeholder="$0.00"
                            :error="form.errors.deductions"
                        />
                    </div>

                    <!-- Total (readonly, auto-calculated) -->
                    <div>
                        <label for="total" class="block text-sm font-medium text-gray-700 mb-2">Total</label>
                        <input
                            id="total"
                            :value="new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(form.total / 100)"
                            type="text"
                            readonly
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-semibold"
                        />
                    </div>

                    <!-- Estado -->
                    <div>
                        <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select
                            id="status_id"
                            v-model="form.status_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        >
                            <option value="">Seleccionar</option>
                            <option v-for="status in statusOptions" :key="status.id" :value="status.id">
                                {{ status.name }}
                            </option>
                        </select>
                        <span v-if="form.errors.status_id" class="text-red-500 text-sm">{{ form.errors.status_id }}</span>
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                        <textarea
                            id="observations"
                            v-model="form.observations"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Agregue sus observaciones..."
                        ></textarea>
                        <span v-if="form.errors.observations" class="text-red-500 text-sm">{{ form.errors.observations }}</span>
                    </div>

                    <!-- Archivos adjuntos -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Archivos Adjuntos</h3>
                        
                        <!-- Edit Mode -->
                        <div v-if="isEditing">
                            <ModelAttachments
                                :model-id="payroll.id"
                                model-type="App\Models\Payroll"
                                area-slug="finanzas"
                                :files="payroll.files"
                            />
                        </div>

                        <!-- Create Mode -->
                        <div v-else>
                            <ModelAttachmentsCreator
                                model-type="App\Models\Payroll"
                                area-slug="finanzas"
                                v-model:files="form.files"
                                v-model:pendingFileIds="form.pending_file_ids"
                            />
                            <p v-if="form.errors.files" class="text-red-500 text-sm mt-1">{{ form.errors.files }}</p>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 border-t pt-6">
                        <Link
                            :href="route('finances.payrolls.index')"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50"
                        >
                            {{ isEditing ? 'Actualizar' : 'Crear' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
