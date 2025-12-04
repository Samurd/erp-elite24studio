<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detalles de Vacación/Permiso</h1>
                <p class="text-gray-600 mt-1">Información completa del colaborador y panel histórico</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('rrhh.holidays.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <a href="{{ route('rrhh.holidays.edit', $holiday->id) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <button wire:click="delete" wire:confirm="¿Estás seguro de que quieres eliminar este festivo?"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Holiday Details -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            <i class="fas fa-info-circle text-green-600 mr-2"></i>Información del Colaborador
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Nombre del Colaborador</label>
                <p class="text-lg font-semibold text-gray-900">{{ $holiday->employee->full_name }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Cargo</label>
                <p class="text-lg font-semibold text-gray-900">{{ $holiday->employee->job_title }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Tipo</label>
                @if($holiday->type)
                    <span class="px-3 py-1 text-sm font-semibold rounded-full inline-block"
                        style="background-color: {{ $holiday->type->color }}20; color: {{ $holiday->type->color }}">
                        {{ $holiday->type->name }}
                    </span>
                @else
                    <p class="text-gray-500">-</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha Inicio</label>
                <p class="text-lg font-semibold text-gray-900">
                    {{ $holiday->start_date->format('d/m/Y') }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha Fin</label>
                <p class="text-lg font-semibold text-gray-900">
                    {{ $holiday->end_date->format('d/m/Y') }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Total de Días</label>
                @php
                    $days = $holiday->start_date->diffInDays($holiday->end_date) + 1;
                @endphp
                <p class="text-lg font-semibold text-green-600">
                    {{ $days }} {{ $days == 1 ? 'día' : 'días' }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                @if($holiday->status)
                    <span class="px-3 py-1 text-sm font-semibold rounded-full inline-block"
                        style="background-color: {{ $holiday->status->color }}20; color: {{ $holiday->status->color }}">
                        {{ $holiday->status->name }}
                    </span>
                @else
                    <p class="text-gray-500">-</p>
                @endif
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Responsable de Aprobación</label>
                <p class="text-lg font-semibold text-gray-900">{{ $holiday->approver->name ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Creado</label>
                <p class="text-gray-700">{{ $holiday->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Última Actualización</label>
                <p class="text-gray-700">{{ $holiday->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Files Section -->
    <livewire:modules.cloud.components.model-files :model="$holiday" />

    <!-- Hist orical Panel -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            <i class="fas fa-history text-green-600 mr-2"></i>Panel Histórico
        </h2>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-600 font-medium">Total Días</p>
                        <p class="text-3xl font-bold text-green-700">{{ $totalDays }}</p>
                    </div>
                    <div class="bg-green-200 p-3 rounded-full">
                        <i class="fas fa-calendar-days text-green-700 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600 font-medium">Última Fecha</p>
                        <p class="text-3xl font-bold text-blue-700">{{ $lastDate }}</p>
                    </div>
                    <div class="bg-blue-200 p-3 rounded-full">
                        <i class="fas fa-clock text-blue-700 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-600 font-medium">Solicitudes en Año</p>
                        <p class="text-3xl font-bold text-purple-700">{{ $requestsInYear }}</p>
                    </div>
                    <div class="bg-purple-200 p-3 rounded-full">
                        <i class="fas fa-file-invoice text-purple-700 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Año</label>
            <div class="flex gap-4">
                <select wire:model.live="historicalYear"
                    class="w-64 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    @foreach($yearOptions as $yearOption)
                        <option value="{{ $yearOption }}">{{ $yearOption }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Historical Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Colaborador
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cargo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha Inicio
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha Fin
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Días
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($historicalData as $record)
                        @php
                            $start = \Carbon\Carbon::parse($record->start_date);
                            $end = \Carbon\Carbon::parse($record->end_date);
                            $recordDays = $start->diffInDays($end) + 1;
                        @endphp
                        <tr class="hover:bg-gray-50 {{ $record->id == $holiday->id ? 'bg-green-50' : '' }}">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $record->employee->full_name }}
                                    @if($record->id == $holiday->id)
                                        <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                            Actual
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $record->employee->job_title }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($record->type)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        style="background-color: {{ $record->type->color }}20; color: {{ $record->type->color }}">
                                        {{ $record->type->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $start->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $end->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                {{ $recordDays }} {{ $recordDays == 1 ? 'día' : 'días' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($record->status)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        style="background-color: {{ $record->status->color }}20; color: {{ $record->status->color }}">
                                        {{ $record->status->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron registros para el año seleccionado
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($historicalData && $historicalData->hasPages())
            <div class="mt-4">
                {{ $historicalData->links() }}
            </div>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 mt-4">
            {{ session('success') }}
        </div>
    @endif
</div>