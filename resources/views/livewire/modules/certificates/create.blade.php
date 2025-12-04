<div class="flex flex-col flex-1">
    <main class="flex-1 p-10 bg-gray-100 overflow-y-auto">
        <div class="bg-white w-full p-4 rounded-lg">
            <div class="flex border-b p-2 my-2">
                <h3 class="text-xl font-bold">
                    {{ $isEdit ? 'Actualizar Certificado' : 'Añadir nuevo Certificado' }}
                </h3>
            </div>

            <form wire:submit.prevent="save" class="my-4">


                <!-- Nombre certificado -->
                <div class="mb-3">
                    <label for="name" class="block font-semibold">Nombre del Certificado</label>
                    <x-input id="name" type="text" name="nombre_certificado" class="w-full"
                        placeholder="Nombre del certificado" wire:model="form.name" />

                    @error('form.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="status_id" class="block font-medium">Estado</label>
                    <select id="status_id" wire:model="form.status_id"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600 mb-2">
                        <option value="">Seleccionar</option>
                        @foreach($this->states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
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
                        @foreach($certificate_types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>

                    @error('form.type_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Fecha emisión -->
                <div class="mb-3">
                    <label for="issue_date" class="block font-semibold">Fecha de Emisión</label>
                    <x-input id="issue_date" type="date" name="fecha_emision" class="w-full"
                        wire:model="form.issued_at" />

                    @error('form.issued_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Fecha vencimiento -->
                <div class="mb-3">
                    <label for="expiration_date" class="block font-semibold">Fecha de Vencimiento</label>
                    <x-input id="expiration_date" type="date" name="fecha_vencimiento" class="w-full"
                        wire:model="form.expires_at" />

                    @error('form.expires_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Responsable -->
                <div>
                    <label for="responsible" class="block font-medium">Reponsable</label>
                    <select id="responsible" wire:model="form.assigned_to_id"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600 mb-2">
                        <option value="">Seleccionar</option>
                        @foreach($this->users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>

                    @error('form.assigned_to_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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

                @if(isset($form->certificate))
                    @livewire('modules.cloud.components.model-attachments', [
                        'model' => $form->certificate,
                        'area' => 'certificados'
                    ])
                @else
                    @livewire('modules.cloud.components.model-attachments-creator', [
                        'modelClass' => \App\Models\Certificate::class,
                        'areaSlug' => 'certificados'
                    ])
                @endif

                <div class="my-2">
                    <x-button class="bg-yellow-700 hover:bg-yellow-800 text-white ml-2">
                        {{ $isEdit ? 'Actualizar' : 'Crear' }} </x-button>
                </div>
            </form>

        </div>
    </main>
    @livewire('components.notification')

</div>