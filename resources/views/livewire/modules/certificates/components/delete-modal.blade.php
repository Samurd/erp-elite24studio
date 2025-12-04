    <x-confirmation-modal wire:model="isDeleteModalOpen">
        <x-slot name="title">
            Eliminar Certificado
        </x-slot>

        <x-slot name="content">
            @if ($selectedCert)

                ¿Estás seguro de que deseas eliminar <strong>{{$selectedCert->name}}</strong>?
                <p>Esta acción no se puede deshacer.</p>


                @if ($selectedCert->files->isNotEmpty())

                    <div class="flex flex-col gap-2 my-2">
                        Otros relaciones tambien se eliminaran.
                        <strong>Archivos</strong>

                        @foreach ($selectedCert->files as $file)
                            <div class="flex flex-col gap-1">
                                <a href="{{ Storage::url($file->path) }}" target="_blank" class="text-blue-600 hover:underline">
                                    📄 {{ $file->name }}
                                </a>
                            </div>

                        @endforeach

                    </div>
                @endif
            @endif

        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="deleteCert" wire:loading.attr="disabled">
                Eliminar
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>