<div>
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detalles del Contrato</h1>
                <p class="text-gray-600 mt-1">Información detallada del contrato</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('rrhh.contracts.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <a href="{{ route('rrhh.contracts.edit', $contract->id) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-pen mr-2"></i>Editar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Employee Info -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Información del Empleado</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Nombre Completo</dt>
                        <dd class="text-sm text-gray-900">{{ $contract->employee->formatted_full_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Identificación</dt>
                        <dd class="text-sm text-gray-900">{{ $contract->employee->identification_number }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="text-sm text-gray-900">
                            {{ $contract->employee->work_email ?? $contract->employee->personal_email }}
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Contract Info -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Detalles del Contrato</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Tipo de Contrato</dt>
                        <dd class="text-sm text-gray-900">{{ $contract->type->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Categoría</dt>
                        <dd class="text-sm text-gray-900">{{ $contract->category->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Estado</dt>
                        <dd class="text-sm">
                            @if($contract->status)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    style="background-color: {{ $contract->status->color }}20; color: {{ $contract->status->color }}">
                                    {{ $contract->status->name }}
                                </span>
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Fecha Inicio</dt>
                        <dd class="text-sm text-gray-900">{{ $contract->start_date->format('d/m/Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Fecha Fin</dt>
                        <dd class="text-sm text-gray-900">
                            {{ $contract->end_date ? $contract->end_date->format('d/m/Y') : 'Indefinido' }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Monto</dt>
                        <dd class="text-sm text-gray-900">
                            {{ \App\Services\MoneyFormatterService::format($contract->amount) }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Horario</dt>
                        <dd class="text-sm text-gray-900">{{ $contract->schedule ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Files Section -->
        <livewire:modules.cloud.components.model-files :model="$contract" />

        <!-- Metadata -->
        <div class="mt-8 pt-6 border-t border-gray-200 text-xs text-gray-500 flex justify-between">
            <div>
                Registrado por: {{ $contract->registeredBy->name ?? 'Sistema' }}
            </div>
            <div>
                Creado el: {{ $contract->created_at->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</div>