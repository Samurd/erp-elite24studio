<div>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                {{ isset($attendance) ? 'Editar Asistencia' : 'Registrar Asistencia' }}
            </h1>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Empleado -->
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Empleado</label>
                    <select id="employee_id" wire:model="form.employee_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Seleccione un empleado</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                    @error('form.employee_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fecha -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                    <input type="date" id="date" wire:model="form.date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('form.date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Hora de Entrada -->
                <div>
                    <label for="check_in" class="block text-sm font-medium text-gray-700 mb-2">Hora de Entrada</label>
                    <input type="time" id="check_in" wire:model="form.check_in"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('form.check_in')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Hora de Salida -->
                <div>
                    <label for="check_out" class="block text-sm font-medium text-gray-700 mb-2">Hora de Salida</label>
                    <input type="time" id="check_out" wire:model="form.check_out"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    @error('form.check_out')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="status_id" wire:model="form.status_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Seleccione un estado</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('form.status_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Modalidad de Trabajo -->
                <div>
                    <label for="modality_id" class="block text-sm font-medium text-gray-700 mb-2">Modalidad de
                        Trabajo</label>
                    <select id="modality_id" wire:model="form.modality_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Seleccione una modalidad</option>
                        @foreach($modalityOptions as $modality)
                            <option value="{{ $modality->id }}">{{ $modality->name }}</option>
                        @endforeach
                    </select>
                    @error('form.modality_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Observaciones -->
                <div class="md:col-span-2">
                    <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea id="observations" wire:model="form.observations" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Observaciones adicionales..."></textarea>
                    @error('form.observations')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('rrhh.attendances.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    {{ isset($attendance) ? 'Actualizar Asistencia' : 'Registrar Asistencia' }}
                </button>
            </div>
        </form>
    </div>
</div>