<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DoughnutChart from './DoughnutChart.vue'
import { computed } from 'vue'

const props = defineProps({
    totalExpenses: Number,
    totalGrossIncome: Number,
    netIncome: Number,
    recentTransactions: Array,
    clientInvoicesData: Object,
    providerInvoicesData: Object,
    billingAccountsData: Object,
    payrollPaymentsData: Object,
    payrollGenderData: Object,
})

const formatMoney = (amount, showSymbol = true) => {
    const formatted = new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount / 100)
    return showSymbol ? `$${formatted}` : formatted
}

const formatDate = (date) => {
    if (!date) return ''
    const d = new Date(date)
    const now = new Date()
    const diffMs = now - d
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))

    if (diffDays === 0) return 'Hoy'
    if (diffDays === 1) return 'Hace 1 día'
    if (diffDays < 7) return `Hace ${diffDays} días`
    if (diffDays < 30) return `Hace ${Math.floor(diffDays / 7)} semanas`
    return `Hace ${Math.floor(diffDays / 30)} meses`
}

const getStatusClass = (status) => {
    const lower = status?.toLowerCase() || ''
    if (lower.includes('completada') || lower.includes('pagada')) {
        return 'bg-green-100 text-green-600'
    }
    if (lower.includes('pendiente')) {
        return 'bg-yellow-100 text-yellow-600'
    }
    return 'bg-gray-100 text-gray-600'
}

const hasPayrollData = computed(() => {
    return (props.payrollPaymentsData?.data?.length > 0) || (props.payrollGenderData?.data?.length > 0)
})
</script>

<template>
    <AppLayout title="Finanzas">
        <div class="p-8 bg-gray-100 min-h-screen space-y-8">
            <!-- GRID PRINCIPAL -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                <!-- COLUMNA IZQUIERDA: INGRESOS / GASTOS -->
                <div class="xl:col-span-4 space-y-6">
                    <h2 class="text-2xl font-bold text-gray-800">Balance General</h2>

                    <!-- INGRESO NETO -->
                    <div class="bg-gradient-to-r from-black to-yellow-600 text-white rounded-2xl shadow-lg p-6 min-h-[250px] flex flex-col justify-between">
                        <div>
                            <h3 class="text-sm font-semibold uppercase tracking-wide opacity-90">Ingresos neto empresarial</h3>
                            <div class="text-5xl font-bold my-3 flex gap-2">
                                <span class="text-sm">$</span>
                                {{ formatMoney(netIncome, false) }}
                            </div>
                            <p class="text-sm opacity-90 leading-snug">
                                Consulta el total de ganancias totales de la
                                empresa, después de deducir los gastos
                                operativos y obligaciones financieras.
                            </p>
                        </div>
                        <Link :href="route('finances.net.index')">
                            <button class="text-sm text-yellow-600 underline mt-2 self-start">Ver Detalle</button>
                        </Link>
                    </div>

                    <!-- INGRESO BRUTO -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 min-h-[250px] flex flex-col justify-between">
                        <div>
                            <h3 class="text-sm font-semibold uppercase tracking-wide opacity-90">Ingresos bruto empresarial</h3>
                            <div class="text-5xl font-bold my-3 flex gap-2">
                                <span class="text-sm">$</span>
                                {{ formatMoney(totalGrossIncome, false) }}
                            </div>
                            <p class="text-sm text-gray-500 leading-snug">
                                Revisa el monta total de Ingresos generados
                                antes de aplicar deducciones y costos
                                operativos.
                            </p>
                        </div>
                        <Link :href="route('finances.gross.index')">
                            <button class="text-sm text-yellow-600 underline mt-2 self-start">Ver Detalle</button>
                        </Link>
                    </div>

                    <!-- GASTOS -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 min-h-[250px] flex flex-col justify-between">
                        <div>
                            <h3 class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Gastos Totales</h3>
                            <div class="text-5xl font-bold text-gray-800 my-3 flex gap-2">
                                <span class="text-sm">$</span>
                                {{ formatMoney(totalExpenses, false) }}
                            </div>
                            <p class="text-sm text-gray-500 leading-snug">
                                Egresos registrados en el período actual: nómina, suministros, servicios y otros gastos
                                operativos.
                            </p>
                        </div>
                        <Link :href="route('finances.expenses.index')">
                            <button class="text-sm text-yellow-600 underline mt-2 self-start">Ver Detalle</button>
                        </Link>
                    </div>
                </div>

                <!-- CENTRO: TRANSACCIONES -->
                <div class="xl:col-span-6 bg-white rounded-2xl shadow-lg p-6 flex flex-col">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-xl">Transacciones Recientes</h3>
                    </div>

                    <div class="overflow-y-auto border rounded-lg flex-1">
                        <table class="w-full text-sm">
                            <thead class="bg-gradient-to-r from-black to-yellow-600 text-white uppercase font-semibold sticky top-0">
                                <tr>
                                    <th class="p-2 text-left">Descripción</th>
                                    <th class="p-2 text-center">Fecha</th>
                                    <th class="p-2 text-center">Monto</th>
                                    <th class="p-2 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(transaction, index) in recentTransactions" :key="index" class="border-b hover:bg-gray-50">
                                    <td class="p-2">
                                        {{ transaction.description }}
                                        <span class="block text-xs text-gray-500">{{ transaction.subtitle }}</span>
                                    </td>
                                    <td class="p-2 text-center">
                                        {{ formatDate(transaction.date) }}
                                    </td>
                                    <td class="p-2 text-center font-semibold" :class="transaction.is_income ? 'text-green-500' : 'text-red-500'">
                                        {{ transaction.is_income ? '+' : '-' }}
                                        {{ formatMoney(transaction.amount) }}
                                    </td>
                                    <td class="p-2 text-center">
                                        <span class="text-xs px-2 py-1 rounded" :class="getStatusClass(transaction.status)">
                                            {{ transaction.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!recentTransactions || recentTransactions.length === 0">
                                    <td colspan="4" class="p-4 text-center text-gray-500">No hay transacciones recientes</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- DERECHA: FACTURACIÓN -->
                <div class="xl:col-span-2 space-y-6 w-full">
                    <h3 class="font-bold text-xl text-gray-800">Facturación</h3>

                    <div class="bg-white p-4 rounded-2xl shadow">
                        <div class="flex justify-between text-sm font-semibold mb-2">
                            <span>Clientes</span>
                            <span class="text-gray-400">Reciente</span>
                        </div>
                        <DoughnutChart
                            :labels="clientInvoicesData?.labels || []"
                            :data="clientInvoicesData?.data || []"
                            :colors="clientInvoicesData?.colors || []"
                        />
                        <Link :href="route('finances.invoices.clients.index')" class="text-yellow-600 text-xs underline mt-2 block">
                            Ver Detalle
                        </Link>
                    </div>

                    <div class="bg-white p-4 rounded-2xl shadow">
                        <div class="flex justify-between text-sm font-semibold mb-2">
                            <span>Cuentas de Cobro</span>
                            <span class="text-gray-400">Reciente</span>
                        </div>
                        <DoughnutChart
                            :labels="billingAccountsData?.labels || []"
                            :data="billingAccountsData?.data || []"
                            :colors="billingAccountsData?.colors || []"
                        />
                        <Link :href="route('finances.invoices.clients.billing-accounts.index')" class="text-yellow-600 text-xs underline mt-2 block">
                            Ver Detalle
                        </Link>
                    </div>

                    <div class="bg-white p-4 rounded-2xl shadow">
                        <div class="flex justify-between text-sm font-semibold mb-2">
                            <span>Proveedores</span>
                            <span class="text-gray-400">Reciente</span>
                        </div>
                        <DoughnutChart
                            :labels="providerInvoicesData?.labels || []"
                            :data="providerInvoicesData?.data || []"
                            :colors="providerInvoicesData?.colors || []"
                        />
                        <Link :href="route('finances.invoices.providers.index')" class="text-yellow-600 text-xs underline mt-2 block">
                            Ver Detalle
                        </Link>
                    </div>
                </div>
            </div>

            <!-- BLOQUES INFERIORES -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <!-- Nómina -->
                <div class="bg-white p-6 rounded-2xl col-span-2 shadow-lg flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-lg mb-3">Gestión de Nómina</h3>
                        <div v-if="!hasPayrollData" class="text-center text-gray-500 py-8">
                            <p>No hay datos de nómina disponibles</p>
                        </div>
                        <div v-else class="flex items-center justify-center gap-8">
                            <DoughnutChart
                                v-if="payrollPaymentsData?.data?.length > 0"
                                :labels="payrollPaymentsData?.labels || []"
                                :data="payrollPaymentsData?.data || []"
                                :colors="payrollPaymentsData?.colors || []"
                            />
                            <DoughnutChart
                                v-if="payrollGenderData?.data?.length > 0"
                                :labels="payrollGenderData?.labels || []"
                                :data="payrollGenderData?.data || []"
                                :colors="payrollGenderData?.colors || []"
                            />
                        </div>
                    </div>
                    <Link :href="route('finances.payrolls.stats')" class="text-yellow-600 text-sm underline mt-3 self-start">
                        Ver Estadísticas
                    </Link>
                </div>

                <!-- Indicadores Financieros -->
                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <h3 class="font-bold text-lg mb-3">Analisis Financieros</h3>
                    <ul class="space-y-2">
                        <li>
                            <Link :href="route('finances.payrolls.index')" class="bg-gray-800 text-white px-3 py-1 rounded inline-block">
                                Control Salarial
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('finances.taxes.index')" class="bg-gray-800 text-white px-3 py-1 rounded inline-block">
                                Impuestos laborales
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Cumplimiento Fiscal -->
                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <h3 class="font-bold text-lg mb-3">Cumplimiento Fiscal</h3>
                    <ul class="space-y-2">
                        <li>
                            <Link :href="route('finances.audits.index')" class="bg-gray-800 text-white px-3 py-1 rounded inline-block">
                                Auditorías
                            </Link>
                        </li>
                        <li>
                            <Link :href="route('finances.norms.index')" class="bg-gray-800 text-white px-3 py-1 rounded inline-block">
                                Normas NIF
                            </Link>
                        </li>
                    </ul>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <ul class="space-y-2">
                        <li>
                            <Link :href="route('finances.invoices.index')" class="bg-gray-800 text-white px-3 py-1 rounded">
                                Registro de facturas
                            </Link>
                        </li>
                        <li>
                            <a href="https://muisca.dian.gov.co/WebIdentidadLogin/?ideRequest=eyJjbGllbnRJZCI6IldvMGFLQWxCN3ZSUF8xNmZyUEkxeDlacGhCRWEiLCJyZWRpcmVjdF91cmkiOiJodHRwOi8vbXVpc2NhLmRpYW4uZ292LmNvL0lkZW50aWRhZFJlc3RfTG9naW5GaWx0cm8vYXBpL3N0cy92MS9hdXRoL2NhbGxiYWNrP3JlZGlyZWN0X3VyaT1odHRwJTNBJTJGJTJGbXVpc2NhLmRpYW4uZ292LmNvJTJGV2ViQXJxdWl0ZWN0dXJhJTJGRGVmTG9naW4uZmFjZXMiLCJyZXNwb25zZVR5cGUiOiIiLCJzY29wZSI6IiIsInN0YXRlIjoiIiwibm9uY2UiOiIiLCJwYXJhbXMiOnsidGlwb1VzdWFyaW8iOiJtdWlzY2EifX0%3D" target="_blank" class="bg-gradient-to-r from-black to-yellow-600 text-white px-3 py-1 rounded flex flex-col gap-1 items-center">
                                <span>Sistema de facturación</span>
                                <p class="text-xs text-gray-200 font-semibold">Serás redirigido al Software de Facturacion- DIAN</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
