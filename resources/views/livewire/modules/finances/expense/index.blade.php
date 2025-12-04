<div class="p-4">

    <div>
        <h4 class="text-xl font-semibold">Costos y Gastos</h4>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_2fr_1fr] gap-2 p-6">

        <!-- Columna Medio: Charts -->
        <livewire:modules.finances.expense.components.totals />



        <!-- Columna Medio: Charts -->
        <livewire:modules.finances.expense.components.charts />


        <!-- Columna Derecha: Tabla de Ingresos -->
        <livewire:modules.finances.expense.components.table />




    </div>
</div>