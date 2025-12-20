<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MoneyInput from '@/Components/MoneyInput.vue'
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue'
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue'
import { computed } from 'vue'

const props = defineProps({
    invoice: Object,
    providerContacts: Array,
    statusOptions: Array,
    generatedCode: String,
})

const isEditing = computed(() => !!props.invoice)

const form = useForm({
    _method: isEditing.value ? 'POST' : 'POST',
    invoice_date: props.invoice?.invoice_date || new Date().toISOString().split('T')[0],
    code: props.invoice?.code || props.generatedCode || '',
    contact_id: props.invoice?.contact_id || '',
    description: props.invoice?.description || '',
    total_amount: props.invoice?.total_amount || '',
    method_payment: props.invoice?.method_payment || '',
    status_id: props.invoice?.status_id || '',
    files: [],
    pending_file_ids: [],
})

const submit = () => {
    if (isEditing.value) {
        form.post(route('finances.invoices.providers.update', props.invoice.id), {
            forceFormData: true,
        })
    } else {
        form.post(route('finances.invoices.providers.store'), {
            forceFormData: true,
        })
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Factura de Proveedor' : 'Crear Factura de Proveedor'">
        <div class="max-w-4xl mx-auto p-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center border-b pb-4 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ isEditing ? 'Editar Factura de Proveedor' : 'Crear Factura de Proveedor' }}
                    </h2>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Fecha de Factura -->
                        <div>
                            <label for="invoice_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Factura <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="invoice_date"
                                v-model="form.invoice_date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.invoice_date" class="text-red-500 text-sm">{{ form.errors.invoice_date }}</span>
                        </div>

                        <!-- Código -->
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                                Código <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="code"
                                v-model="form.code"
                                type="text"
                                readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 font-mono"
                            />
                            <span v-if="form.errors.code" class="text-red-500 text-sm">{{ form.errors.code }}</span>
                        </div>
                    </div>

                    <!-- Proveedor -->
                    <div>
                        <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Proveedor <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="contact_id"
                            v-model="form.contact_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        >
                            <option value="">Seleccionar proveedor</option>
                            <option v-for="contact in providerContacts" :key="contact.id" :value="contact.id">
                                {{ contact.name }}
                            </option>
                        </select>
                        <span v-if="form.errors.contact_id" class="text-red-500 text-sm">{{ form.errors.contact_id }}</span>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Descripción de la factura..."
                        ></textarea>
                        <span v-if="form.errors.description" class="text-red-500 text-sm">{{ form.errors.description }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Monto Total -->
                        <MoneyInput
                            id="total_amount"
                            v-model="form.total_amount"
                            label="Monto Total"
                            placeholder="$0.00"
                            :error="form.errors.total_amount"
                            required
                        />

                        <!-- Método de Pago -->
                        <div>
                            <label for="method_payment" class="block text-sm font-medium text-gray-700 mb-2">Método de Pago</label>
                            <input
                                id="method_payment"
                                v-model="form.method_payment"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                placeholder="Ej: Transferencia, Efectivo..."
                            />
                            <span v-if="form.errors.method_payment" class="text-red-500 text-sm">{{ form.errors.method_payment }}</span>
                        </div>
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

                    <!-- Archivos adjuntos -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Archivos Adjuntos</h3>
                        
                        <!-- Edit Mode -->
                        <div v-if="isEditing">
                            <ModelAttachments
                                :model-id="invoice.id"
                                model-type="App\Models\Invoice"
                                area-slug="finanzas"
                                :files="invoice.files"
                            />
                        </div>

                        <!-- Create Mode -->
                        <div v-else>
                            <ModelAttachmentsCreator
                                model-type="App\Models\Invoice"
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
                            :href="route('finances.invoices.providers.index')"
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
