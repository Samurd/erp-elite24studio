<script setup>
import { Head, Link } from '@inertiajs/vue3';
import WeeklyChart from './WeeklyChart.vue';
import MonthlyChart from './MonthlyChart.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    totalIngresos: Number,
    totalGastos: Number,
    totalGeneral: Number,
    weeklyChartData: Object,
    monthlyChartData: Object,
});

const formatMoney = (amount) => {
    return new Intl.NumberFormat('es-CO', { 
        style: 'currency', 
        currency: 'COP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
};
</script>

<template>
    <AppLayout title="Dashboard">
        <!-- Removed repetitive wrappers because layout handles them -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Columna izquierda (Gráficos) -->
            <div class="space-y-4">
                <WeeklyChart :data="weeklyChartData" />
                <MonthlyChart :data="monthlyChartData" />
            </div>

            <!-- Columna derecha -->
            <div class="space-y-4">
                <div class="flex justify-between flex-wrap">
                    <h3 class="font-bold text-fuente">Resumen de ganancias</h3>
                    <a href="/finances" class="text-fuente">Ver detalles</a>
                </div>

                <!-- Ingreso Neto Empresarial -->
                <div class="bg-gradient-to-r from-black via-[#b87c2b] to-[#c3933b] rounded-md p-4 text-white">
                    <div class="flex justify-between">
                        <h3 class="text-fuente">Ingreso neto Empresarial</h3>
                        <i class="fas fa-ellipsis-vertical"></i>
                    </div>
                    <div class="mt-1">
                        <p class="font-bold text-fuente text-[#fff]" style="font-size: 33px;">
                            {{ formatMoney(totalGeneral) }}
                        </p>
                    </div>
                    <div class="mt-2">
                            <a href="/finances">
                            <p class="text-fuente font-bold"><i class="fas fa-caret-right mr-2"></i>Ver Finanzas - Módulo Contabilidad</p>
                        </a>
                        <p class="ml-2 mt-2 text-fuente">Consulta el total de ganancias totales de la empresa, después de deducir los gastos operativos y obligaciones financieras.</p>
                    </div>
                </div>

                <!-- Segundo bloque -->
                <div class="bg-white rounded-md p-4 mt-12">
                    <div class="flex justify-between">
                        <p class="ml-2 text-[#c3993b] text-[16px]">Ingreso Bruto Empresarial</p>
                        <i class="fas fa-ellipsis-vertical"></i>
                    </div>
                    <div class="mt-2 space-y-2">
                        <p class="font-bold text-fuente text-[#c3933b]" style="font-size: 33px;">
                            {{ formatMoney(totalIngresos) }}
                        </p>
                        <a href="/finances">
                            <p class="font-bold"><i class="fas fa-caret-right mr-2"></i>Ver Finanzas - Módulo Contabilidad</p>
                        </a>
                        <p class="ml-2">Revisa el monto total de ingresos generados antes de aplicar deducciones y costos operativos.</p>
                        
                        <hr class="mb-5 mt-5" />
                        
                        <div class="flex justify-between">
                            <p class="ml-2">Gastos</p>
                            <i class="fas fa-ellipsis-vertical"></i>
                        </div>
                        <p class="font-bold text-fuente text-[#000]" style="font-size: 33px;">
                            {{ formatMoney(totalGastos) }}
                        </p>
                        <a href="/finances">
                            <p class="font-bold"><i class="fas fa-caret-right mr-2"></i>Ver Finanzas - Módulo Contabilidad</p>
                        </a>
                        <p class="ml-2">Revisa el monto total de gastos y costos operativos de la empresa.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
