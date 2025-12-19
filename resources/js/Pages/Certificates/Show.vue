<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    certificate: Object,
});

const getStatusBadgeClass = (statusName) => {
    switch (statusName) {
        case 'Activo': return 'bg-green-100 text-green-800';
        case 'Inactivo': return 'bg-red-100 text-red-800';
        case 'Pendiente': return 'bg-yellow-100 text-yellow-800';
        case 'En Proceso': return 'bg-blue-100 text-blue-800';
        case 'Vencido': return 'bg-gray-100 text-gray-800';
        case 'Prorrogado': return 'bg-teal-100 text-teal-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'No especificada';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { timeZone: 'UTC' });
};

const isExpired = (dateString) => {
    if (!dateString) return false;
    const date = new Date(dateString);
    const now = new Date();
    return date < now;
};
</script>

<template>
    <AppLayout title="Detalles del Certificado">
        <main class="flex-1 p-10 bg-gray-100 font-sans">
            
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Detalles del Certificado</h1>
                        <p class="text-gray-600 mt-1">Información completa del certificado</p>
                    </div>
                    <div class="flex space-x-3">
                        <Link :href="route('certificates.index')" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </Link>
                        <Link :href="route('certificates.edit', certificate.id)" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center">
                            <i class="fas fa-edit mr-2"></i>Editar
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Certificate Details -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Información Básica</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">ID</label>
                                <p class="text-sm text-gray-900">#{{ certificate.id }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                                <p class="text-sm text-gray-900">{{ certificate.name }}</p>
                            </div>

                            <div v-if="certificate.description">
                                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                <p class="text-sm text-gray-900">{{ certificate.description }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tipo</label>
                                <span v-if="certificate.type" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ certificate.type.name }}
                                </span>
                                <p v-else class="text-sm text-gray-500">No especificado</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Estado</label>
                                <span v-if="certificate.status" :class="getStatusBadgeClass(certificate.status.name)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                    {{ certificate.status.name }}
                                </span>
                                <p v-else class="text-sm text-gray-500">No especificado</p>
                            </div>
                        </div>

                        <!-- Dates and Assignment -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Fechas y Asignación</h3>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha de Emisión</label>
                                <p class="text-sm text-gray-900">{{ formatDate(certificate.issued_at) }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                                <p class="text-sm text-gray-900">{{ formatDate(certificate.expires_at) }}</p>
                                <p v-if="isExpired(certificate.expires_at)" class="text-xs text-red-600 mt-1">Vencido</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Asignado a</label>
                                <div v-if="certificate.assigned_to">
                                    <p class="text-sm text-gray-900">{{ certificate.assigned_to.name }}</p>
                                    <p class="text-xs text-gray-500">{{ certificate.assigned_to.email }}</p>
                                </div>
                                <p v-else class="text-sm text-gray-500">No asignado</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha de Creación</label>
                                <p class="text-sm text-gray-900">{{ new Date(certificate.created_at).toLocaleDateString() }} {{ new Date(certificate.created_at).toLocaleTimeString() }}</p>
                            </div>

                             <div v-if="certificate.updated_at != certificate.created_at">
                                <label class="block text-sm font-medium text-gray-700">Última Actualización</label>
                                <p class="text-sm text-gray-900">{{ new Date(certificate.updated_at).toLocaleDateString() }} {{ new Date(certificate.updated_at).toLocaleTimeString() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Files Section -->
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Archivos Adjuntos</h2>
                         <div v-if="certificate.files && certificate.files.length > 0" class="space-y-3">
                            <div v-for="file in certificate.files" :key="file.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <a :href="'/storage/' + file.path" target="_blank" class="flex items-center space-x-3 truncate hover:underline">
                                    <i class="fas fa-file text-gray-400"></i>
                                    <span class="text-sm text-gray-700 truncate">{{ file.name }}</span>
                                </a>
                                <span class="text-xs text-gray-500">{{ new Date(file.created_at).toLocaleDateString() }}</span>
                            </div>
                         </div>
                         <div v-else class="text-sm text-gray-500 italic">No hay archivos adjuntos.</div>
                    </div>

                </div>
            </div>

        </main>
    </AppLayout>
</template>
