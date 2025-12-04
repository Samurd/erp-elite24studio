<div class="p-8 bg-gray-100 min-h-screen space-y-8">
    <!-- GRID PRINCIPAL -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <!-- COLUMNA IZQUIERDA: INGRESOS / GASTOS -->
        <div class="xl:col-span-4 space-y-6">
            <h2 class="text-2xl font-bold text-gray-800">Balance General</h2>

            <!-- INGRESO NETO -->
            <div
                class="bg-gradient-to-r from-black to-yellow-600 text-white rounded-2xl shadow-lg p-6 min-h-[250px] flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wide opacity-90">Ingresos neto empresarial</h3>
                    <div class="text-5xl font-bold my-3 flex gap-2"><span class="text-sm">$</span>
                        {{ App\Services\MoneyFormatterService::format($netIncome, false) }}</div>
                    <p class="text-sm opacity-90 leading-snug">
                        Consulta el total de ganancias totales de la
                        empresa, después de deducir los gastos
                        operativos y obligaciones financieras.
                    </p>
                </div>

                <a href="{{route('finances.net.index')}}">
                    <button class="text-sm text-yellow-600 underline mt-2 self-start">Ver Detalle</button>
                </a>
            </div>

            <!-- INGRESO BRUTO -->
            <div class="bg-white rounded-2xl shadow-lg p-6 min-h-[250px] flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wide opacity-90">Ingresos bruto empresarial</h3>
                    <div class="text-5xl font-bold my-3 flex gap-2"><span class="text-sm">$</span>
                        {{ App\Services\MoneyFormatterService::format($totalGrossIncome, false) }}</div>
                    <p class="text-sm text-gray-500 leading-snug">
                        Revisa el monta total de Ingresos generados
                        antes de aplicar deducciones y costos
                        operativos.
                    </p>
                </div>
                <a href="{{route('finances.gross.index')}}">
                    <button class="text-sm text-yellow-600 underline mt-2 self-start">Ver Detalle</button>
                </a>
            </div>


            <!-- GASTOS -->
            <div class="bg-white rounded-2xl shadow-lg p-6 min-h-[250px] flex flex-col justify-between">
                <div>
                    <h3 class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Gastos Totales</h3>
                    <div class="text-5xl font-bold text-gray-800 my-3 flex gap-2"><span class="text-sm">$</span>
                        {{ App\Services\MoneyFormatterService::format($totalExpenses, false) }}</div>
                    <p class="text-sm text-gray-500 leading-snug">
                        Egresos registrados en el período actual: nómina, suministros, servicios y otros gastos
                        operativos.
                    </p>
                </div>

                <a href="{{route('finances.expenses.index')}}">
                    <button class="text-sm text-yellow-600 underline mt-2 self-start">Ver Detalle</button>
                </a>
            </div>
        </div>

        <!-- CENTRO: TRANSACCIONES -->
        <div class="xl:col-span-6 bg-white rounded-2xl shadow-lg p-6 flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-xl">Transacciones Recientes</h3>
            </div>

            <div class="overflow-y-auto border rounded-lg flex-1">
                <table class="w-full text-sm">
                    <thead
                        class="bg-gradient-to-r from-black to-yellow-600 text-white uppercase font-semibold sticky top-0">
                        <tr>
                            <th class="p-2 text-left">Descripción</th>
                            <th class="p-2 text-center">Fecha</th>
                            <th class="p-2 text-center">Monto</th>
                            <th class="p-2 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $transaction)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $transaction['description'] }}
                                    <span class="block text-xs text-gray-500">{{ $transaction['subtitle'] }}</span>
                                </td>
                                <td class="p-2 text-center">
                                    {{ \Carbon\Carbon::parse($transaction['date'])->diffForHumans() }}
                                </td>
                                <td
                                    class="p-2 text-center {{ $transaction['is_income'] ? 'text-green-500' : 'text-red-500' }} font-semibold">
                                    {{ $transaction['is_income'] ? '+' : '-' }}
                                    ${{ App\Services\MoneyFormatterService::format($transaction['amount'], false) }}
                                </td>
                                <td class="p-2 text-center">
                                    @if(str_contains(strtolower($transaction['status']), 'completada') || str_contains(strtolower($transaction['status']), 'pagada'))
                                        <span
                                            class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded">{{ $transaction['status'] }}</span>
                                    @elseif(str_contains(strtolower($transaction['status']), 'pendiente'))
                                        <span
                                            class="bg-yellow-100 text-yellow-600 text-xs px-2 py-1 rounded">{{ $transaction['status'] }}</span>
                                    @else
                                        <span
                                            class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">{{ $transaction['status'] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-500">No hay transacciones recientes</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- DERECHA: FACTURACIÓN -->
        <div class="xl:col-span-2 space-y-6 w-full">
            <h3 class="font-bold text-xl text-gray-800">Facturación</h3>

            <div class="bg-white p-4 rounded-2xl shadow">
                <div class="flex justify-between text-sm font-semibold mb-2">
                    <span>Clientes</span>
                    <span class="text-gray-400">Reciente</span>
                </div>
                @if(empty($clientInvoicesData['data'] ?? []))
                    <div class="text-center text-gray-500 py-8">
                        <p>No hay datos disponibles</p>
                    </div>
                @else
                    <div class="flex justify-center items-center">
                        <canvas id="chartClientes" height="150" width="150"></canvas>
                    </div>
                @endif
                <a href="{{ route('finances.invoices.clients.index') }}"
                    class="text-yellow-600 text-xs underline mt-2 block">Ver Detalle</a>
            </div>

            <div class="bg-white p-4 rounded-2xl shadow">
                <div class="flex justify-between text-sm font-semibold mb-2">
                    <span>Cuentas de Cobro</span>
                    <span class="text-gray-400">Reciente</span>
                </div>
                @if(empty($billingAccountsData['data'] ?? []))
                    <div class="text-center text-gray-500 py-8">
                        <p>No hay datos disponibles</p>
                    </div>
                @else
                    <div class="flex justify-center items-center">
                        <canvas id="chartCuentasCobro" height="150" width="150"></canvas>
                    </div>
                @endif
                <a href="{{ route('finances.invoices.clients.billing-accounts.index') }}"
                    class="text-yellow-600 text-xs underline mt-2 block">Ver Detalle</a>
            </div>

            <div class="bg-white p-4 rounded-2xl shadow">
                <div class="flex justify-between text-sm font-semibold mb-2">
                    <span>Proveedores</span>
                    <span class="text-gray-400">Reciente</span>
                </div>
                @if(empty($providerInvoicesData['data'] ?? []))
                    <div class="text-center text-gray-500 py-8">
                        <p>No hay datos disponibles</p>
                    </div>
                @else
                    <div class="flex justify-center items-center">
                        <canvas id="chartProveedores" height="150" width="150"></canvas>
                    </div>
                @endif
                <a href="{{ route('finances.invoices.providers.index') }}"
                    class="text-yellow-600 text-xs underline mt-2 block">Ver Detalle</a>
            </div>
        </div>
    </div>

    <!-- BLOQUES INFERIORES -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        <!-- Nómina -->
        <div class="bg-white p-6 rounded-2xl col-span-2 shadow-lg flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-lg mb-3">Gestión de Nómina</h3>
                @if(empty($payrollPaymentsData['data'] ?? []) && empty($payrollGenderData['data'] ?? []))
                    <div class="text-center text-gray-500 py-8">
                        <p>No hay datos de nómina disponibles</p>
                    </div>
                @else
                    <div class="flex items-center justify-center gap-8">
                        @if(!empty($payrollPaymentsData['data'] ?? []))
                            <canvas id="chartNominaPagos" height="120" width="120"></canvas>
                        @endif
                        @if(!empty($payrollGenderData['data'] ?? []))
                            <canvas id="chartNominaSexo" height="120" width="120"></canvas>
                        @endif
                    </div>
                @endif
            </div>
            <a href="{{ route('finances.payrolls.stats') }}"
                class="text-yellow-600 text-sm underline mt-3 self-start">Ver Estadísticas</a>
        </div>

        <!-- Indicadores Financieros -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="font-bold text-lg mb-3">Analisis Financieros</h3>
            <ul class="space-y-2">
                <li><a href="{{ route('finances.payrolls.index') }}"
                        class="bg-gray-800 text-white px-3 py-1 rounded inline-block">Control Salarial</a></li>
                <li><a href="{{ route('finances.taxes.index') }}"
                        class="bg-gray-800 text-white px-3 py-1 rounded inline-block">Impuestos laborales</a></li>
            </ul>
        </div>

        <!-- Cumplimiento Fiscal -->
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="font-bold text-lg mb-3">Cumplimiento Fiscal</h3>
            <ul class="space-y-2">
                <li><a href="{{ route('finances.audits.index') }}"
                        class="bg-gray-800 text-white px-3 py-1 rounded inline-block">Auditorías</a></li>
                <li><a href="{{ route('finances.norms.index') }}"
                        class="bg-gray-800 text-white px-3 py-1 rounded inline-block">Normas NIF</a></li>
            </ul>
        </div>


        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <ul class="space-y-2">
                <li><a href="{{ route('finances.invoices.index') }}"
                        class="bg-gray-800 text-white px-3 py-1 rounded">Registro de facturas</a></li>
                <li>
                    <button
                        class="bg-gradient-to-r from-black to-yellow-600 text-white px-3 py-1 rounded flex flex-col gap-1 items-center">
                        <span>Sistema de facturación</span>
                        <p class="text-xs text-gray-200 font-semibold">Serás redirigido al Software de
                            Facturacion- DIAN</p>
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>


<!-- CHARTS -->
<script>
    // Declarar datos de charts como variables globales (fuera del DOMContentLoaded)
    const chartDataClient = {
        colors: {!! json_encode($clientInvoicesData['colors'] ?? []) !!},
        data: {!! json_encode($clientInvoicesData['data'] ?? []) !!},
        labels: {!! json_encode($clientInvoicesData['labels'] ?? []) !!}
    };

    const chartDataBilling = {
        colors: {!! json_encode($billingAccountsData['colors'] ?? []) !!},
        data: {!! json_encode($billingAccountsData['data'] ?? []) !!},
        labels: {!! json_encode($billingAccountsData['labels'] ?? []) !!}
    };

    const chartDataProvider = {
        colors: {!! json_encode($providerInvoicesData['colors'] ?? []) !!},
        data: {!! json_encode($providerInvoicesData['data'] ?? []) !!},
        labels: {!! json_encode($providerInvoicesData['labels'] ?? []) !!}
    };

    const chartDataPayroll = {
        colors: {!! json_encode($payrollPaymentsData['colors'] ?? []) !!},
        data: {!! json_encode($payrollPaymentsData['data'] ?? []) !!},
        labels: {!! json_encode($payrollPaymentsData['labels'] ?? []) !!}
    };

    const chartDataGender = {
        colors: {!! json_encode($payrollGenderData['colors'] ?? []) !!},
        data: {!! json_encode($payrollGenderData['data'] ?? []) !!},
        labels: {!! json_encode($payrollGenderData['labels'] ?? []) !!}
    };

    document.addEventListener('DOMContentLoaded', () => {
        // Registrar el plugin de ChartDataLabels
        Chart.register(ChartDataLabels);

        // Configuración base de charts
        const baseOptions = {
            responsive: false, // Cambiar a false para evitar scroll infinito
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: { enabled: true },
                datalabels: {
                    color: '#fff',
                    backgroundColor: '#374151',
                    borderRadius: 6,
                    padding: 4,
                    font: { weight: 'bold', size: 8 },
                    formatter: (value, context) => {
                        const label = context.chart.data.labels[context.dataIndex];
                        return `${label}\n${value}%`;
                    },
                    align: 'center',
                    anchor: 'center',
                    textAlign: 'center',
                    clip: false,
                }
            }
        };

        const donut = (id, colors, data, labels) => {
            const element = document.getElementById(id);
            if (!element) return; // Si el elemento no existe, no renderizar

            new Chart(element, {
                type: 'doughnut',
                data: { labels, datasets: [{ data, backgroundColor: colors }] },
                options: baseOptions
            });
        };

        // Renderizar charts solo si tienen datos
        if (chartDataClient.data.length > 0) {
            donut('chartClientes', chartDataClient.colors, chartDataClient.data, chartDataClient.labels);
        }

        if (chartDataBilling.data.length > 0) {
            donut('chartCuentasCobro', chartDataBilling.colors, chartDataBilling.data, chartDataBilling.labels);
        }

        if (chartDataProvider.data.length > 0) {
            donut('chartProveedores', chartDataProvider.colors, chartDataProvider.data, chartDataProvider.labels);
        }

        if (chartDataPayroll.data.length > 0) {
            donut('chartNominaPagos', chartDataPayroll.colors, chartDataPayroll.data, chartDataPayroll.labels);
        }

        if (chartDataGender.data.length > 0) {
            donut('chartNominaSexo', chartDataGender.colors, chartDataGender.data, chartDataGender.labels);
        }
    });
</script>