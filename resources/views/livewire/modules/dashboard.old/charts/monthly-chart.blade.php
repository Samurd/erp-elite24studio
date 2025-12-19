<div x-data="{
    chart: null,
    monthlyData: @entangle('monthlyData'),
    init() {
        this.renderChart();
        this.$watch('monthlyData', () => this.renderChart());
    },
    renderChart() {
        if (this.chart) {
            this.chart.destroy();
        }

        const canvas = this.$refs.canvas;
        if (!canvas) return;

        if (this.monthlyData && this.monthlyData.labels && this.monthlyData.labels.length > 0) {
            const ctx = canvas.getContext('2d');
            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.values(this.monthlyData.labels),
                    datasets: [
                        {
                            label: 'Ingresos',
                            data: this.monthlyData.ingresos,
                            backgroundColor: '#c3933b',
                            borderColor: '#c3933b',
                            borderWidth: 1,
                            borderRadius: 5,
                        },
                        {
                            label: 'Gastos',
                            data: this.monthlyData.gastos,
                            backgroundColor: '#d9d9d9',
                            borderColor: '#d9d9d9',
                            borderWidth: 1,
                            borderRadius: 5,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Comparación mensual: Ingresos vs Gastos',
                            color: '#ffffff'
                        },
                        legend: {
                            labels: {
                                color: '#ffffff'
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#ffffff'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#ffffff'
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            }
                        }
                    }
                }
            });
        }
    }
}" class="bg-[#252525] rounded-md p-4 w-full md:h-[455px]">
    <div class="flex justify-between items-center mb-2">
        <h4 class="text-white font-semibold text-sm">Ingresos vs Gastos Mensuales</h4>
        <select wire:model.live="selectedYear"
            class="border border-gray-600 bg-gray-700 text-white rounded px-2 py-1 text-xs">
            @foreach($availableYears as $year)
                <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
        </select>
    </div>
    <div wire:ignore class="w-full h-[90%]">
        <canvas x-ref="canvas" class="w-full h-full"></canvas>
    </div>
</div>