<div class="p-6 space-y-6 bg-slate-900 text-white min-h-screen">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Finanzas Netas</h1>
        <div class="flex gap-2">
            <select wire:model.live="year" class="bg-slate-800 border-slate-700 rounded-lg text-sm">
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
            <select wire:model.live="month" class="bg-slate-800 border-slate-700 rounded-lg text-sm">
                <option value="">Todo el año</option>
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Total Income --}}
        <div class="bg-slate-800 p-4 rounded-xl border border-slate-700">
            <div class="text-slate-400 text-sm mb-1">INGRESO NETO EMPRESARIAL</div>
            <div class="text-2xl font-bold">${{ number_format($totalIncome, 2) }}</div>
        </div>

        {{-- Dynamic KPI Cards based on Types --}}
        @foreach($incomeByType as $type => $amount)
            <div class="bg-slate-800 p-4 rounded-xl border border-slate-700">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-slate-400 text-sm mb-1">{{ $type }}</div>
                        <div class="text-2xl font-bold">${{ number_format($amount, 2) }}</div>
                    </div>
                    <div class="p-2 bg-slate-700 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Charts Row 1 --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Growth Chart --}}
        <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex flex-col">
            <h3 class="text-lg font-semibold mb-4">Porcentaje de Crecimiento</h3>
            <div class="flex-1 relative" style="min-height: 200px;">
                <canvas id="growthChart"></canvas>
            </div>
            <div class="mt-4 flex justify-between text-sm text-slate-400">
                <span>Periodo Anterior: {{ number_format($previousPeriodIncome, 2) }}</span>
                <span>Actual: {{ number_format($totalIncome, 2) }}</span>
            </div>
        </div>

        {{-- Monthly Income Chart --}}
        <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex flex-col col-span-1 md:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Ingreso Neto por Mes-Año</h3>
                <select wire:model.live="monthlyChartYear" class="bg-slate-700 border-none text-xs rounded px-2 py-1">
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-1 relative" style="min-height: 250px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Category Chart --}}
        <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex flex-col col-span-1 md:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Ingreso Neto por Categoría</h3>
                <select wire:model.live="categoryChartMonth" class="bg-slate-700 border-none text-xs rounded px-2 py-1">
                    <option value="">Todo el año</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 relative" style="min-height: 250px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        {{-- Area Donut Chart --}}
        <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex flex-col">
            <h3 class="text-lg font-semibold mb-4">Ingreso Neto por Áreas</h3>
            <div class="flex-1 relative" style="min-height: 200px;">
                <canvas id="areaChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Charts Row 3 --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Comparison Chart --}}
        <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Comparativa Anual</h3>
                <select wire:model.live="comparisonChartYear"
                    class="bg-slate-700 border-none text-xs rounded px-2 py-1">
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex-1 relative" style="min-height: 250px;">
                <canvas id="comparisonChart"></canvas>
            </div>
        </div>

        {{-- Stacked Category Chart --}}
        <div class="bg-slate-800 p-4 rounded-xl border border-slate-700 flex flex-col">
            <h3 class="text-lg font-semibold mb-4">Categoría por Mes</h3>
            <div class="flex-1 relative" style="min-height: 250px;">
                <canvas id="stackedChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {

            // Chart Instances
            let charts = {};

            // Common Options
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: '#94a3b8' } }
                },
                scales: {
                    y: { grid: { color: '#334155' }, ticks: { color: '#94a3b8' } },
                    x: { grid: { color: '#334155' }, ticks: { color: '#94a3b8' } }
                }
            };

            const noGridOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { labels: { color: '#94a3b8' } } },
                scales: {
                    y: { display: false },
                    x: { display: false }
                }
            };

            // Initialize Charts Function
            function initCharts(data) {
                // Destroy existing charts if any
                Object.values(charts).forEach(chart => chart.destroy());

                // 1. Growth Chart (Doughnut)
                charts.growth = new Chart(document.getElementById('growthChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Periodo Anterior', 'Periodo Actual'],
                        datasets: [{
                            data: [data.previousPeriodIncome, data.totalIncome],
                            backgroundColor: ['#475569', '#3b82f6'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        ...noGridOptions,
                        cutout: '70%',
                        plugins: {
                            legend: { position: 'bottom', labels: { color: '#94a3b8' } }
                        }
                    }
                });

                // 2. Monthly Chart (Bar)
                charts.monthly = new Chart(document.getElementById('monthlyChart'), {
                    type: 'bar',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                        datasets: [{
                            label: 'Ingreso Neto',
                            data: data.chartMonthlyData,
                            backgroundColor: '#fce7f3',
                            borderRadius: 4
                        }]
                    },
                    options: commonOptions
                });

                // 3. Category Chart (Bar)
                charts.category = new Chart(document.getElementById('categoryChart'), {
                    type: 'bar',
                    data: {
                        labels: data.chartCategoryLabels,
                        datasets: [{
                            label: 'Ingreso',
                            data: data.chartCategoryData,
                            backgroundColor: '#818cf8',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        ...commonOptions,
                        indexAxis: 'y', // Horizontal bar
                    }
                });

                // 4. Area Chart (Doughnut)
                charts.area = new Chart(document.getElementById('areaChart'), {
                    type: 'doughnut',
                    data: {
                        labels: data.chartAreaLabels,
                        datasets: [{
                            data: data.chartAreaData,
                            backgroundColor: ['#818cf8', '#c084fc', '#f472b6', '#fb7185', '#38bdf8'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        ...noGridOptions,
                        cutout: '60%',
                        plugins: {
                            legend: { position: 'right', labels: { color: '#94a3b8' } }
                        }
                    }
                });

                // 5. Comparison Chart (Line/Bar)
                charts.comparison = new Chart(document.getElementById('comparisonChart'), {
                    type: 'bar',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                        datasets: [
                            {
                                label: 'Año Actual',
                                data: data.chartComparisonCurrent,
                                backgroundColor: '#38bdf8',
                                order: 1
                            },
                            {
                                label: 'Año Anterior',
                                data: data.chartComparisonLast,
                                backgroundColor: '#475569',
                                order: 2
                            }
                        ]
                    },
                    options: commonOptions
                });

                // 6. Stacked Chart
                charts.stacked = new Chart(document.getElementById('stackedChart'), {
                    type: 'bar',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                        datasets: data.stackedChartData
                    },
                    options: {
                        ...commonOptions,
                        scales: {
                            x: { stacked: true, grid: { color: '#334155' }, ticks: { color: '#94a3b8' } },
                            y: { stacked: true, grid: { color: '#334155' }, ticks: { color: '#94a3b8' } }
                        }
                    }
                });
            }

            // Initial Load
            const initialData = {
                totalIncome: @json($totalIncome),
                previousPeriodIncome: @json($previousPeriodIncome),
                chartMonthlyData: @json($chartMonthlyData),
                chartCategoryLabels: @json($chartCategoryLabels),
                chartCategoryData: @json($chartCategoryData),
                chartAreaLabels: @json($chartAreaLabels),
                chartAreaData: @json($chartAreaData),
                chartComparisonCurrent: @json($chartComparisonCurrent),
                chartComparisonLast: @json($chartComparisonLast),
                stackedChartData: @json($stackedChartData)
            };

            initCharts(initialData);

            // Listen for updates
            Livewire.on('charts-updated', (data) => {
                // data is an array of args, the first one is our payload
                // Livewire 3 passes args as spread, so (data) might be the first arg
                // Let's check if it's wrapped
                const payload = Array.isArray(data) ? data[0] : data;
                initCharts(payload);
            });
        });
    </script>
</div>