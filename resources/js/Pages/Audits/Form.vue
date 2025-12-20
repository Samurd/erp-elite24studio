<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue'
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue'
import { computed } from 'vue'

const props = defineProps({
    audit: Object,
    typeOptions: Array,
    statusOptions: Array,
})

const isEditing = computed(() => !!props.audit)

const form = useForm({
    _method: isEditing.value ? 'POST' : 'POST',
    date_register: props.audit?.date_register || new Date().toISOString().split('T')[0],
    date_audit: props.audit?.date_audit || new Date().toISOString().split('T')[0],
    objective: props.audit?.objective || '',
    type_id: props.audit?.type_id || '',
    place: props.audit?.place || '',
    status_id: props.audit?.status_id || '',
    observations: props.audit?.observations || '',
    files: [],
    pending_file_ids: [],
})

const submit = () => {
    if (isEditing.value) {
        form.post(route('finances.audits.update', props.audit.id), {
            forceFormData: true,
        })
    } else {
        form.post(route('finances.audits.store'), {
            forceFormData: true,
        })
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Auditoría' : 'Crear Auditoría'">
        <div class="max-w-4xl mx-auto p-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center border-b pb-4 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ isEditing ? 'Editar Auditoría' : 'Crear Auditoría' }}
                    </h2>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Fecha de Registro -->
                        <div>
                            <label for="date_register" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Registro <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="date_register"
                                v-model="form.date_register"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.date_register" class="text-red-500 text-sm">{{ form.errors.date_register }}</span>
                        </div>

                        <!-- Fecha de Auditoría -->
                        <div>
                            <label for="date_audit" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Auditoría <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="date_audit"
                                v-model="form.date_audit"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.date_audit" class="text-red-500 text-sm">{{ form.errors.date_audit }}</span>
                        </div>
                    </div>

                    <!-- Objetivo -->
                    <div>
                        <label for="objective" class="block text-sm font-medium text-gray-700 mb-2">
                            Objetivo <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="objective"
                            v-model="form.objective"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Objetivo numérico"
                        />
                        <span v-if="form.errors.objective" class="text-red-500 text-sm">{{ form.errors.objective }}</span>
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

                    <!-- Lugar -->
                    <div>
                        <label for="place" class="block text-sm font-medium text-gray-700 mb-2">
                            Lugar <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="place"
                            v-model="form.place"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Lugar de la auditoría"
                        />
                        <span v-if="form.errors.place" class="text-red-500 text-sm">{{ form.errors.place }}</span>
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
                                :model-id="audit.id"
                                model-type="App\Models\Audit"
                                area-slug="finanzas"
                                :files="audit.files"
                            />
                        </div>

                        <!-- Create Mode -->
                        <div v-else>
                            <ModelAttachmentsCreator
                                model-type="App\Models\Audit"
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
                            :href="route('finances.audits.index')"
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
