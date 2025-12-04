<x-app-layout>
    <div class="flex flex-col flex-1">
        <main class="flex-1 p-10 bg-gray-100 overflow-y-auto">
            <!-- GRID PRINCIPAL -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Contratos -->
                <div class="bg-white p-4 flex items-center justify-between shadow h-full">
                    <div class="text-blue-500 text-2xl">
                        <i class="fa-solid fa-file-contract"></i>
                    </div>

                </div>

                <!-- Proyectos -->
                <div class="bg-white  p-4 flex items-center justify-between shadow h-full">
                    <div class="text-blue-500 text-2xl">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-500 text-sm">Proyectos</p>
                        <p class="text-xl font-bold">{{ $totalProyectos }}</p>
                    </div>
                </div>

                <!-- Cotizaciones -->
                <div class="bg-white p-4 flex items-center justify-between shadow h-full">
                    <div class="text-blue-500 text-2xl">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-500 text-sm">Cotizaciones</p>
                        <p class="text-xl font-bold">{{ $totalCotizaciones }}</p>
                    </div>
                </div>

                <!-- Certificados -->
                <div class="bg-white  p-4 flex items-center justify-between shadow h-full">
                    <div class="text-blue-500 text-2xl">
                        <i class="fa-solid fa-certificate"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-500 text-sm">Certificados</p>
                        <p class="text-xl font-bold">{{ $totalCertificados }}</p>
                    </div>
                </div>
            </div>


            <!-- NUEVA GRID PARA CONTENIDO INFERIOR -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Columna izquierda -->
                <div class="space-y-4">
                    <div class="flex justify-between flex-wrap">
                        <h3 class="font-bold">Ingreso Neto por Trimestre /Año</h3>
                        <a href="">Filtrar</a>
                    </div>
                    <div class="bg-white rounded-md p-4 h-64">
                        <canvas id="multiAxisChart" class="w-full h-full"></canvas>
                    </div>
                    <div class="bg-[#252525] rounded-md p-4 h-[355px]">
                        <canvas id="barChart" class="w-full h-full"></canvas>
                    </div>
                </div>
                <!-- Columna derecha -->
                <div class="space-y-4">
                    <div class="flex justify-between flex-wrap">
                        <h3 class="font-bold text-fuente">Resumen de ganancias</h3>
                        <a class="text-fuente" href="">Ver detalles</a>
                    </div>
                    <div class="bg-gradient-to-r from-black via-[#b87c2b] to-[#c3933b] rounded-md p-4 text-white">
                        <div class="flex justify-between">
                            <h3 class="text-fuente">Ingreso neto Empresarial</h3>
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </div>
                        <div class="mt-1">
                            <p class="font-bold text-fuente text-[#fff]" style="font-size: 33px;"><i
                                    class="fa-solid fa-dollar-sign text-xs relative -translate-y-3 mr-1"></i>{{ number_format($totalGeneral, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="mt-2">
                            <a href="">
                                <p class="text-fuente font-bold"><i class="fa-solid fa-caret-right mr-2"></i>Ver
                                    Finanzas - Modulo Contabilidad</p>
                            </a>
                            <p class="ml-2 mt-2 text-fuente">Consulta el total de ganancias totales de la empresa,
                                después de deducir los gastos operativos y obligaciones financieras.</p>
                        </div>
                    </div>
                    <!-- Segundo bloque -->
                    <div class="bg-white rounded-md p-4 mt-12">
                        <div class="flex justify-between">
                            <p class="ml-2 text-[#c3993b] text-[16px]">Ingreso Bruto Empresarial</p>
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </div>
                        <div class="mt-2 space-y-2">
                            <p class="font-bold text-fuente text-[#c3933b]" style="font-size: 33px;"><i
                                    class="fa-solid fa-dollar-sign text-xs relative -translate-y-3 mr-1"></i>{{ number_format($totalIngresos, 0, ',', '.') }}
                            </p>
                            <a href="">
                                <p class="font-bold"><i class="fa-solid fa-caret-right mr-2"></i>Ver Finanzas - Modulo
                                    Contabilidad</p>
                            </a>
                            <p class="ml-2">Revisa el monto total de ingresos generados antes de aplicar deducciones y
                                costos operativos.</p>
                            <hr class="mb-5 mt-5" />
                            <div class="flex justify-between">
                                <p class="ml-2">Gastos</p>
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </div>
                            <p class="font-bold text-fuente text-[#000]" style="font-size: 33px;"><i
                                    class="fa-solid fa-dollar-sign text-xs relative -translate-y-3 mr-1"></i>{{ number_format($totalGastos, 0, ',', '.') }}
                            </p>
                            <a href="">
                                <p class="font-bold"><i class="fa-solid fa-caret-right mr-2"></i>Ver Finanzas - Modulo
                                    Contabilidad</p>
                            </a>
                            <p class="ml-2">Revisa el monto total de ingresos generados antes de aplicar deducciones y
                                costos operativos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>


    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const ctxMulti = document.getElementById('multiAxisChart').getContext('2d');
                const ctxBar = document.getElementById('barChart').getContext('2d');

                // 🔹 Eje X: Semanas dentro de un trimestre
                const semanas = ["Week 1", "Week 2", "Week 3", "Week 4"];

                // 🔹 Año fijo
                const anio = "2025";

                // 🔹 Datos de ejemplo (pueden venir de BD después)
                const t1IngresoNeto = [10, 15, 25, 20];
                const t1Gastos = [5, 10, 15, 20];

                new Chart(ctxMulti, {
                    type: 'line',
                    data: {
                        labels: semanas,
                        datasets: [
                            {
                                label: `T1 ${anio}`,
                                data: t1IngresoNeto,
                                borderColor: '#c3933b',
                                backgroundColor: '#c3933b',
                                tension: 0.3
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: `Evolución semanal por trimestre - ${anio}`
                            }
                        }
                    }
                });

                // Chart de barras (solo Ingreso Neto por trimestre)
                new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: semanas,
                        datasets: [{
                            label: 'Ingreso Neto',
                            data: t1IngresoNeto,
                            backgroundColor: '#c3933b',
                            borderColor: '#c3933b',
                            borderWidth: 1,
                            borderRadius: 5,
                        },
                        {
                            label: 'Gastos',
                            data: t1Gastos,
                            backgroundColor: '#d9d9d9',
                            borderColor: '#d9d9d9',
                            borderWidth: 1,
                            borderRadius: 5,
                        }

                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            });
        </script>
    @endpush





    {{-- <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <x-welcome />
            </div>
        </div>
    </div> --}}
</x-app-layout>