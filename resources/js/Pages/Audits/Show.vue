<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    audit: Object,
})

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('es-CO')
}
</script>

<template>
    <AppLayout title="Detalle de Auditoría">
        <div class="max-w-4xl mx-auto p-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center border-b pb-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Detalle de Auditoría</h2>
                        <p class="text-gray-600 mt-1">ID: #{{ audit.id }}</p>
                    </div>
                    <div class="flex gap-3">
                        <Link
                            :href="route('finances.audits.edit', audit.id)"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <i class="fa-solid fa-pen-to-square mr-2"></i>Editar
                        </Link>
                        <Link
                            :href="route('finances.audits.index')"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Volver
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Objetivo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Objetivo</label>
                        <p class="text-lg font-semibold text-gray-900">{{ audit.objective }}</p>
                    </div>

                    <!-- Lugar -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Lugar</label>
                        <p class="text-lg text-gray-900">{{ audit.place }}</p>
                    </div>

                    <!-- Tipo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tipo</label>
                        <span v-if="audit.type" class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                            {{ audit.type.name }}
                        </span>
                        <p v-else class="text-gray-400">-</p>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                        <span v-if="audit.status" class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ audit.status.name }}
                        </span>
                        <p v-else class="text-gray-400">-</p>
                    </div>

                    <!-- Fecha de Registro -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Registro</label>
                        <p class="text-lg text-gray-900">{{ formatDate(audit.date_register) }}</p>
                    </div>

                    <!-- Fecha de Auditoría -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Auditoría</label>
                        <p class="text-lg text-gray-900">{{ formatDate(audit.date_audit) }}</p>
                    </div>
                </div>

                <!-- Observaciones -->
                <div v-if="audit.observations" class="mt-6 border-t pt-6">
                    <label class="block text-sm font-medium text-gray-500 mb-2">Observaciones</label>
                    <p class="text-gray-900 whitespace-pre-wrap">{{ audit.observations }}</p>
                </div>

                <!-- Archivos Adjuntos -->
                <div class="mt-6 border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Archivos Adjuntos</h3>
                    <div v-if="audit.files && audit.files.length > 0" class="space-y-2">
                        <div
                            v-for="file in audit.files"
                            :key="file.id"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                        >
                            <div class="flex items-center">
                                <i class="fa-solid fa-file mr-3 text-gray-400"></i>
                                <span class="text-sm text-gray-900">{{ file.name }}</span>
                            </div>
                            <a
                                :href="file.url"
                                target="_blank"
                                class="text-blue-600 hover:text-blue-800 text-sm"
                            >
                                <i class="fa-solid fa-download mr-1"></i>Descargar
                            </a>
                        </div>
                    </div>
                    <p v-else class="text-gray-500 text-sm">No hay archivos adjuntos</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
