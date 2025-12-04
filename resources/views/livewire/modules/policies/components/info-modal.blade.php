<!-- MODAL INFO POLICY -->
<x-dialog-modal wire:model="showModal" maxWidth="2xl">
    <x-slot name="title">
        <div class="flex items-center gap-3 border-b pb-3">
            <div class="bg-blue-100 text-blue-600 p-2 rounded-full">
                <i class="fas fa-file-alt"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Detalles de la Política</h2>
        </div>
    </x-slot>

    <x-slot name="content">
        @if ($policy)
            <div class="mt-4 space-y-6">
                {{-- Información general --}}
                <div class="bg-gray-50 border rounded-lg p-4 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $policy->name }}</h3>

                    {{-- Estado y tipo --}}
                    <div class="flex flex-wrap gap-3 mt-3 text-sm">
                        <div class="flex items-center gap-1">
                            <span class="text-gray-500">Estado:</span>
                            <span class="px-3 py-1 rounded-full font-medium">
                                {{ $policy->status?->name ?? 'N/A' }}
                            </span>
                        </div>

                        <div class="flex items-center gap-1">
                            <span class="text-gray-500">Tipo:</span>
                            <span class="font-medium text-gray-800">
                                {{ $policy->type?->name ?? 'Sin tipo asignado' }}
                            </span>
                        </div>
                    </div>

                    {{-- Fechas y usuario asignado --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4 text-sm">
                        <div>
                            <span class="block text-gray-500">Fecha de Emisión:</span>
                            <span class="font-medium text-gray-800">
                                {{ $policy->issued_at ? \Carbon\Carbon::parse($policy->issued_at)->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>

                        <div>
                            <span class="block text-gray-500">Ultima Revisión:</span>
                            <span class="font-medium text-gray-800">
                                {{ $policy->reviewed_at ? \Carbon\Carbon::parse($policy->reviewed_at)->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>

                        <div>
                            <span class="block text-gray-500">Responsable:</span>
                            <span class="font-medium text-gray-800">
                                {{ $policy->assignedTo?->name ?? 'Sin asignar' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Observaciones --}}
                @if ($policy->description)
                    <div class="border rounded-lg p-4 bg-white shadow-sm">
                        <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-comment-dots text-gray-500"></i>
                            Observaciones
                        </h4>
                        <p class="mt-2 text-gray-700 leading-relaxed">
                            {{ $policy->description }}
                        </p>
                    </div>
                @endif

                {{-- Archivos adjuntos --}}
                @if ($policy->files && count($policy->files))
                    <div class="border-t pt-4">
                        <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-paperclip text-gray-500"></i> Archivos adjuntos
                        </h4>

                        <ul class="mt-2 space-y-2">
                            @foreach ($policy->files as $file)
                                <li class="flex items-center gap-2 text-sm text-blue-600 hover:underline">
                                    <i class="fas fa-file text-gray-400"></i>
                                    <a href="{{ Storage::url($file->path) }}" target="_blank">
                                        {{ $file->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-10 text-gray-500">
                <i class="fas fa-info-circle text-3xl mb-2"></i>
                <p>No hay información disponible para esta política.</p>
            </div>
        @endif
    </x-slot>

    <x-slot name="footer">
        <div class="flex justify-end">
            <x-secondary-button wire:click="closeModal" wire:loading.attr="disabled" class="!text-gray-700">
                <i class="fas fa-times mr-2"></i> Cerrar
            </x-secondary-button>
        </div>
    </x-slot>
</x-dialog-modal>