<div>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                {{ isset($payroll) ? 'Editar Nómina' : 'Nueva Nómina' }}
            </h1>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Empleado -->
                <div class="md:col-span-2">
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Empleado</label>
                    <select id="employee_id" wire:model="form.employee_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccione un empleado</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                        @endforeach
                    </select>
                    @error('form.employee_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="status_id" wire:model="form.status_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccione un estado</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('form.status_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Subtotal -->
                <div>
                    <x-money-input model="form.subtotal" label="Subtotal (Servicio/Pago)" placeholder="$0" />
                </div>

                <!-- Bonos -->
                <div>
                    <x-money-input model="form.bonos" label="Bonos" placeholder="$0" />
                </div>

                <!-- Deducciones -->
                <div>
                    <x-money-input model="form.deductions" label="Deducciones" placeholder="$0" />
                </div>

                <!-- Total (Read-only, calculated) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total (Calculado)</label>
                    <div class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-900 font-bold">
                        {{-- We can display the calculated total here if we want real-time feedback,
                        but since calculation happens on save/update in Form, we might need
                        wire:change or updated hooks to show it live.
                        For now, let's just show what's in the form model. --}}
                        {{ \App\Services\MoneyFormatterService::format($form->total) }}
                        <span class="text-xs text-gray-500 font-normal ml-2">(Se actualiza al guardar)</span>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="md:col-span-2">
                    <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea id="observations" wire:model="form.observations" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"></textarea>
                    @error('form.observations')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @if(isset($payroll))
                @livewire('modules.cloud.components.model-attachments', [
                    'model' => $payroll,
                    'area' => 'finanzas'
                ])
            @else
                @livewire('modules.cloud.components.model-attachments-creator', [
                    'modelClass' => \App\Models\Payroll::class,
                    'areaSlug' => 'finanzas'
                ])
            @endif

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('finances.payrolls.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    {{ isset($payroll) ? 'Actualizar Nómina' : 'Guardar Nómina' }}
                </button>
            </div>
        </form>
    </div>

    {{-- Componente de notificaciones --}}
    @livewire('components.notification')

    {{-- Modal selector de carpetas --}}

</div>