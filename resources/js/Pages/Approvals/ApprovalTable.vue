<script setup>
import { Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    approvals: Object,
    isSent: Boolean
});

const emit = defineEmits(['view', 'remove']);

const getPriorityIcon = (name) => {
    if (name === 'Alta') return '❗';
    if (name === 'Media') return '⚠️';
    return '🟢';
};

const getStatusClass = (name) => {
    if(name == 'Aprobado') return 'bg-green-100 text-green-800';
    if(name == 'Rechazado') return 'bg-red-100 text-red-800';
    if(name == 'En espera') return 'bg-yellow-100 text-yellow-800';
    if(name == 'Cancelado') return 'bg-gray-100 text-gray-700';
    return 'bg-gray-100 text-gray-600';
};
</script>

<template>
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-gray-700 font-semibold">
                <tr>
                    <th class="px-4 py-3 text-left">Prioridad</th>
                    <th class="px-4 py-3 text-left">Título de la solicitud</th>
                    <th class="px-4 py-3 text-left">Estado</th>
                    <th class="px-4 py-3 text-left">Origen</th>
                    <th class="px-4 py-3 text-left">Creado</th>
                    <th class="px-4 py-3 text-left">Enviado por</th>
                    <!-- <th class="px-4 py-3 text-left">Enviado a</th> -->
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr v-for="approval in approvals.data" :key="approval.id" class="hover:bg-gray-50 transition duration-150">
                    <td class="px-4 py-3">
                        <span :title="approval.priority ? approval.priority.name : ''">
                             {{ getPriorityIcon(approval.priority ? approval.priority.name : '') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ approval.name }}</td>
                    <td class="px-4 py-3">
                         <span :class="['px-2 py-1 rounded text-xs font-semibold', getStatusClass(approval.status ? approval.status.name : '')]">
                            {{ approval.status ? approval.status.name : 'N/A' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 text-amber-800 rounded text-xs font-medium">
                            {{ approval.buy ? 'Solicitud de Compra' : 'Aprobación' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ new Date(approval.created_at).toLocaleString() }}
                    </td>
                    <td class="px-4 py-3 text-gray-700">
                         <div class="flex items-center gap-2">
                             <!-- Avatar placeholder -->
                             <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs border border-blue-200 font-bold">
                                {{ approval.creator ? approval.creator.name.charAt(0) : '?' }}
                            </div>
                            <span>{{ approval.creator ? approval.creator.name : 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button @click="$emit('view', approval.id)" class="text-blue-600 hover:text-blue-900 text-xs font-bold uppercase mr-2">
                            Ver
                        </button>
                        <Link v-if="isSent" :href="route('approvals.edit', approval.id)" class="text-yellow-600 hover:text-yellow-900 text-xs font-bold uppercase mr-2">
                            Editar
                        </Link>
                        <button v-if="isSent" @click="$emit('remove', approval.id)" class="text-red-600 hover:text-red-900 text-xs font-bold uppercase">
                            Eliminar
                        </button>
                    </td>
                </tr>
                 <tr v-if="approvals.data.length === 0">
                    <td colspan="7" class="px-6 py-4 text-center text-gray-400">No hay resultados</td>
                </tr>
            </tbody>
        </table>
         <div class="p-3 border-t border-gray-200">
             <Pagination :links="approvals.links" />
        </div>
    </div>
</template>
