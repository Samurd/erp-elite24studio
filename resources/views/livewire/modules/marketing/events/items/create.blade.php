<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ isset($eventItem) ? 'Editar Ítem' : 'Nuevo Ítem' }} del Evento: {{ $event->name }}
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ isset($eventItem) ? 'Actualiza la información del ítem' : 'Complete los datos para registrar un nuevo ítem' }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('marketing.events.show', $event->id) }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver al Evento
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Descripción -->
                <div class="lg:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <input type="text" id="description" wire:model="form.description"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Descripción del ítem">
                    @error('form.description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Unidad -->
                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">Unidad</label>
                    <select id="unit_id" wire:model="form.unit_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar unidad</option>
                        @foreach($unitOptions as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                    @error('form.unit_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Cantidad -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Cantidad</label>
                    <input type="number" id="quantity" wire:model="form.quantity" step="0.01" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="0">
                    @error('form.quantity')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Precio Unitario -->
                <div>
                    <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">Precio Unitario</label>
                    <x-money-input model="form.unit_price" label="" placeholder="0.00" />
                    @error('form.unit_price')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Precio Total -->
                <div>
                    <label for="total_price" class="block text-sm font-medium text-gray-700 mb-2">Precio Total</label>
                    <x-money-input model="form.total_price" label="" placeholder="0.00" />
                    @error('form.total_price')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>


                <div class="lg:col-span-2">
                    @if(isset($eventItem))
                    @livewire('modules.cloud.components.model-attachments', [
                        'model' => $eventItem,
                        'area' => 'marketing'
                    ])
                @else
                @livewire('modules.cloud.components.model-attachments-creator', [
                    'modelClass' => \App\Models\EventItem::class,
                    'areaSlug' => 'marketing'
                ])
                @endif
                </div>
             </div>

            <!-- Botones de Acción -->
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <a href="{{ route('marketing.events.show', $event->id) }}"
                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
                <button type="submit"
                        class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ isset($eventItem) ? 'Actualizar' : 'Guardar' }} Ítem
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
