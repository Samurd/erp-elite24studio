<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue'

const props = defineProps({
    worksite: Object,
    change: Object,
})

const deleteChange = () => {
    if (confirm('¿Estás seguro de que deseas eliminar este cambio?')) {
        router.delete(route('worksites.changes.destroy', [props.worksite.id, props.change.id]))
    }
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('es-CO')
}

const getStatusColor = (statusName) => {
    if (!statusName) return 'bg-gray-100 text-gray-800'
    statusName = statusName.toLowerCase()
    if (statusName.includes('aprobado')) return 'bg-green-100 text-green-800'
    if (statusName.includes('pendiente') || statusName.includes('revisión')) return 'bg-yellow-100 text-yellow-800'
    if (statusName.includes('rechazado')) return 'bg-red-100 text-red-800'
    return 'bg-gray-100 text-gray-800'
}
</script>

<template>
    <AppLayout title="Detalle del Cambio">
        <div class="max-w-4xl mx-auto p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Detalle del Cambio</h1>
                        <p class="text-gray-600 mt-1">Obra: {{ worksite.name }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <Link
                            :href="route('worksites.show', worksite.id)"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </Link>
                         <Link
                            :href="route('worksites.changes.edit', [worksite.id, change.id])"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <i class="fas fa-edit mr-2"></i>Editar
                        </Link>
                        <button
                            @click="deleteChange"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
                        >
                            <i class="fas fa-trash mr-2"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Fecha</label>
                        <p class="text-lg text-gray-900">{{ formatDate(change.change_date) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tipo</label>
                        <p class="text-lg text-gray-900">{{ change.type?.name || '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Solicitado Por</label>
                        <p class="text-lg text-gray-900">{{ change.requested_by || '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Impacto Presupuestal</label>
                        <p class="text-lg text-gray-900">{{ change.budget_impact?.name || '-' }}</p>
                    </div>
                    <div>
                         <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                         <p class="text-lg text-gray-900">
                             <span v-if="change.status" :class="['px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full', getStatusColor(change.status.name)]">
                                {{ change.status.name }}
                            </span>
                             <span v-else>-</span>
                         </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Aprobado Por</label>
                        <p class="text-lg text-gray-900">{{ change.approver?.name || '-' }}</p>
                    </div>
                     <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Descripción</label>
                        <div class="bg-gray-50 p-4 rounded-lg text-gray-900 whitespace-pre-wrap">{{ change.description }}</div>
                    </div>
                     <div class="md:col-span-2" v-if="change.internal_notes">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Notas Internas</label>
                         <div class="bg-yellow-50 p-4 rounded-lg text-gray-900 whitespace-pre-wrap">{{ change.internal_notes }}</div>
                    </div>
                </div>

                <!-- Archivos -->
                <div class="mt-8 pt-6 border-t">
                     <ModelAttachments 
                        :model-id="change.id"
                        model-type="App\Models\Change"
                        area-slug="obras"
                        :files="change.files" 
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
