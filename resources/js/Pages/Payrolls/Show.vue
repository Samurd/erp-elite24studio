<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    payroll: Object,
})

const formatMoney = (amount) => {
    const formatted = new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount / 100)
    return `$${formatted}`
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('es-CO')
}
</script>

<template>
    <AppLayout title="Detalle de Nómina">
        <div class="max-w-4xl mx-auto p-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center border-b pb-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Detalle de Nómina</h2>
                        <p class="text-gray-600 mt-1">ID: #{{ payroll.id }}</p>
                    </div>
                    <div class="flex gap-3">
                        <Link
                            :href="route('finances.payrolls.edit', payroll.id)"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <i class="fa-solid fa-pen-to-square mr-2"></i>Editar
                        </Link>
                        <Link
                            :href="route('finances.payrolls.index')"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Volver
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Empleado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Empleado</label>
                        <p class="text-lg font-semibold text-gray-900">{{ payroll.employee?.full_name || 'Sin empleado' }}</p>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                        <span v-if="payroll.status" class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ payroll.status.name }}
                        </span>
                        <p v-else class="text-gray-400">-</p>
                    </div>

                    <!-- Subtotal -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Subtotal</label>
                        <p class="text-lg font-semibold text-gray-900">{{ formatMoney(payroll.subtotal) }}</p>
                    </div>

                    <!-- Bonos -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Bonos</label>
                        <p class="text-lg font-semibold text-green-600">{{ formatMoney(payroll.bonos || 0) }}</p>
                    </div>

                    <!-- Deducciones -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Deducciones</label>
                        <p class="text-lg font-semibold text-red-600">{{ formatMoney(payroll.deductions || 0) }}</p>
                    </div>

                    <!-- Total -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Total Neto</label>
                        <p class="text-2xl font-bold text-yellow-600">{{ formatMoney(payroll.total) }}</p>
                    </div>

                    <!-- Fecha -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Creación</label>
                        <p class="text-lg text-gray-900">{{ formatDate(payroll.created_at) }}</p>
                    </div>
                </div>

                <!-- Observaciones -->
                <div v-if="payroll.observations" class="mt-6 border-t pt-6">
                    <label class="block text-sm font-medium text-gray-500 mb-2">Observaciones</label>
                    <p class="text-gray-900 whitespace-pre-wrap">{{ payroll.observations }}</p>
                </div>

                <!-- Archivos Adjuntos -->
                <div class="mt-6 border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Archivos Adjuntos</h3>
                    <div v-if="payroll.files && payroll.files.length > 0" class="space-y-2">
                        <div
                            v-for="file in payroll.files"
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
