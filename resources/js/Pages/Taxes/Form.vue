<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MoneyInput from '@/Components/MoneyInput.vue'
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue'
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue'
import { computed } from 'vue'

const props = defineProps({
    taxRecord: Object,
    typeOptions: Array,
    statusOptions: Array,
})

const isEditing = computed(() => !!props.taxRecord)

const form = useForm({
    _method: isEditing.value ? 'POST' : 'POST',
    type_id: props.taxRecord?.type_id || '',
    status_id: props.taxRecord?.status_id || '',
    entity: props.taxRecord?.entity || '',
    base: props.taxRecord?.base || '',
    porcentage: props.taxRecord?.porcentage || '',
    amount: props.taxRecord?.amount || '',
    date: props.taxRecord?.date || new Date().toISOString().split('T')[0],
    observations: props.taxRecord?.observations || '',
    files: [],
    pending_file_ids: [],
})

const submit = () => {
    if (isEditing.value) {
        form.post(route('finances.taxes.update', props.taxRecord.id), {
            forceFormData: true,
        })
    } else {
        form.post(route('finances.taxes.store'), {
            forceFormData: true,
        })
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Impuesto' : 'Crear Impuesto'">
        <div class="max-w-4xl mx-auto p-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center border-b pb-4 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ isEditing ? 'Editar Impuesto' : 'Crear Impuesto' }}
                    </h2>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Entidad -->
                    <div>
                        <label for="entity" class="block text-sm font-medium text-gray-700 mb-2">
                            Entidad <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="entity"
                            v-model="form.entity"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Nombre de la entidad"
                        />
                        <span v-if="form.errors.entity" class="text-red-500 text-sm">{{ form.errors.entity }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tipo -->
                        <div>
                            <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                            <select
                                id="type_id"
                                v-model="form.type_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar</option>
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
                                <option value="">Seleccionar</option>
                                <option v-for="status in statusOptions" :key="status.id" :value="status.id">
                                    {{ status.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.status_id" class="text-red-500 text-sm">{{ form.errors.status_id }}</span>
                        </div>
                    </div>

                    <!-- Base -->
                    <MoneyInput
                        id="base"
                        v-model="form.base"
                        label="Base"
                        placeholder="$0.00"
                        :error="form.errors.base"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Porcentaje -->
                        <div>
                            <label for="porcentage" class="block text-sm font-medium text-gray-700 mb-2">
                                Porcentaje (%) <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="porcentage"
                                v-model="form.porcentage"
                                type="number"
                                min="0"
                                max="100"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                placeholder="0"
                            />
                            <span v-if="form.errors.porcentage" class="text-red-500 text-sm">{{ form.errors.porcentage }}</span>
                        </div>

                        <!-- Fecha -->
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="date"
                                v-model="form.date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.date" class="text-red-500 text-sm">{{ form.errors.date }}</span>
                        </div>
                    </div>

                    <!-- Monto -->
                    <MoneyInput
                        id="amount"
                        v-model="form.amount"
                        label="Monto"
                        placeholder="$0.00"
                        :error="form.errors.amount"
                    />

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
                                :model-id="taxRecord.id"
                                model-type="App\Models\TaxRecord"
                                area-slug="finanzas"
                                :files="taxRecord.files"
                            />
                        </div>

                        <!-- Create Mode -->
                        <div v-else>
                            <ModelAttachmentsCreator
                                model-type="App\Models\TaxRecord"
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
                            :href="route('finances.taxes.index')"
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
