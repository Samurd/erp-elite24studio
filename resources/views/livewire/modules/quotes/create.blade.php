<div>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                {{ isset($quote) ? 'Editar Cotización' : 'Nueva Cotización' }}
            </h1>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Contacto -->
                <div>
                    <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-2">Contacto</label>
                    <select id="contact_id" wire:model="form.contact_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccione un contacto</option>
                        @foreach($contacts as $contact)
                            <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                        @endforeach
                    </select>
                    @error('form.contact_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fecha de Emisión -->
                <div>
                    <label for="issued_at" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Emisión</label>
                    <input type="date" id="issued_at" wire:model="form.issued_at"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.issued_at')
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

                <!-- Total -->
                <div>
                    <x-money-input model="form.total" label="Total" placeholder="$0" />
                </div>
            </div>

            @if(isset($quote))
                @livewire('modules.cloud.components.model-attachments', [
                    'model' => $quote,
                    'area' => 'cotizaciones'
                ])
            @else
                  @livewire('modules.cloud.components.model-attachments-creator', [
                    'modelClass' => \App\Models\Quote::class,
                    'areaSlug' => 'cotizaciones'
                ])
            @endif

  

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('quotes.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    {{ isset($quote) ? 'Actualizar Cotización' : 'Guardar Cotización' }}
                </button>
            </div>
        </form>
    </div>
    @livewire('components.notification')

</div>