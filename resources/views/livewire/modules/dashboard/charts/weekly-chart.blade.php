<div x-data="{
    chart: null,
    weeklyData: @entangle('weeklyData'),
    init() {
        this.renderChart();
        this.$watch('weeklyData', () => {
            this.renderChart();
        });
    },
    renderChart() {
        if (this.chart) {
            this.chart.destroy();
        }

        const canvas = this.$refs.canvas;
        if (!canvas) return;

        if (this.weeklyData && this.weeklyData.labels && this.weeklyData.labels.length > 0) {
            const ctx = canvas.getContext('2d');
            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.weeklyData.labels,
                    datasets: [{
                        label: 'Ingreso Neto',
                        data: this.weeklyData.ingresos.map((ingreso, i) => ingreso - (this.weeklyData.gastos[i] || 0)),
                        borderColor: '#c3933b',
                        backgroundColor: 'rgba(195, 147, 59, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Evolución semanal del ingreso neto'
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }
}" class="w-full">
    <div class="flex justify-between flex-wrap items-center gap-2 mb-4">
        <h3 class="font-bold">Ingreso Neto Semanal por Trimestre</h3>
        <div class="flex gap-2">
            <select wire:model.live="selectedQuarter" class="border border-gray-300 rounded px-3 py-1 text-sm">
                @foreach($availableQuarters as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedYear" class="border border-gray-300 rounded px-3 py-1 text-sm">
                @foreach($availableYears as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="bg-white rounded-md p-4 h-64">
        <div wire:ignore class="w-full h-full">
            <canvas x-ref="canvas" class="w-full h-full"></canvas>
        </div>
    </div>
</div>