<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue'

const props = defineProps({
    contract: Object,
})

const deleteContract = () => {
    if (confirm('¿Estás seguro de que deseas eliminar este contrato?')) {
        router.delete(route('rrhh.contracts.destroy', props.contract.id))
    }
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('es-CO')
}

const formatCurrency = (amount) => {
    if (!amount) return '-'
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(amount)
}

const getStatusColor = (statusName) => {
    if (!statusName) return 'bg-gray-100 text-gray-800'
    const name = statusName.toLowerCase()
    if (name.includes('activo') || name.includes('vigente')) return 'bg-green-100 text-green-800'
    if (name.includes('inactivo') || name.includes('terminado') || name.includes('finalizado')) return 'bg-red-100 text-red-800'
    return 'bg-gray-100 text-gray-800'
}
</script>

<template>
    <AppLayout title="Detalle del Contrato">
        <div class="max-w-4xl mx-auto p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Detalle del Contrato</h1>
                        <p class="text-gray-600 mt-1">Empleado: {{ contract.employee?.full_name }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <Link
                            :href="route('rrhh.contracts.index')"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </Link>
                         <Link
                            :href="route('rrhh.contracts.edit', contract.id)"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <i class="fas fa-edit mr-2"></i>Editar
                        </Link>
                        <button
                            @click="deleteContract"
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
                    
                    <!-- Basic Info -->
                     <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg shadow-sm">
                         <i class="fas fa-user text-blue-500 text-xl"></i>
                         <div>
                             <p class="text-sm text-gray-500">Empleado</p>
                             <p class="font-semibold text-gray-800">{{ contract.employee?.full_name || '-' }}</p>
                         </div>
                     </div>

                     <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg shadow-sm">
                         <i class="fas fa-id-card text-blue-400 text-xl"></i>
                         <div>
                             <p class="text-sm text-gray-500">Identificación</p>
                             <p class="font-semibold text-gray-800">{{ contract.employee?.identification_number || '-' }}</p>
                         </div>
                     </div>

                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg shadow-sm">
                         <i class="fas fa-file-contract text-purple-500 text-xl"></i>
                         <div>
                             <p class="text-sm text-gray-500">Tipo de Contrato</p>
                             <p class="font-semibold text-gray-800">{{ contract.type?.name || '-' }}</p>
                         </div>
                     </div>

                     <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg shadow-sm">
                         <i class="fas fa-tags text-pink-500 text-xl"></i>
                         <div>
                             <p class="text-sm text-gray-500">Categoría</p>
                             <p class="font-semibold text-gray-800">{{ contract.category?.name || '-' }}</p>
                         </div>
                     </div>

                     <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg shadow-sm">
                         <i class="fas fa-calendar-alt text-yellow-500 text-xl"></i>
                         <div>
                             <p class="text-sm text-gray-500">Fecha Inicio</p>
                             <p class="font-semibold text-gray-800">{{ formatDate(contract.start_date) }}</p>
                         </div>
                     </div>

                     <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg shadow-sm">
                         <i class="fas fa-calendar-check text-red-500 text-xl"></i>
                         <div>
                             <p class="text-sm text-gray-500">Fecha Fin</p>
                             <p class="font-semibold text-gray-800">{{ formatDate(contract.end_date) }}</p>
                         </div>
                     </div>

                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg shadow-sm">
                         <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                         <div>
                             <p class="text-sm text-gray-500">Salario</p>
                             <p class="font-semibold text-gray-800">{{ formatCurrency(contract.amount) }}</p>
                         </div>
                     </div>

                     <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg shadow-sm">
                         <i class="fas fa-clock text-indigo-500 text-xl"></i>
                         <div>
                             <p class="text-sm text-gray-500">Horario</p>
                             <p class="font-semibold text-gray-800">{{ contract.schedule || '-' }}</p>
                         </div>
                     </div>

                     <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg shadow-sm">
                         <i class="fas fa-info-circle text-orange-500 text-xl"></i>
                         <div>
                             <p class="text-sm text-gray-500">Estado</p>
                             <span :class="['px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusColor(contract.status?.name)]">
                                {{ contract.status?.name || '-' }}
                            </span>
                         </div>
                     </div>

                     <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg shadow-sm">
                         <i class="fas fa-user-edit text-gray-500 text-xl"></i>
                         <div>
                             <p class="text-sm text-gray-500">Registrado Por</p>
                             <p class="font-semibold text-gray-800">{{ contract.registeredBy?.name || '-' }}</p>
                         </div>
                     </div>

                </div>

                <!-- Archivos -->
                <div class="mt-8 pt-6 border-t">
                     <ModelAttachments 
                        :model-id="contract.id"
                        model-type="App\Models\Contract"
                        area-slug="rrhh"
                        :files="contract.files" 
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
