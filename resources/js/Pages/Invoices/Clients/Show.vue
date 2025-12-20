<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    invoice: Object,
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
    <AppLayout title="Detalle de Factura DIAN">
        <div class="max-w-4xl mx-auto p-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center border-b pb-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Detalle de Factura DIAN</h2>
                        <p class="text-gray-600 mt-1">{{ invoice.code }}</p>
                    </div>
                    <Link
                        :href="route('finances.invoices.clients.index')"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                    >
                        Volver
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Código -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Código</label>
                        <p class="text-lg font-semibold text-gray-900">{{ invoice.code }}</p>
                    </div>

                    <!-- Cliente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Cliente</label>
                        <p class="text-lg text-gray-900">{{ invoice.contact?.name || '-' }}</p>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                        <span v-if="invoice.status" class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ invoice.status.name }}
                        </span>
                        <p v-else class="text-gray-400">-</p>
                    </div>

                    <!-- Monto Total -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Monto Total</label>
                        <p class="text-2xl font-bold text-yellow-600">{{ formatMoney(invoice.total_amount || 0) }}</p>
                    </div>

                    <!-- Fecha -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Factura</label>
                        <p class="text-lg text-gray-900">{{ formatDate(invoice.invoice_date) }}</p>
                    </div>

                    <!-- Método de Pago -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Método de Pago</label>
                        <p class="text-lg text-gray-900">{{ invoice.method_payment || '-' }}</p>
                    </div>

                    <!-- Creado por -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Creado por</label>
                        <p class="text-lg text-gray-900">{{ invoice.created_by?.name || '-' }}</p>
                    </div>
                </div>

                <!-- Descripción -->
                <div v-if="invoice.description" class="mt-6 border-t pt-6">
                    <label class="block text-sm font-medium text-gray-500 mb-2">Descripción</label>
                    <p class="text-gray-900 whitespace-pre-wrap">{{ invoice.description }}</p>
                </div>

                <!-- Archivos Adjuntos -->
                <div class="mt-6 border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Archivos Adjuntos</h3>
                    <div v-if="invoice.files && invoice.files.length > 0" class="space-y-2">
                        <div
                            v-for="file in invoice.files"
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
