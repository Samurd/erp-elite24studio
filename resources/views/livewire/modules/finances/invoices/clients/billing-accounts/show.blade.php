<div>
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Detalle de Cuenta de Cobro
            </h1>
            <div class="flex space-x-3">
                <a href="{{ route('finances.invoices.clients.billing-accounts.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <a href="{{ route('finances.invoices.clients.billing-accounts.edit', $billingAccount->id) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <button wire:click="delete" wire:confirm="¿Estás seguro de que quieres eliminar esta cuenta de cobro?"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Eliminar
                </button>
            </div>
        </div>

        <!-- Invoice Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Código</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $billingAccount->code }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Cliente</label>
                    <p class="text-lg font-medium text-gray-900">{{ $billingAccount->contact->name ?? '-' }}</p>
                    @if($billingAccount->contact && $billingAccount->contact->company)
                        <p class="text-sm text-gray-600">{{ $billingAccount->contact->company }}</p>
                    @endif
                    @if($billingAccount->contact && $billingAccount->contact->email)
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-envelope mr-1"></i>{{ $billingAccount->contact->email }}
                        </p>
                    @endif
                    @if($billingAccount->contact && $billingAccount->contact->phone)
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-phone mr-1"></i>{{ $billingAccount->contact->phone }}
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Fecha</label>
                    <p class="text-lg text-gray-900">
                        {{ $billingAccount->invoice_date ? \Carbon\Carbon::parse($billingAccount->invoice_date)->format('d/m/Y') : '-' }}
                    </p>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Total</label>
                    <p class="text-2xl font-bold text-yellow-600">
                        {{ \App\Services\MoneyFormatterService::format($billingAccount->total) }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Método de Pago</label>
                    <p class="text-lg text-gray-900">{{ $billingAccount->method_payment ?? '-' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Estado</label>
                    @if($billingAccount->status)
                        <span
                            class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $billingAccount->status->name }}
                        </span>
                    @else
                        <p class="text-lg text-gray-500">-</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Creado por</label>
                    <p class="text-lg text-gray-900">{{ $billingAccount->createdBy->name ?? '-' }}</p>
                    <p class="text-sm text-gray-600">
                        {{ $billingAccount->created_at ? $billingAccount->created_at->format('d/m/Y H:i') : '-' }}
                    </p>
                </div>
            </div>
        </div>

        @if($billingAccount->description)
            <div class="mt-6 pt-6 border-t">
                <label class="block text-sm font-medium text-gray-500 mb-2">Descripción</label>
                <p class="text-gray-900 whitespace-pre-wrap">{{ $billingAccount->description }}</p>
            </div>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Files Section -->
    <livewire:modules.cloud.components.model-files :model="$billingAccount" />
</div>