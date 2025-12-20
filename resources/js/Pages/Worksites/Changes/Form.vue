<script setup>
import { useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue'
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue'
import { computed, ref } from 'vue'

const props = defineProps({
    worksite: Object,
    change: Object, // Null if creating
    users: Array,
    changeTypeOptions: Array,
    statusOptions: Array,
    budgetImpactOptions: Array,
})

const isEditing = computed(() => !!props.change)

const form = useForm({
    change_date: props.change?.change_date ? props.change.change_date.substring(0, 10) : new Date().toISOString().substring(0, 10),
    change_type_id: props.change?.change_type_id || '',
    requested_by: props.change?.requested_by || '',
    description: props.change?.description || '',
    budget_impact_id: props.change?.budget_impact_id || '',
    status_id: props.change?.status_id || '',
    approved_by: props.change?.approved_by || '',
    internal_notes: props.change?.internal_notes || '',
    files: [], // Array for new file objects
    pending_file_ids: [],
})

const submit = () => {
    if (isEditing.value) {
        form.post(route('worksites.changes.update', [props.worksite.id, props.change.id]), {
            forceFormData: true,
            _method: 'PUT',
        })
    } else {
        form.post(route('worksites.changes.store', props.worksite.id), {
            forceFormData: true,
        })
    }
}

const deleteChange = () => {
    if (confirm('¿Estás seguro de que deseas eliminar este cambio?')) {
        router.delete(route('worksites.changes.destroy', [props.worksite.id, props.change.id]))
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Cambio' : 'Nuevo Cambio'">
        <div class="max-w-4xl mx-auto p-6">
             <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEditing ? 'Editar Cambio' : 'Nuevo Cambio' }}
                        </h1>
                        <p class="text-gray-600 mt-1">Obra: {{ worksite.name }}</p>
                    </div>
                    <Link
                        :href="route('worksites.show', worksite.id)"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Fecha del Cambio -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha del Cambio <span class="text-red-500">*</span></label>
                            <input
                                v-model="form.change_date"
                                type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            />
                            <span v-if="form.errors.change_date" class="text-red-500 text-sm">{{ form.errors.change_date }}</span>
                        </div>

                        <!-- Tipo de Cambio -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Cambio <span class="text-red-500">*</span></label>
                            <select
                                v-model="form.change_type_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar tipo</option>
                                <option v-for="type in changeTypeOptions" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.change_type_id" class="text-red-500 text-sm">{{ form.errors.change_type_id }}</span>
                        </div>

                         <!-- Solicitado Por -->
                        <div class="md:col-span-2">
                             <label class="block text-sm font-medium text-gray-700 mb-2">Solicitado Por</label>
                             <input
                                v-model="form.requested_by"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                placeholder="Nombre del solicitante"
                            />
                             <span v-if="form.errors.requested_by" class="text-red-500 text-sm">{{ form.errors.requested_by }}</span>
                        </div>

                        <!-- Descripción -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Descripción <span class="text-red-500">*</span></label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                placeholder="Detalles del cambio..."
                            ></textarea>
                            <span v-if="form.errors.description" class="text-red-500 text-sm">{{ form.errors.description }}</span>
                        </div>

                        <!-- Impacto Presupuesto -->
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Impacto en Presupuesto</label>
                            <select
                                v-model="form.budget_impact_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar impacto</option>
                                <option v-for="impact in budgetImpactOptions" :key="impact.id" :value="impact.id">
                                    {{ impact.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.budget_impact_id" class="text-red-500 text-sm">{{ form.errors.budget_impact_id }}</span>
                        </div>

                        <!-- Estado -->
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

                        <!-- Aprobado Por -->
                        <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Aprobado Por</label>
                             <select
                                v-model="form.approved_by"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            >
                                <option value="">Seleccionar aprobador</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                             <span v-if="form.errors.approved_by" class="text-red-500 text-sm">{{ form.errors.approved_by }}</span>
                        </div>
                        
                         <!-- Notas Internas -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notas Internas</label>
                            <textarea
                                v-model="form.internal_notes"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                placeholder="Notas internas..."
                            ></textarea>
                            <span v-if="form.errors.internal_notes" class="text-red-500 text-sm">{{ form.errors.internal_notes }}</span>
                        </div>

                        <!-- Archivos Adjuntos -->
                        <div class="md:col-span-2 mt-4 pt-4 border-t">
                            <label class="block text-lg font-medium text-gray-800 mb-4">Archivos Adjuntos</label>
                            
                            <ModelAttachmentsCreator 
                                v-if="!isEditing"
                                model-type="App\Models\Change"
                                area-slug="obras"
                                v-model:files="form.files"
                                v-model:pendingFileIds="form.pending_file_ids"
                            />

                            <ModelAttachments 
                                v-else
                                :model-id="change.id"
                                model-type="App\Models\Change"
                                area-slug="obras"
                                :files="change.files" 
                            />
                        </div>

                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                        <button
                            v-if="isEditing"
                            type="button"
                            @click="deleteChange"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors mr-auto"
                        >
                            Eliminar
                        </button>
                        <Link
                            :href="route('worksites.show', worksite.id)"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50"
                        >
                            {{ isEditing ? 'Actualizar Cambio' : 'Guardar Cambio' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
