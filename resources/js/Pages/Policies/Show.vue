<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    policy: Object,
});

const getStatusBadgeClass = (statusName) => {
    switch (statusName) {
        case 'Activa': return 'bg-green-100 text-green-800';
        case 'En Revisión': return 'bg-yellow-100 text-yellow-800';
        case 'Vencida': return 'bg-red-100 text-red-800';
        case 'Suspendida': return 'bg-gray-100 text-gray-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    // UTC handling
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { timeZone: 'UTC' });
};

</script>

<template>
    <AppLayout title="Detalles de Política">
        <main class="flex-1 p-10 bg-gray-100 font-sans">
            
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Detalles de Política</h1>
                        <p class="text-gray-600 mt-1">Información completa de la política</p>
                    </div>
                    <div class="flex space-x-3">
                        <Link :href="route('policies.edit', policy.id)" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                            <i class="fas fa-edit mr-2"></i>Editar
                        </Link>
                        <Link :href="route('policies.index')" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </Link>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Main Information -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Información General</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                                <p class="text-gray-900">#{{ policy.id }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                <p class="text-gray-900">{{ policy.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                                <span v-if="policy.type" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ policy.type.name }}
                                </span>
                                <span v-else class="text-gray-500">-</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <span v-if="policy.status" :class="getStatusBadgeClass(policy.status.name)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                    {{ policy.status.name }}
                                </span>
                                <span v-else class="text-gray-500">-</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                                <p class="text-gray-900">{{ policy.assigned_to ? policy.assigned_to.name : '-' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Emisión</label>
                                <p class="text-gray-900">{{ formatDate(policy.issued_at) }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Última Revisión</label>
                                <p class="text-gray-900">{{ formatDate(policy.reviewed_at) }}</p>
                            </div>
                        </div>

                         <!-- URL (if exists) -->
                         <div v-if="policy.url" class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL de la Política</label>
                            <a :href="policy.url" target="_blank" class="text-blue-600 hover:text-blue-900 text-sm flex items-center" title="Abrir URL de la política">
                                <i class="fas fa-external-link-alt mr-2"></i> {{ policy.url }}
                            </a>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="policy.description" class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Descripción</h2>
                        <div class="prose max-w-none">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ policy.description }}</p>
                        </div>
                    </div>

                     <!-- Content (if exists in model, though form didn't show it, Show blade did) -->
                    <div v-if="policy.content" class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Contenido</h2>
                        <div class="prose max-w-none">
                            <div class="text-gray-700 whitespace-pre-wrap">{{ policy.content }}</div>
                        </div>
                    </div>

                </div>

                 <!-- Sidebar -->
                <div class="lg:col-span-1">
                    
                    <!-- Files Section (Replicating the look of model-files but with Vue logic) -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Archivos Adjuntos</h2>
                         <div v-if="policy.files && policy.files.length > 0" class="space-y-3">
                            <div v-for="file in policy.files" :key="file.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <a :href="'/storage/' + file.path" target="_blank" class="flex items-center space-x-3 truncate">
                                    <i class="fas fa-file text-gray-400"></i>
                                    <span class="text-sm text-gray-700 truncate">{{ file.name }}</span>
                                </a>
                            </div>
                         </div>
                         <div v-else class="text-sm text-gray-500 italic">No hay archivos adjuntos.</div>
                    </div>

                    <!-- Metadata -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Información de Registro</h2>
                         <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Creación</label>
                                <p class="text-gray-900">{{ new Date(policy.created_at).toLocaleDateString() }} {{ new Date(policy.created_at).toLocaleTimeString() }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Última Actualización</label>
                                <p class="text-gray-900">{{ new Date(policy.updated_at).toLocaleDateString() }} {{ new Date(policy.updated_at).toLocaleTimeString() }}</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </main>
    </AppLayout>
</template>
