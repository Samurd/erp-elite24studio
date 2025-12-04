<div class="flex flex-col flex-1">
    <main class="flex-1 p-10 bg-gray-100 overflow-y-auto">
        <!-- GRID PARA CONTENIDO -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Columna izquierda -->
            <div class="space-y-4">
                @livewire('modules.dashboard.charts.weekly-chart')
                @livewire('modules.dashboard.charts.monthly-chart')
            </div>
            <!-- Columna derecha -->
            <div class="space-y-4">
                <div class="flex justify-between flex-wrap">
                    <h3 class="font-bold text-fuente">Resumen de ganancias</h3>
                    <a class="text-fuente" href="{{ route('finances.index') }}">Ver detalles</a>
                </div>
                <div class="bg-gradient-to-r from-black via-[#b87c2b] to-[#c3933b] rounded-md p-4 text-white">
                    <div class="flex justify-between">
                        <h3 class="text-fuente">Ingreso neto Empresarial</h3>
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </div>
                    <div class="mt-1">
                        <p class="font-bold text-fuente text-[#fff]" style="font-size: 33px;">
                            {{ $moneyFormatter::format($totalGeneral) }}
                        </p>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('finances.index') }}">
                            <p class="text-fuente font-bold"><i class="fa-solid fa-caret-right mr-2"></i>Ver
                                Finanzas - Módulo Contabilidad</p>
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
                        <p class="font-bold text-fuente text-[#c3933b]" style="font-size: 33px;">
                            {{ $moneyFormatter::format($totalIngresos) }}
                        </p>
                        <a href="{{ route('finances.index') }}">
                            <p class="font-bold"><i class="fa-solid fa-caret-right mr-2"></i>Ver Finanzas - Módulo
                                Contabilidad</p>
                        </a>
                        <p class="ml-2">Revisa el monto total de ingresos generados antes de aplicar deducciones y
                            costos operativos.</p>
                        <hr class="mb-5 mt-5" />
                        <div class="flex justify-between">
                            <p class="ml-2">Gastos</p>
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </div>
                        <p class="font-bold text-fuente text-[#000]" style="font-size: 33px;">
                            {{ $moneyFormatter::format($totalGastos) }}
                        </p>
                        <a href="{{ route('finances.index') }}">
                            <p class="font-bold"><i class="fa-solid fa-caret-right mr-2"></i>Ver Finanzas - Módulo
                                Contabilidad</p>
                        </a>
                        <p class="ml-2">Revisa el monto total de gastos y costos operativos de la empresa.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>