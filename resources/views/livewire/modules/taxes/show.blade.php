<div>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detalles del Impuesto</h1>
            <div class="flex space-x-3">
                <a href="{{ route('finances.taxes.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <a href="{{ route('finances.taxes.edit', $taxRecord->id) }}"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-pen mr-2"></i>Editar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- ID -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">ID</label>
                <div class="text-gray-900 text-lg">#{{ $taxRecord->id }}</div>
            </div>

            <!-- Entidad -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Entidad</label>
                <div class="text-gray-900 text-lg">{{ $taxRecord->entity }}</div>
            </div>

            <!-- Tipo -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tipo</label>
                <div class="text-gray-900 text-lg">
                    @if($taxRecord->type)
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            {{ $taxRecord->type->name }}
                        </span>
                    @else
                        <span class="text-gray-500">-</span>
                    @endif
                </div>
            </div>

            <!-- Fecha -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha</label>
                <div class="text-gray-900 text-lg">
                    {{ $taxRecord->date ? $taxRecord->date->format('d/m/Y') : '-' }}
                </div>
            </div>

            <!-- Base Gravable -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Base Gravable</label>
                <div class="text-gray-900 text-lg">
                    {{ \App\Services\MoneyFormatterService::format($taxRecord->base) }}
                </div>
            </div>

            <!-- Porcentaje -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Porcentaje</label>
                <div class="text-gray-900 text-lg">{{ $taxRecord->porcentage }}%</div>
            </div>

            <!-- Monto -->
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Monto Retenido/Pagado</label>
                <div class="text-gray-900 text-lg font-bold text-green-600">
                    {{ \App\Services\MoneyFormatterService::format($taxRecord->amount) }}
                </div>
            </div>

            <!-- Observaciones -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Observaciones</label>
                <div class="text-gray-900 bg-gray-50 p-4 rounded-lg">
                    {{ $taxRecord->observations ?: 'Sin observaciones' }}
                </div>
            </div>

            <!-- Files Section -->
            <livewire:modules.cloud.components.model-files :model="$taxRecord" />
        </div>
    </div>
</div>