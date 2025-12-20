<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    norm: Object,
})

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('es-CO')
}
</script>

<template>
    <AppLayout title="Detalle de Norma">
        <div class="max-w-4xl mx-auto p-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center border-b pb-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Detalle de Norma</h2>
                        <p class="text-gray-600 mt-1">ID: #{{ norm.id }}</p>
                    </div>
                    <div class="flex gap-3">
                        <Link
                            :href="route('finances.norms.edit', norm.id)"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <i class="fa-solid fa-pen-to-square mr-2"></i>Editar
                        </Link>
                        <Link
                            :href="route('finances.norms.index')"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Volver
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Nombre</label>
                        <p class="text-lg font-semibold text-gray-900">{{ norm.name }}</p>
                    </div>

                    <!-- Creado por -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Creado por</label>
                        <p class="text-lg text-gray-900">{{ norm.user?.name || 'N/A' }}</p>
                    </div>

                    <!-- Fecha de creación -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Creación</label>
                        <p class="text-lg text-gray-900">{{ formatDate(norm.created_at) }}</p>
                    </div>
                </div>

                <!-- Archivos Adjuntos -->
                <div class="mt-6 border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Archivos Adjuntos</h3>
                    <div v-if="norm.files && norm.files.length > 0" class="space-y-2">
                        <div
                            v-for="file in norm.files"
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
