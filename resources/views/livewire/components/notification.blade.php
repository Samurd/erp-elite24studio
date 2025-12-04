<div x-data="{ show: @entangle('show'), duration: @entangle('duration') }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-x-full"
     x-transition:enter-end="opacity-100 transform translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-x-0"
     x-transition:leave-end="opacity-0 transform translate-x-full"
     x-init="$watch('show', value => {
        if (value && duration > 0) {
            setTimeout(() => {
                $wire.hideNotification()
            }, duration)
        }
     })"
     class="fixed top-4 right-4 z-50 max-w-sm w-full"
     style="display: none;">

    @if($type === 'success')
        <div class="bg-green-50 border border-green-200 rounded-lg shadow-lg p-4 flex items-start space-x-3">
            <!-- Icono -->
            <div class="flex-shrink-0">
                <x-fas-check-circle class="w-6 h-6 text-green-500" />
            </div>

            <!-- Contenido -->
            <div class="flex-1">
                <p class="text-sm font-medium text-green-800">{{ $message }}</p>
            </div>

            <!-- Botón cerrar -->
            <button wire:click="hideNotification"
                    class="flex-shrink-0 p-1 rounded hover:bg-green-200 transition">
                <x-fas-times class="w-4 h-4 text-green-600" />
            </button>
        </div>
    @elseif($type === 'error')
        <div class="bg-red-50 border border-red-200 rounded-lg shadow-lg p-4 flex items-start space-x-3">
            <!-- Icono -->
            <div class="flex-shrink-0">
                <x-fas-exclamation-circle class="w-6 h-6 text-red-500" />
            </div>

            <!-- Contenido -->
            <div class="flex-1">
                <p class="text-sm font-medium text-red-800">{{ $message }}</p>
            </div>

            <!-- Botón cerrar -->
            <button wire:click="hideNotification"
                    class="flex-shrink-0 p-1 rounded hover:bg-red-200 transition">
                <x-fas-times class="w-4 h-4 text-red-600" />
            </button>
        </div>
    @elseif($type === 'warning')
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg shadow-lg p-4 flex items-start space-x-3">
            <!-- Icono -->
            <div class="flex-shrink-0">
                <x-fas-exclamation-triangle class="w-6 h-6 text-yellow-500" />
            </div>

            <!-- Contenido -->
            <div class="flex-1">
                <p class="text-sm font-medium text-yellow-800">{{ $message }}</p>
            </div>

            <!-- Botón cerrar -->
            <button wire:click="hideNotification"
                    class="flex-shrink-0 p-1 rounded hover:bg-yellow-200 transition">
                <x-fas-times class="w-4 h-4 text-yellow-600" />
            </button>
        </div>
    @else
        <div class="bg-blue-50 border border-blue-200 rounded-lg shadow-lg p-4 flex items-start space-x-3">
            <!-- Icono -->
            <div class="flex-shrink-0">
                <x-fas-info-circle class="w-6 h-6 text-blue-500" />
            </div>

            <!-- Contenido -->
            <div class="flex-1">
                <p class="text-sm font-medium text-blue-800">{{ $message }}</p>
            </div>

            <!-- Botón cerrar -->
            <button wire:click="hideNotification"
                    class="flex-shrink-0 p-1 rounded hover:bg-blue-200 transition">
                <x-fas-times class="w-4 h-4 text-blue-600" />
            </button>
        </div>
    @endif
</div>
