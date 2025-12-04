<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Editar KPI</h1>
                <p class="text-gray-600 mt-1">Actualiza la información del indicador clave de rendimiento</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('kpis.index') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Código de Protocolo -->
                <div>
                    <label for="protocol_code" class="block text-sm font-medium text-gray-700 mb-2">Código de Protocolo</label>
                    <input type="text" id="protocol_code" wire:model="form.protocol_code"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="Ej: PRO-001">
                    @error('form.protocol_code')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Nombre del Indicador -->
                <div>
                    <label for="indicator_name" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Indicador</label>
                    <input type="text" id="indicator_name" wire:model="form.indicator_name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="Ej: Tiempo de respuesta al cliente">
                    @error('form.indicator_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Valor Objetivo -->
                <div>
                    <label for="target_value" class="block text-sm font-medium text-gray-700 mb-2">Valor Objetivo</label>
                    <input type="number" id="target_value" wire:model="form.target_value" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="Ej: 95">
                    @error('form.target_value')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Periodicidad -->
                <div>
                    <label for="periodicity_days" class="block text-sm font-medium text-gray-700 mb-2">Periodicidad (días)</label>
                    <input type="number" id="periodicity_days" wire:model="form.periodicity_days"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="Ej: 30">
                    @error('form.periodicity_days')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Rol -->
                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">Rol Responsable</label>
                    <select id="role_id" wire:model="form.role_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Seleccionar rol</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('form.role_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <a href="{{ route('kpis.index') }}"
                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
                <button type="submit"
                        class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Actualizar KPI
                </button>
            </div>
        </form>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
</div>
