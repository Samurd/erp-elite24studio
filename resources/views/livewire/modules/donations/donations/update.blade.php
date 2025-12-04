<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Editar Donación</h1>
                <p class="text-gray-600 mt-1">Modificar información de la donación #{{ $donation->id }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('donations.donations.index') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre del donante -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del Donante <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           wire:model="form.name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                           placeholder="Ingrese el nombre del donante o entidad">
                    @error('form.name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Campaña -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Campaña
                    </label>
                    <select wire:model="form.campaign_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar campaña</option>
                        @foreach($campaigns as $campaign)
                            <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                        @endforeach
                    </select>
                    @error('form.campaign_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Monto -->
                <div>
                    <x-money-input
                        model="form.amount"
                        label="Monto"
                        placeholder="$0.00"
                    />
                </div>

                <!-- Método de pago -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Método de Pago <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           wire:model="form.payment_method"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                           placeholder="Ej: Transferencia, Efectivo, Tarjeta">
                    @error('form.payment_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Donación <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           wire:model="form.date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Certificado -->
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox"
                               wire:model="form.certified"
                               class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Certificado</span>
                    </label>
                </div>
            </div>


            @livewire('modules.cloud.components.model-attachments', [
                'model' => $donation,
                'area' => 'donaciones'
            ])

            <!-- Botones -->
            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('donations.donations.index') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Actualizar Donación
                </button>
            </div>
        </form>
    </div>
</div>
