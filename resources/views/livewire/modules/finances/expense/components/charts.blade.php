<div class="space-y-6">
    <!-- 🟦 Chart 1: Ingreso Bruto por Año -->
    <div class="bg-[#252525] text-white p-4 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-semibold">Gastos por Mes–Año</h3>
            <select wire:model.live="yearChart1" class="bg-gray-800 text-sm rounded-md px-2 py-1">
                @foreach (range(now()->year, now()->year - 5) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div wire:ignore>
            <canvas id="grossIncomeChart" class="max-h-[14rem]"></canvas>
        </div>
    </div>

    <!-- 🟪 Chart 2: Ingreso Bruto por Categoría (según mes) -->
    <div class="bg-[#252525] text-white p-4 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-semibold">Gastos por Categoría (Mes)</h3>
            <select wire:model.live="monthChart2" class="bg-gray-800 text-sm rounded-md px-2 py-1">
                @foreach (['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $i => $m)
                    <option value="{{ $i + 1 }}">{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div wire:ignore>
            <canvas id="categoryIncomeChart" class="max-h-[14rem]"></canvas>
        </div>
    </div>

    <!-- 🟩 Tabla TOP 5 -->
    <div class="bg-[#252525] text-white p-4 rounded-xl shadow-md">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-sm font-semibold">TOP 5 Proovedores con
                mayores transacciones. </h3>
            <select wire:model.live="yearTable" class="bg-gray-800 text-sm rounded-md px-2 py-1">
                @foreach (range(now()->year, now()->year - 5) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <table class="w-full text-xs">
            <thead class="border-b border-gray-700 text-gray-400 uppercase">
                <tr>

                    <th class="p-2 text-left">N.Proyecto</th>
                    <th class="p-2 text-left">Proyecto</th>
                    <th class="p-2 text-center">Monto</th>
                    <th class="p-2 text-center">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topProjects as $i)
                    <tr class="border-b border-gray-800 hover:bg-gray-800/50">
                        <td class="p-2">{{ $i->name }}</td>
                        <td class="p-2">{{ $i->category->name ?? 'Sin categoría' }}</td>
                        <td class="p-2 text-red-400 text-center">
                            {{ App\Services\MoneyFormatterService::format($i->amount) }}</td>
                        <td class="p-2 text-center">{{ \Carbon\Carbon::parse($i->date)->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-gray-500 py-2">No hay datos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function moneyFormatterPesos(amount) {
        // console.log('amount', amount);
        const number = Number(amount) || 0;
        return number.toLocaleString('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 2
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const monthLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        const ctx1 = document.getElementById('grossIncomeChart');
        const ctx2 = document.getElementById('categoryIncomeChart');

        if (!ctx1 || !ctx2) {
            // console.error('Canvas elements not found');
            return;
        }

        // Datos iniciales desde PHP
        const initialChart1Data = @json(array_values($expenseByMonth));
        const initialChart2Labels = @json(array_keys($expenseByCategory));
        const initialChart2Data = @json(array_values($expenseByCategory));

        // Chart 1
        let chart1 = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Gastos por mes',
                    data: initialChart1Data,
                    backgroundColor: 'rgba(236,72,153,0.7)',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#fff' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#fff',
                            callback: function (value) {
                                return moneyFormatterPesos(value);
                            }
                        },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    },
                    x: {
                        ticks: { color: '#fff' },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    }
                }
            }
        });

        // Chart 2
        let chart2 = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: initialChart2Labels,
                datasets: [{
                    label: 'Gastos por categoría',
                    data: initialChart2Data,
                    backgroundColor: 'rgba(216,180,254,0.7)',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#fff' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#fff',
                            callback: function (value) {
                                return moneyFormatterPesos(value);
                            }
                        },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    },
                    x: {
                        ticks: { color: '#fff' },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    }
                }
            }
        });

        // Escuchar eventos de actualización
        window.addEventListener('updateChart1', event => {
            // console.log('Updating Chart 1 - Raw detail:', event.detail);

            // El primer elemento del array es el objeto con los datos
            const data = Array.isArray(event.detail) && event.detail.length > 0
                ? event.detail[0]
                : event.detail;

            // console.log('Chart 1 processed data:', data);

            chart1.data.datasets[0].data = Object.values(data);
            chart1.update();
        });

        window.addEventListener('updateChart2', event => {
            // console.log('Updating Chart 2 - Raw detail:', event.detail);

            // El primer elemento del array es el objeto con los datos
            const data = Array.isArray(event.detail) && event.detail.length > 0
                ? event.detail[0]
                : event.detail;

            // console.log('Chart 2 processed data:', data);

            chart2.data.labels = Object.keys(data);
            chart2.data.datasets[0].data = Object.values(data);
            chart2.update();
        });
    });
</script>