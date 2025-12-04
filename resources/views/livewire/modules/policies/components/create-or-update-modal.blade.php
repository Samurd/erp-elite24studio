<x-dialog-modal wire:model="showModal" position="center">
    <x-slot name="title"> <!-- Header -->

        <div class="flex border-b p-2">
            <h3 class="text-xl font-bold">{{ isset($policy) ? 'Actualizar Politica' : 'Añadir nueva Politica' }}
            </h3>
        </div>

    </x-slot>
    <x-slot name="content">

        <div id="policyForm" class="flex flex-col gap-3">

            <!-- Nombre política -->
            <div class="mb-3">
                <label for="name" class="block font-semibold">Nombre de la Política</label>
                <x-input id="name" type="text" name="nombre_politica" class="w-full" placeholder="Nombre de la política"
                    wire:model="form.name" />

                @error('form.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Estado -->
            <div>
                <label for="status_id" class="block font-medium">Estado</label>
                <select id="status_id" wire:model="form.status_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600 mb-2">
                    <option value="">Seleccionar</option>
                    @foreach($this->states as $state)
                        <option wire:key="{{ $state->id }}" value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                </select>

                @error('form.status_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <!-- Tipico -->
            <div class="mb-3">
                <label for="type" class="block font-semibold">Tipo</label>
                <select id="type" wire:model="form.type_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600 mb-2">
                    <option value="">Seleccionar</option>
                    @foreach($policy_types as $type)
                        <option wire:key="{{ $type->id }}" value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>

                @error('form.type_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Fecha emisión -->
            <div class="mb-3">
                <label for="issue_date" class="block font-semibold">Fecha de Emisión</label>
                <x-input id="issue_date" type="date" name="fecha_emision" class="w-full" wire:model="form.issued_at" />

                @error('form.issued_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Última revisión -->
            <div class="mb-3">
                <label for="latest_review" class="block font-semibold">Última Revisión</label>
                <x-input id="latest_review" type="date" name="ultima_revision" class="w-full"
                    wire:model="form.reviewed_at" />

                @error('form.reviewed_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Responsable -->
            <div>
                <label for="responsible" class="block font-medium">Reponsable</label>
                <select id="responsible" wire:model="form.assigned_to_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600 mb-2">
                    <option value="">Seleccionar</option>
                    @foreach($this->users as $user)
                        <option wire:key="{{ $user->id }}" value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>

                @error('form.assigned_to_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>



            <div x-data x-init=" window.addEventListener('beforeunload', () => { $wire.cleanupTempFiles() })">
                <label wire:loading.remove wire:target="form.files" for="files" class="block font-medium mb-1">Datos
                    adjuntos</label>
                <div wire:loading.remove wire:target="form.files" class="flex items-center justify-between">
                    <span class="text-gray-500 text-sm">Puedes agregar datos adicionales</span>
                    <button type="button" onclick="document.getElementById('file-input').click()"
                        class="text-yellow-700 hover:underline flex items-center gap-1 text-sm"> 📎 Agregar
                        datos
                        adjuntos
                    </button>
                    <input id="file-input" type="file" wire:model="form.files" class="hidden" />
                </div> <!-- Estado de carga -->
                <div wire:loading wire:target="form.files"
                    class="flex flex-col items-center justify-center text-center"> <svg
                        class="animate-spin h-6 w-6 text-blue-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg> <span class=" text-gray-700">Cargando archivo...</span>
                </div>
                <!-- Aquí se mostrarán los nombres -->
                @if ($form->files)
                    <div class="mt-2 text-sm text-gray-700 space-y-1">
                        @foreach ($form->files as $index => $file)
                            <div class="flex flex-row flex-wrap gap-2" wire:key="{{ $file->id }}">
                                <div class="flex gap-2 items-center bg-gray-100 rounded-full px-2">
                                    <span>{{ $file->getClientOriginalName() }}</span>
                                    <button type="button" wire:click="removeTempFile({{ $index }})"
                                        class="text-red-500 text-lg px-2"> x
                                    </button>
                                </div>
                        </div> @endforeach
                    </div>
                @endif
                @error('form.files')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror


                @if ($form->files_db && $form->files_db->isNotEmpty())
                    <div class="mt-2 text-sm text-gray-700 space-y-1 bg-gray-200 p-2 rounded-lg">
                        <span class="text-lg font-semibold">Files subidos</span>

                        @foreach ($form->files_db as $file)
                            <div class="flex flex-row flex-wrap gap-2" wire:key="{{ $file->id }}">
                                <div class="flex gap-2 items-center bg-white rounded-full px-2">
                                    <a class="underline" href="{{ Storage::url($file->path) }}"
                                        target="_blank">{{ $file->name }}</a>

                                    <button type="button" wire:click="removeStoredFile({{ $file->id }})"
                                        class="text-red-500 text-lg px-2">
                                        x
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>


            <div>
                <label for="observations" class="block text-sm font-medium text-gray-700">Observaciones</label>
                <textarea wire:model="form.description" id="observations"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 mt-1"
                    rows="3" placeholder="Agregue sus observaciones"></textarea>

                @error('form.description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>


    </x-slot>

    <x-slot name="footer">
        <x-button wire:click="closeModal" class="bg-gray-600 hover:bg-gray-700 text-white"> Cerrar </x-button>
        <x-button wire:click="save" class="bg-yellow-700 hover:bg-yellow-800 text-white ml-2">
            {{ $policy ? 'Actualizar' : 'Crear' }} </x-button>

    </x-slot>

</x-dialog-modal>