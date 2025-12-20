<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    worksite: Object,
    punchItem: Object,
});

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleString('es-ES');
};

const getStatusClass = (statusName) => {
    if (!statusName) return 'bg-blue-100 text-blue-800';
    if (statusName === 'Pendiente') return 'bg-gray-100 text-gray-800';
    if (statusName === 'En Progreso') return 'bg-yellow-100 text-yellow-800';
    if (statusName === 'Completado') return 'bg-green-100 text-green-800';
    if (statusName === 'Cancelado') return 'bg-red-100 text-red-800';
    return 'bg-blue-100 text-blue-800';
};
</script>

<template>
    <Head title="Detalle de Punch Item" />

    <AppLayout title="Detalle de Punch Item">
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Detalle de Punch Item
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Información completa del Punch Item
                    </p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('worksites.punch-items.edit', [worksite.id, punchItem.id])" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Editar Punch Item
                    </Link>
                    <Link :href="route('worksites.show', worksite.id)" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver a la Obra
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Punch Item Information -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Punch Item</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- ID del Punch Item -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">ID del Punch Item</label>
                            <p class="text-gray-900">#{{ punchItem.id }}</p>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                            <span v-if="punchItem.status" :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(punchItem.status.name)]">
                                {{ punchItem.status.name }}
                            </span>
                            <span v-else class="text-gray-500">-</span>
                        </div>

                        <!-- Responsable -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Responsable</label>
                            <p class="text-gray-900">{{ punchItem.responsible ? punchItem.responsible.name : '-' }}</p>
                        </div>

                        <!-- Observaciones -->
                        <div class="md:col-span-2 lg:col-span-3">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Observaciones</label>
                            <p class="text-gray-900">{{ punchItem.observations || '-' }}</p>
                        </div>

                        <!-- Fecha de Creación -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Creación</label>
                            <p class="text-gray-900">{{ formatDate(punchItem.created_at) }}</p>
                        </div>

                        <!-- Última Actualización -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Última Actualización</label>
                            <p class="text-gray-900">{{ formatDate(punchItem.updated_at) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Files Section -->
                <div v-if="punchItem.files && punchItem.files.length > 0" class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Archivos Adjuntos</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="file in punchItem.files" :key="file.id" class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <i class="fas fa-file text-gray-400 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-900 truncate">{{ file.name }}</span>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ formatDate(file.created_at) }}
                            </div>
                            <a :href="`/storage/${file.path}`" 
                               target="_blank"
                               class="mt-2 inline-block text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-download mr-1"></i> Descargar
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
