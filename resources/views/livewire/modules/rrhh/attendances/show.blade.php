<div>
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Detalle de Asistencia
            </h1>
            <div class="flex space-x-3">
                <a href="{{ route('rrhh.attendances.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <a href="{{ route('rrhh.attendances.edit', $attendance->id) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <button wire:click="delete" wire:confirm="¿Estás seguro de que quieres eliminar este registro?"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Eliminar
                </button>
            </div>
        </div>

        <!-- Attendance Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Empleado</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $attendance->employee->full_name ?? '-' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Fecha</label>
                    <p class="text-lg text-gray-900">
                        {{ $attendance->date ? \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') : '-' }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ $attendance->date ? \Carbon\Carbon::parse($attendance->date)->format('l') : '' }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Hora de Entrada</label>
                    <p class="text-lg text-gray-900">
                        {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Hora de Salida</label>
                    <p class="text-lg text-gray-900">
                        {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}
                    </p>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                @php
                    $checkIn = \Carbon\Carbon::parse($attendance->check_in);
                    $checkOut = \Carbon\Carbon::parse($attendance->check_out);
                    $hoursWorked = $checkIn->diffInHours($checkOut);
                    $minutesWorked = $checkIn->diffInMinutes($checkOut) % 60;
                @endphp

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Horas Trabajadas</label>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $hoursWorked }}h {{ $minutesWorked }}m
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Modalidad de Trabajo</label>
                    @if($attendance->modality)
                        <span
                            class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $attendance->modality->name }}
                        </span>
                    @else
                        <p class="text-lg text-gray-500">-</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                    @if($attendance->status)
                        <span
                            class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $attendance->status->name }}
                        </span>
                    @else
                        <p class="text-lg text-gray-500">-</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Registro</label>
                    <p class="text-sm text-gray-600">
                        {{ $attendance->created_at ? $attendance->created_at->format('d/m/Y H:i') : '-' }}
                    </p>
                </div>
            </div>
        </div>

        @if($attendance->observations)
            <div class="mt-6 pt-6 border-t">
                <label class="block text-sm font-medium text-gray-500 mb-2">Observaciones</label>
                <p class="text-gray-900 whitespace-pre-wrap">{{ $attendance->observations }}</p>
            </div>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
</div>