<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    worksite: Object,
    visit: Object,
});

const deleteVisit = () => {
    if (confirm('¿Estás seguro de que quieres eliminar esta visita?')) {
        router.delete(route('worksites.visits.destroy', [props.worksite.id, props.visit.id]));
    }
};

const formatDate = (date) => {
    if (!date) return '-';
    // Using UTC to avoid timezone issues if needed, or local
    // Assuming backend sends YYYY-MM-DD or ISO
    return new Date(date).toLocaleDateString('es-ES');
};

const formatDateTime = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleString('es-ES');
};
</script>

<template>
    <Head title="Detalle de Visita" />

    <AppLayout title="Detalle de Visita">
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Detalle de Visita
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Información completa de la visita
                    </p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('worksites.visits.edit', [worksite.id, visit.id])" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Editar Visita
                    </Link>
                    <Link :href="route('worksites.show', worksite.id)" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver a la Obra
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Información de la Visita</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- ID de la Visita -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">ID de Visita</label>
                            <p class="text-gray-900">#{{ visit.id }}</p>
                        </div>

                        <!-- Obra -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Obra</label>
                            <p class="text-gray-900">{{ worksite.name }}</p>
                        </div>

                        <!-- Fecha de Visita -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Visita</label>
                            <p class="text-gray-900">{{ formatDate(visit.visit_date) }}</p>
                        </div>

                        <!-- Visitante -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Visitante</label>
                            <p class="text-gray-900">{{ visit.visitor ? visit.visitor.name : '-' }}</p>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                            <span v-if="visit.status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="{
                                    'bg-blue-100 text-blue-800': visit.status.name == 'Programada',
                                    'bg-green-100 text-green-800': visit.status.name == 'Realizada',
                                    'bg-red-100 text-red-800': visit.status.name == 'Cancelada',
                                    'bg-yellow-100 text-yellow-800': visit.status.name == 'Postergada',
                                    'bg-gray-100 text-gray-800': !['Programada', 'Realizada', 'Cancelada', 'Postergada'].includes(visit.status.name)
                                }">
                                {{ visit.status.name }}
                            </span>
                            <span v-else class="text-gray-500">-</span>
                        </div>

                        <!-- Fecha de Creación -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Creación</label>
                            <p class="text-gray-900">{{ formatDateTime(visit.created_at) }}</p>
                        </div>

                        <!-- Observaciones Generales -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Observaciones Generales</label>
                            <p class="text-gray-900">{{ visit.general_observations || '-' }}</p>
                        </div>

                        <!-- Notas Internas -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Notas Internas</label>
                            <p class="text-gray-900">{{ visit.internal_notes || '-' }}</p>
                        </div>

                        <!-- Última Actualización -->
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Última Actualización</label>
                            <p class="text-gray-900">{{ formatDateTime(visit.updated_at) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-end space-x-3">
                        <Link :href="route('worksites.visits.edit', [worksite.id, visit.id])" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Editar Visita
                        </Link>
                        <DangerButton @click="deleteVisit">
                            <i class="fas fa-trash mr-2"></i>Eliminar Visita
                        </DangerButton>
                        <Link :href="route('worksites.show', worksite.id)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Volver a la Obra
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
