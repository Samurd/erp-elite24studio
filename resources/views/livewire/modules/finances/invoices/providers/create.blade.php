<div>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                {{ isset($invoiceProvider) ? 'Editar Factura de Proveedor' : 'Nueva Factura de Proveedor' }}
            </h1>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Código (readonly, auto-generated) -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Código <span class="text-xs text-gray-500">(auto-generado)</span>
                    </label>
                    <input type="text" id="code" wire:model="form.code" readonly
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                    @error('form.code')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fecha de Factura -->
                <div>
                    <label for="invoice_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de
                        Factura</label>
                    <input type="date" id="invoice_date" wire:model="form.invoice_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.invoice_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Proveedor -->
                <div>
                    <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-2">Proveedor</label>
                    <select id="contact_id" wire:model="form.contact_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccione un proveedor</option>
                        @foreach($providerContacts as $contact)
                            <option value="{{ $contact->id }}">
                                {{ $contact->name }} {{ $contact->company ? '- ' . $contact->company : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Solo contactos con tipo de relación: <span class="font-semibold">Proveedor</span>
                    </p>
                    @error('form.contact_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Total -->
                <div>
                    <x-money-input model="form.total" label="Total" placeholder="$0" />
                </div>

                <!-- Método de Pago -->
                <div>
                    <label for="method_payment" class="block text-sm font-medium text-gray-700 mb-2">Método de
                        Pago</label>
                    <input type="text" id="method_payment" wire:model="form.method_payment"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Ej: Transferencia, Efectivo, Tarjeta">
                    @error('form.method_payment')
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

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea id="description" wire:model="form.description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Descripción de la factura..."></textarea>
                    @error('form.description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @if(isset($invoiceProvider))
                @livewire('modules.cloud.components.model-attachments', [
                    'model' => $invoiceProvider,
                    'area' => 'finanzas'
                ])
            @else
                @livewire('modules.cloud.components.model-attachments-creator', [
                    'modelClass' => \App\Models\Invoice::class,
                    'areaSlug' => 'finanzas'
                ])
            @endif

            <div class="flex justify-end gap-3">
                <a href="{{ route('finances.invoices.providers.index') }}" class="btn btn-neutral">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ $isEdit ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </form>


    </div>


</div>