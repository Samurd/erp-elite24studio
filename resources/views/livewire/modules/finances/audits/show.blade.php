<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detalles de la Auditoría</h1>
                <p class="text-gray-600 mt-1">Información completa y documentos asociados</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('finances.audits.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <a href="{{ route('finances.audits.edit', $audit->id) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
            </div>
        </div>
    </div>

    <!-- Audit Details -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            <i class="fas fa-info-circle text-green-600 mr-2"></i>Información General
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Objetivo (Monto)</label>
                <p class="text-lg font-semibold text-gray-900">
                    {{ \App\Services\MoneyFormatterService::format($audit->objective) }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tipo</label>
                <p class="text-lg font-semibold text-gray-900">{{ $audit->type->name ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                @if($audit->status)
                    <span class="px-3 py-1 text-sm font-semibold rounded-full inline-block"
                        style="background-color: {{ $audit->status->color }}20; color: {{ $audit->status->color }}">
                        {{ $audit->status->name }}
                    </span>
                @else
                    <span class="text-gray-900">-</span>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Lugar</label>
                <p class="text-gray-900">{{ $audit->place ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Registro</label>
                <p class="text-gray-900">{{ $audit->date_register->format('d/m/Y') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Auditoría</label>
                <p class="text-gray-900">{{ $audit->date_audit->format('d/m/Y') }}</p>
            </div>

            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Observaciones</label>
                <p class="text-gray-900 whitespace-pre-line">{{ $audit->observations ?? 'Sin observaciones' }}</p>
            </div>
        </div>
    </div>

    <!-- Files Section -->
    <livewire:modules.cloud.components.model-files :model="$audit" />
</div>