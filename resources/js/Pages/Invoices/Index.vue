<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
</script>

<template>
    <AppLayout title="Facturación">
        <div class="p-6">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-3xl font-bold text-gray-800">Facturación</h1>
                </div>
                <h2 class="text-xl font-semibold text-gray-700 mb-2">REGISTRO DE FACTURAS-ELITE</h2>
                <div class="flex items-start gap-2 text-sm text-gray-600">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>ANTES DE INICIAR A REGISTRAR LA FACTURA DEBE SER EMITIDA POR LA DIAN, O SI ES CUENTA DE COBRO ASEGÚRESE
                        QUE ESTÉ DESCARGADA CORRECTAMENTE.</p>
                </div>
            </div>

            <!-- Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Card 1: Facturas a Clientes-DIAN -->
                <Link
                    :href="route('finances.invoices.clients.index')"
                    class="block bg-white rounded-2xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer"
                >
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4 text-center">Facturación desde la DIAN para
                            nuestros Clientes, Subir datos, ARCHIVOS</p>

                        <!-- Icon with gradient background -->
                        <div class="flex justify-center mb-4">
                            <div
                                class="w-36 h-36 rounded-2xl bg-gradient-to-br from-teal-400 to-green-500 flex items-center justify-center shadow-lg">
                                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 5v6m0 0l-2-2m2 2l2-2"></path>
                                    <circle cx="16" cy="8" r="2.5" stroke="currentColor" stroke-width="1.5" fill="none">
                                    </circle>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.5 9.5l3 3">
                                    </path>
                                </svg>
                            </div>
                        </div>

                        <h3 class="text-lg font-bold text-gray-800 text-center mb-3">Facturas a Clientes-DIAN
                        </h3>

                        <div class="flex items-start gap-2 text-xs text-gray-500">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold">CONTRATISTA: ELITE 24 STUDIO SAS</p>
                                <p>CONTRATANTE: CLIENTE</p>
                            </div>
                        </div>
                    </div>
                </Link>

                <!-- Card 2: Proveedores C. Cobro a Elite -->
                <Link
                    :href="route('finances.invoices.providers.index')"
                    class="block bg-white rounded-2xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer"
                >
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4 text-center">Registrar las Cuentas de Cobro
                            emitidas por Contratistas</p>

                        <!-- Icon with gradient background -->
                        <div class="flex justify-center mb-4">
                            <div
                                class="w-36 h-36 rounded-2xl bg-gradient-to-br from-pink-400 via-purple-500 to-purple-600 flex items-center justify-center shadow-lg">
                                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </div>
                        </div>

                        <h3 class="text-lg font-bold text-gray-800 text-center mb-3">Proveedores C. Cobro a
                            Elite</h3>

                        <div class="flex items-start gap-2 text-xs text-gray-500">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold">CONTRATISTA: PROVEEDOR</p>
                                <p>CONTRATANTE: ELITE 24 STUDIO SAS</p>
                            </div>
                        </div>
                    </div>
                </Link>

                <!-- Card 3: Cuentas de Cobro de ELITE -->
                <Link
                    :href="route('finances.invoices.clients.billing-accounts.index')"
                    class="block bg-white rounded-2xl shadow-lg overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer"
                >
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4 text-center">Realizar y guardar las cuentas de
                            Cobro emitidas por ELITE</p>

                        <!-- Icon with gradient background -->
                        <div class="flex justify-center mb-4">
                            <div
                                class="w-36 h-36 rounded-2xl bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center shadow-lg">
                                <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                    <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="1.5" fill="none"></circle>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 10a3 3 0 11-6 0"></path>
                                </svg>
                            </div>
                        </div>

                        <h3 class="text-lg font-bold text-gray-800 text-center mb-3">Cuentas de Cobro de ELITE
                        </h3>

                        <div class="flex items-start gap-2 text-xs text-gray-500">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold">CONTRATISTA: ELITE 24 STUDIO SAS</p>
                                <p>CONTRATANTE: CLIENTE</p>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
