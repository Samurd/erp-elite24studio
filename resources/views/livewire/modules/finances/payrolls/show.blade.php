<div>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detalles de Nómina</h1>
            <div class="flex space-x-3">
                <a href="{{ route('finances.payrolls.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <a href="{{ route('finances.payrolls.edit', $payroll->id) }}"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-pen mr-2"></i>Editar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- ID -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">ID</label>
                <div class="text-gray-900 text-lg">#{{ $payroll->id }}</div>
            </div>

            <!-- Empleado -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Empleado</label>
                <div class="text-gray-900 text-lg">{{ $payroll->employee->full_name ?? 'N/A' }}</div>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                <div class="text-gray-900 text-lg">
                    @if($payroll->status)
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            {{ $payroll->status->name }}
                        </span>
                    @else
                        <span class="text-gray-500">-</span>
                    @endif
                </div>
            </div>

            <!-- Fecha -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Creación</label>
                <div class="text-gray-900 text-lg">
                    {{ $payroll->created_at ? $payroll->created_at->format('d/m/Y H:i') : '-' }}
                </div>
            </div>

            <!-- Subtotal -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Subtotal (Servicio/Pago)</label>
                <div class="text-gray-900 text-lg">
                    {{ \App\Services\MoneyFormatterService::format($payroll->subtotal) }}
                </div>
            </div>

            <!-- Bonos -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Bonos</label>
                <div class="text-gray-900 text-lg">
                    {{ \App\Services\MoneyFormatterService::format($payroll->bonos) }}
                </div>
            </div>

            <!-- Deducciones -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Deducciones</label>
                <div class="text-gray-900 text-lg text-red-600">
                    -{{ \App\Services\MoneyFormatterService::format($payroll->deductions) }}
                </div>
            </div>

            <!-- Total -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Total</label>
                <div class="text-gray-900 text-lg font-bold text-green-600">
                    {{ \App\Services\MoneyFormatterService::format($payroll->total) }}
                </div>
            </div>

            <!-- Observaciones -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Observaciones</label>
                <div class="text-gray-900 bg-gray-50 p-4 rounded-lg">
                    {{ $payroll->observations ?: 'Sin observaciones' }}
                </div>
            </div>

            <!-- Files Section -->
            <livewire:modules.cloud.components.model-files :model="$payroll" />
        </div>
    </div>
</div>