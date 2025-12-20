<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue'
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue'
import { computed } from 'vue'

const props = defineProps({
    norm: Object,
})

const isEditing = computed(() => !!props.norm)

const form = useForm({
    _method: isEditing.value ? 'POST' : 'POST',
    name: props.norm?.name || '',
    files: [],
    pending_file_ids: [],
})

const submit = () => {
    if (isEditing.value) {
        form.post(route('finances.norms.update', props.norm.id), {
            forceFormData: true,
        })
    } else {
        form.post(route('finances.norms.store'), {
            forceFormData: true,
        })
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Norma' : 'Crear Norma'">
        <div class="max-w-4xl mx-auto p-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center border-b pb-4 mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ isEditing ? 'Editar Norma' : 'Crear Norma' }}
                    </h2>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Nombre de la norma"
                        />
                        <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                    </div>

                    <!-- Archivos adjuntos -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold mb-4">Archivos Adjuntos</h3>
                        
                        <!-- Edit Mode -->
                        <div v-if="isEditing">
                            <ModelAttachments
                                :model-id="norm.id"
                                model-type="App\Models\Norm"
                                area-slug="finanzas"
                                :files="norm.files"
                            />
                        </div>

                        <!-- Create Mode -->
                        <div v-else>
                            <ModelAttachmentsCreator
                                model-type="App\Models\Norm"
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
                            :href="route('finances.norms.index')"
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
