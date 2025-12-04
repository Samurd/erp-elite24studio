<div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Column 1: Charts -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Nóminas-Estadistica -->
            <div class="bg-white rounded-lg shadow-sm p-6" wire:key="stats-section-{{ $yearStats }}">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Nóminas-Estadistica</h2>
                <div class="flex items-center space-x-2 mb-4">
                    <span class="text-sm text-gray-600">Ordenar</span>
                    <select wire:model.live="yearStats"
                        class="text-blue-600 font-bold border-none focus:ring-0 cursor-pointer">
                        @for($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}">Año {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Status Chart -->
                    <div class="relative max-h-60 flex items-center justify-center"
                        x-data="{ hasData: @js(count($statusData) > 0) }"
                        @update-stats-charts.window="hasData = $event.detail[0].statusData.length > 0">
                        <canvas id="statusChart" x-show="hasData"></canvas>
                        <div x-show="!hasData" class="text-gray-400 text-sm font-medium py-10">Sin datos</div>
                    </div>
                    <!-- Gender Chart -->
                    <div class="relative max-h-40 flex items-center justify-center"
                        x-data="{ hasData: @js(count($genderData) > 0) }"
                        @update-stats-charts.window="hasData = $event.detail[0].genderData.length > 0">
                        <canvas id="genderChart" x-show="hasData"></canvas>
                        <div x-show="!hasData" class="text-gray-400 text-sm font-medium py-10">Sin datos</div>
                    </div>
                </div>
            </div>

            <!-- Impuestos Laborales -->
            <div class="bg-white rounded-lg shadow-sm p-6" wire:key="taxes-section-{{ $yearTaxes }}">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Impuestos</h2>
                    <span class="text-blue-500 font-bold tracking-widest">DIAN</span>
                </div>
                <div class="flex items-center space-x-2 mb-4">
                    <span class="text-sm text-gray-600">Ordenar</span>
                    <select wire:model.live="yearTaxes"
                        class="text-blue-600 font-bold border-none focus:ring-0 cursor-pointer">
                        @for($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}">Año {{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="space-y-4">
                    <!-- Table Header -->
                    <div class="grid grid-cols-4 text-xs font-bold text-white bg-black p-2 rounded-t-lg">
                        <div>Impuesto</div>
                        <div>Fecha</div>
                        <div>Monto</div>
                        <div>Estado</div>
                    </div>

                    <!-- List -->
                    @forelse($recentTaxes as $tax)
                        <div class="grid grid-cols-4 text-sm items-center border-b border-gray-100 pb-2">
                            <div>
                                <div class="font-bold text-gray-800">{{ $tax->entity }}</div>
                                <div class="text-xs text-gray-500">{{ $tax->type->name ?? 'N/A' }}</div>
                            </div>
                            <div class="text-gray-600">
                                {{ $tax->date ? $tax->date->translatedFormat('d M') : '-' }}
                                <div class="text-xs text-gray-400">{{ $tax->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="font-bold {{ $tax->amount < 0 ? 'text-red-500' : 'text-green-500' }}">
                                {{ \App\Services\MoneyFormatterService::format($tax->amount) }}
                            </div>
                            <div>
                                @if($tax->status)
                                    <span class="px-2 py-1 text-xs font-bold text-white rounded-full"
                                        style="background-color: {{ $tax->status->color ?? '#10B981' }}">
                                        {{ $tax->status->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500 text-sm">
                            No hay impuestos registrados para este año.
                        </div>
                    @endforelse
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('finances.taxes.index') }}"
                        class="text-yellow-600 font-bold text-sm hover:underline">Ver todas mis transacciones</a>
                </div>

                <div class="mt-6 flex space-x-4">
                    <a href="{{ route('finances.taxes.create') }}"
                        class="flex-1 bg-blue-500 text-white text-center py-2 rounded-lg font-bold hover:bg-blue-600 transition">
                        REGISTRAR
                    </a>
                </div>
            </div>
        </div>

        <!-- Column 2: Control Salarial & Deductions -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Control Salarial -->
            <div class="bg-white rounded-lg shadow-sm p-6" wire:key="payrolls-section-{{ $yearPayrolls }}">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Control Salarial</h2>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mt-1">REGISTRO DE PAGOS A EMPLEADOS
                            O
                            COLABORADORES</p>
                        <div class="flex items-center space-x-2 mt-2">
                            <span class="text-sm text-gray-600">Ordenar</span>
                            <select wire:model.live="yearPayrolls"
                                class="text-blue-600 font-bold border-none focus:ring-0 cursor-pointer">
                                @for($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}">Año {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons (Right Side) -->
                    <div class="space-y-2">
                        <a href="{{ route('finances.payrolls.index') }}"
                            class="block w-48 bg-blue-600 text-white p-3 rounded-xl flex items-center justify-between hover:bg-blue-700 transition">
                            <div class="flex flex-col text-left">
                                <i class="fas fa-eye text-2xl mb-1"></i>
                                <span class="text-xs font-bold">VER</span>
                                <span class="text-[10px]">TODOS LOS PAGOS</span>
                            </div>
                            <div class="bg-white/20 rounded-full p-1">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </div>
                        </a>

                        <a href="{{ route('finances.payrolls.create') }}"
                            class="block w-48 bg-cyan-500 text-white p-3 rounded-xl flex items-center justify-between hover:bg-cyan-600 transition">
                            <div class="flex flex-col text-left">
                                <i class="fas fa-clipboard-list text-2xl mb-1"></i>
                                <span class="text-xs font-bold">REGISTRAR</span>
                                <span class="text-[10px]">NUEVOS PAGOS</span>
                            </div>
                            <div class="bg-white/20 rounded-full p-1">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="mt-6">
                    <!-- Table Header -->
                    <div class="grid grid-cols-4 text-sm font-bold text-white bg-yellow-700 p-3 rounded-t-lg">
                        <div>Empleado</div>
                        <div>Fecha</div>
                        <div>Monto</div>
                        <div>Resultado</div>
                    </div>

                    <!-- List -->
                    @forelse($recentPayrolls as $payroll)
                        <div
                            class="grid grid-cols-4 items-center border-b border-gray-100 py-4 hover:bg-gray-50 transition">
                            <div>
                                <div class="font-bold text-gray-800">{{ $payroll->employee->full_name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $payroll->employee->job_title ?? 'Empleado' }}
                                </div>
                            </div>
                            <div class="text-gray-600 text-sm">
                                {{ $payroll->created_at->isToday() ? 'Hoy' : ($payroll->created_at->isYesterday() ? 'Ayer' : $payroll->created_at->translatedFormat('d M')) }}
                                <div class="text-xs text-gray-400">{{ $payroll->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="font-bold {{ $payroll->total < 0 ? 'text-red-500' : 'text-green-500' }}">
                                {{ \App\Services\MoneyFormatterService::format($payroll->total) }}
                                <div class="text-xs text-gray-400 font-normal">Transferencia</div>
                            </div>
                            <div>
                                @if($payroll->status)
                                    <span class="px-2 py-1 text-xs font-bold text-white rounded-full"
                                        style="background-color: {{ $payroll->status->color ?? '#10B981' }}">
                                        {{ $payroll->status->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500 text-sm">
                            No hay nóminas registradas para este año.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Deducciones Nóminas Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6" wire:key="deductions-section-{{ $yearDeductions }}">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Deducciones Nóminas</h2>
                        <p class="text-xs text-gray-500 uppercase tracking-wide mt-1">APORTES SEGURIDAD SOCIAL,
                            PENSIÓN,
                            IMPUESTO DE RENTA, O CREDITOS</p>

                        <div class="mt-4">
                            <label class="block text-xs text-gray-500 mb-1">Seleccionar Año</label>
                            <select wire:model.live="yearDeductions"
                                class="bg-orange-500 text-white font-bold rounded-lg px-4 py-2 border-none focus:ring-0 cursor-pointer">
                                @for($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}" class="bg-white text-gray-800">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="text-xs font-bold text-gray-600 uppercase">
                        DEDUCCIONES POR PERIODO
                    </div>
                </div>

                <div class="h-64 flex items-center justify-center"
                    x-data="{ hasData: @js(array_sum($deductionsData) > 0) }"
                    @update-deductions-chart.window="hasData = $event.detail[0].deductionsData.reduce((a, b) => a + b, 0) > 0">
                    <canvas id="deductionsChart" x-show="hasData"></canvas>
                    <div x-show="!hasData" class="text-gray-400 text-sm font-medium">Sin datos para mostrar</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Initialization -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            const statusCtx = document.getElementById('statusChart').getContext('2d');
            const genderCtx = document.getElementById('genderChart').getContext('2d');
            const deductionsCtx = document.getElementById('deductionsChart').getContext('2d');

            let statusChart, genderChart, deductionsChart;

            const initCharts = () => {
                // Status Chart
                if (statusChart) statusChart.destroy();
                statusChart = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($statusLabels),
                        datasets: [{
                            data: @json($statusData),
                            backgroundColor: ['#EF4444', '#3B82F6', '#10B981', '#F59E0B'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        cutout: '70%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });

                // Gender Chart
                if (genderChart) genderChart.destroy();
                genderChart = new Chart(genderCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($genderLabels),
                        datasets: [{
                            data: @json($genderData),
                            backgroundColor: ['#EF4444', '#3B82F6', '#10B981'], // Adjust colors as needed
                            borderWidth: 0
                        }]
                    },
                    options: {
                        cutout: '70%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });

                // Deductions Chart
                if (deductionsChart) deductionsChart.destroy();
                deductionsChart = new Chart(deductionsCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                        datasets: [{
                            data: @json($deductionsData),
                            backgroundColor: 'black',
                            borderRadius: 4,
                            barPercentage: 0.7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: true,
                                    drawBorder: false
                                },
                                ticks: {
                                    callback: function (value, index, values) {
                                        return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumSignificantDigits: 3 }).format(value);
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            };

            initCharts();

            // Listen for chart updates
            Livewire.on('update-stats-charts', (data) => {
                const chartData = Array.isArray(data) ? data[0] : data;

                if (statusChart) {
                    statusChart.data.labels = chartData.statusLabels;
                    statusChart.data.datasets[0].data = chartData.statusData;
                    statusChart.update();
                }

                if (genderChart) {
                    genderChart.data.labels = chartData.genderLabels;
                    genderChart.data.datasets[0].data = chartData.genderData;
                    genderChart.update();
                }
            });

            Livewire.on('update-deductions-chart', (data) => {
                const chartData = Array.isArray(data) ? data[0] : data;

                if (deductionsChart) {
                    deductionsChart.data.datasets[0].data = chartData.deductionsData;
                    deductionsChart.update();
                }
            });
        });
    </script>
</div>