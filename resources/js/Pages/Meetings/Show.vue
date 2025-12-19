<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    meeting: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return 'No definida';
    const date = new Date(dateString);
     const offset = date.getTimezoneOffset();
    const adjustedDate = new Date(date.getTime() + offset * 60000);
    return adjustedDate.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const formatTime = (timeString) => {
    if (!timeString) return 'No definida';
     if (timeString.includes('T')) {
          const date = new Date(timeString);
          return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    }
    return timeString.substring(0, 5);
};

</script>

<template>
    <AppLayout title="Detalles de Reunión">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalles de Reunión
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Detalles de Reunión</h1>
                            <p class="text-gray-600 mt-1">Información completa de la reunión</p>
                        </div>
                        <div class="flex space-x-3">
                             <Link :href="route('meetings.edit', meeting.id)"
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-edit mr-2"></i>Editar
                            </Link>
                            <Link :href="route('meetings.index')"
                               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>Volver
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Información General</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                                    <p class="text-gray-900">#{{ meeting.id }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                                    <p class="text-gray-900">{{ meeting.title }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                                    <p class="text-gray-900">
                                        {{ formatDate(meeting.date) }}
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora</label>
                                    <p class="text-gray-900">
                                        <span v-if="meeting.start_time">{{ formatTime(meeting.start_time) }}</span>
                                        <span v-if="meeting.end_time"> - {{ formatTime(meeting.end_time) }}</span>
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Equipo</label>
                                    <p class="text-gray-900">
                                        {{ meeting.team ? meeting.team.name : 'No asignado' }}
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                    <span v-if="meeting.status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-blue-100 text-blue-800': meeting.status.name == 'Programada',
                                            'bg-green-100 text-green-800': meeting.status.name == 'Realizada',
                                            'bg-red-100 text-red-800': meeting.status.name == 'Cancelada',
                                            'bg-yellow-100 text-yellow-800': meeting.status.name == 'Postergada',
                                            'bg-purple-100 text-purple-800': meeting.status.name == 'En Progreso',
                                            'bg-gray-100 text-gray-800': !['Programada', 'Realizada', 'Cancelada', 'Postergada', 'En Progreso'].includes(meeting.status.name)
                                        }">
                                        {{ meeting.status.name }}
                                    </span>
                                    <span v-else class="text-sm text-gray-500">No definido</span>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">URL de la Reunión</label>
                                    <div v-if="meeting.url">
                                        <a :href="meeting.url" target="_blank" class="text-blue-600 hover:text-blue-900 text-sm flex items-center" title="Abrir URL">
                                            <i class="fas fa-external-link-alt mr-2"></i> {{ meeting.url }}
                                        </a>
                                    </div>
                                    <p v-else class="text-gray-500 text-sm">No definida</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Cumplida</label>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                          :class="meeting.goal ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                                        {{ meeting.goal ? 'Sí' : 'No' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div v-if="meeting.notes" class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Notas Previas</h2>
                            <div class="prose max-w-none">
                                <p class="text-gray-700 whitespace-pre-wrap">{{ meeting.notes }}</p>
                            </div>
                        </div>

                        <!-- Observations -->
                        <div v-if="meeting.observations" class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Observaciones Finales</h2>
                            <div class="prose max-w-none">
                                <p class="text-gray-700 whitespace-pre-wrap">{{ meeting.observations }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Responsibles -->
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Responsables</h2>
                            <div v-if="meeting.responsibles && meeting.responsibles.length > 0" class="space-y-2">
                                <div v-for="responsible in meeting.responsibles" :key="responsible.id" class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">
                                                {{ responsible.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ responsible.name }}</p>
                                        <p class="text-xs text-gray-500">{{ responsible.email }}</p>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-gray-500 text-sm">No hay responsables asignados</p>
                        </div>

                        <!-- Metadata -->
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Información de Registro</h2>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Creación</label>
                                    <p class="text-gray-900">{{ new Date(meeting.created_at).toLocaleString('es-ES') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Última Actualización</label>
                                    <p class="text-gray-900">{{ new Date(meeting.updated_at).toLocaleString('es-ES') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
