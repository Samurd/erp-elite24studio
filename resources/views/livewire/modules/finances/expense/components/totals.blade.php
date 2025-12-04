<div class="space-y-4">
    <!-- Tarjeta 1 -->
    <div class="bg-[#252525] text-white p-4 rounded-2xl shadow-md">
        <div class="flex items-center space-x-2 mb-2">
            <span class="w-3 h-3 rounded-sm bg-gradient-to-r from-purple-500 to-pink-400"></span>
            <h3 class="text-sm font-semibold">Costos Directos</h3>
        </div>
        <p class="text-xs text-gray-300 mb-3">
            Materiales usados, Pagos de contratistas, Costos de Equipos
        </p>
        <p class="text-3xl font-bold mb-2 flex gap-2"><span class="text-sm">$</span> {{ App\Services\MoneyFormatterService::format($totalDirect, false) }}</p>
    </div>

    <!-- Tarjeta 2 -->
    <div class="bg-[#252525] text-white p-4 rounded-2xl shadow-md">
        <div class="flex items-center space-x-2 mb-2">
            <span class="w-3 h-3 rounded-sm bg-gradient-to-r from-purple-500 to-pink-400"></span>
            <h3 class="text-sm font-semibold">Gastos Indirectos</h3>
        </div>
        <p class="text-xs text-gray-300 mb-3">
            Sueldos Administrativos, Servicios públicos, transporte, Oficinas, Viáticos.
        </p>
       <p class="text-3xl font-bold mb-2 flex gap-2"><span class="text-sm">$</span> {{ App\Services\MoneyFormatterService::format($totalIndirect, false) }}</p>
    </div>

    <!-- Tarjeta 3 -->
    <div class="bg-[#252525] text-white p-4 rounded-2xl shadow-md">
        <div class="flex items-center space-x-2 mb-2">
            <span class="w-3 h-3 rounded-sm bg-gradient-to-r from-purple-500 to-pink-400"></span>
            <h3 class="text-sm font-semibold">Impuestos</h3>
        </div>
        <p class="text-xs text-gray-300 mb-3">
            IVA, Retenciones en la Fuente, Renta, Seguridad Social, Prestaciones
        </p>
        <p class="text-3xl font-bold mb-2 flex gap-2"><span class="text-sm">$</span> {{ App\Services\MoneyFormatterService::format($totalTaxes, false) }}</p>
    </div>

    <!-- Tarjeta 4 -->
    <div class="bg-[#252525] text-white p-4 rounded-2xl shadow-md">
        <div class="flex items-center space-x-2 mb-2">
            <span class="w-3 h-3 rounded-sm bg-gradient-to-r from-purple-500 to-pink-400"></span>
            <h3 class="text-sm font-semibold">Gastos Financieros</h3>
        </div>
        <p class="text-xs text-gray-300 mb-3">
            Pagos o entidades por créditos, Comisiones Bancarias, Mantenimiento de cuentas
        </p>
       <p class="text-3xl font-bold mb-2 flex gap-2"><span class="text-sm">$</span> {{ App\Services\MoneyFormatterService::format($totalFinance, false) }}</p>
    </div>
</div>
