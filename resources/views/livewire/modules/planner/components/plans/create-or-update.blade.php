<!-- MODAL CREATE -->

<x-dialog-modal wire:model="showModal" position="center" wire:key="create-plan-modal">
    <x-slot name="title"> <!-- Header -->
        <div class="flex items-center gap-3 p-6 border-b">
            <div>
                <h2 class="text-lg font-semibold">Plan/Proyecto</h2>
                <p class="text-sm text-gray-500">{{ $isEdit ? 'Editar plan' : 'Crear plan' }}</p>
            </div>
        </div>
    </x-slot>
    <x-slot name="content">
        <div class="overflow-y-auto p-6 space-y-2 text-sm">
            <!-- Tipo de solicitud --> {{-- <div> <label class="block font-medium mb-1">Tipo de solicitud <span
                        class="text-red-500">*</span></label>
                <select
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-700 focus:border-yellow-700">
                    <option>Básico</option>
                    <option>Avanzado</option>
                </select>
            </div> --}} <!-- Nombre de la solicitud -->
            <div>
                <label for="name" class="block font-medium mb-1">Nombre</label>
                <input id="name" type="text" wire:model="form.name"
                    placeholder="Usa un nombre que sea fácil de entender"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-700 focus:border-yellow-700" />

                @error('form.name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>


            <div>
                <label for="description" class="block font-medium mb-1">Descripción</label>
                <textarea id="description" rows="3" wire:model="form.description" placeholder="Descripción del plan"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-700 focus:border-yellow-700"></textarea>

                @error('form.description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>


            @if(!$form->project_id)
                <div>
                    <label for="team_id" class="block font-medium mb-1">Equipo/Grupo</label>
                    <p class="text-sm text-gray-500">Sin equipo asignado, sera un plan/proyecto personal</p>
                    <select id="team_id" wire:model="form.team_id" {{ $isEdit ? 'disabled' : '' }}
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 {{ $isEdit ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                        <option value="">Selecciona una team</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    @if ($isEdit)
                        <p class="text-xs text-gray-400 mt-1">El equipo no se puede cambiar después de crear el plan</p>
                    @endif
                    @error('form.team_id') <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            @else
                <div>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-2"></i>
                        Este plan está asociado a un proyecto específico.
                    </p>
                </div>
            @endif




        </div>
    </x-slot>
    <x-slot name="footer">
        <x-button wire:click="closeModal" class="bg-gray-600 hover:bg-gray-700 text-white"> Cerrar </x-button>
        <x-button x-on:click="$wire.save()" class="bg-yellow-700 hover:bg-yellow-800 text-white ml-2">
            {{ $isEdit ? 'Actualizar' : 'Crear' }} </x-button>
    </x-slot>

</x-dialog-modal>