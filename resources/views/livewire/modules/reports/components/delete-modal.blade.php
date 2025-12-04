<!-- MODAL DELETE ROW -->

<x-confirmation-modal id="delete-modal" wire:model="isDeleteModalOpen">
    <x-slot name="title">
        Eliminar Reporte
    </x-slot>

    <x-slot name="content">
        @if ($selectedReport)

            ¿Estás seguro de que deseas eliminar <strong>{{$selectedReport->title}}</strong>?
            <p>Esta acción no se puede deshacer.</p>


            {{-- @if ($selectedReport->files->isNotEmpty())

            <div class="flex flex-col gap-2 my-2">
                Otros relaciones tambien se eliminaran.
                <strong>Archivos</strong>

                @foreach ($selectedReport->files as $file)
                <div class="flex flex-col gap-1" wire:key="{{ $file->id }}">
                    <a href="{{ Storage::url($file->path) }}" target="_blank" class="text-blue-600 hover:underline">
                        📄 {{ $file->name }}
                    </a>
                </div>

                @endforeach

            </div>
            @endif --}}

        @else
            <p>Cargando...</p>
        @endif

    </x-slot>

    <x-slot name="footer">
        <x-secondary-button x-on:click="openDelete = false; $wire.closeModal()" wire:loading.attr="disabled">
            Cancelar
        </x-secondary-button>

        <x-danger-button class="ml-3" wire:click="deleteReport" wire:loading.attr="disabled">
            Eliminar
        </x-danger-button>
    </x-slot>
</x-confirmation-modal>