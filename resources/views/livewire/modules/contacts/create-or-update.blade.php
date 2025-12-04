<div class="flex flex-col flex-1">
    @session('message')
    <div class="bg-green-50 border border-green-500 text-green-500 px-4 py-3 rounded relative"
        role="alert">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline">{{ session('message') }}</span>
    </div>
    @endsession

    @session('error')
    <div class="bg-red-50 border border-red-500 text-red-500 px-4 py-3 rounded relative"
        role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endsession
    <main class="flex-1 p-10 bg-gray-100 overflow-y-auto">
        <div class="bg-white shadow-md rounded-lg p-4">
            <h3 class="text-black text-2xl font-bold mb-4">Registrar contacto</h3>
            <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">


                <div>
                    <label for="nombre_contacto" class="block mb-1 font-semibold">Nombre Contacto</label>
                    <x-input type="text" id="nombre_contacto" wire:model="name" placeholder="Nombre Contacto"
                        class="border rounded px-3 py-2 w-full" />
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="empresa" class="block mb-1 font-semibold">Empresa</label>
                    <x-input type="text" id="empresa" wire:model="company" placeholder="Empresa"
                        class="border rounded px-3 py-2 w-full" />
                    @error('company') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="tipo_contacto" class="block mb-1 font-semibold">Tipo de Contacto</label>
                    <select id="tipo_contacto"
                        class="mt-1 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2  focus:ring-yellow-500"
                        wire:model="contact_type_id">
                        <option value="">Seleccione...</option>
                        @foreach ($contact_types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('contact_type_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>


                <div>
                    <label for="tipo_relacion" class="block mb-1 font-semibold">Tipo de Relación</label>
                    <select id="tipo_relacion" wire:model="relation_type_id"
                        class="mt-1 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2  focus:ring-yellow-500">
                        <option value="">Seleccione...</option>
                        @foreach ($relation_types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('relation_type_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="estado" class="block mb-1 font-semibold">Estado</label>
                    <select id="estado" wire:model="status_id"
                        class="mt-1 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2  focus:ring-yellow-500">
                        <option value="">Seleccione...</option>
                        @foreach ($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                    @error('status_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block mb-1 font-semibold">Correo electrónico</label>
                    <x-input type="email" id="email" wire:model="email" placeholder="Correo electrónico"
                        class="border rounded px-3 py-2 w-full" />
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="direccion" class="block mb-1 font-semibold">Dirección</label>
                    <x-input type="text" id="direccion" wire:model="address" placeholder="Dirección"
                        class="border rounded px-3 py-2 w-full" />
                    @error('address') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="telefono" class="block mb-1 font-semibold">Teléfono</label>
                    <x-input type="text" id="telefono" wire:model="phone" placeholder="Teléfono"
                        class="border rounded px-3 py-2 w-full" />
                    @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="ciudad" class="block mb-1 font-semibold">Ciudad</label>
                    <x-input type="text" id="ciudad" wire:model="city" placeholder="Ciudad"
                        class="border rounded px-3 py-2 w-full" />
                    @error('city') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="proyecto_asociado" class="block mb-1 font-semibold">Proyecto Asociado</label>
                    <x-input type="text" id="proyecto_asociado" placeholder="Proyecto Asociado"
                        class="border rounded px-3 py-2 w-full" />
                </div>

                <div>
                    <label for="fuente" class="block mb-1 font-semibold">Fuente</label>
                    <select id="fuente" wire:model="source_id"
                        class="mt-1 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2  focus:ring-yellow-500">
                        <option value="">Seleccione...</option>
                        @foreach ($sources as $source)
                            <option value="{{ $source->id }}">{{ $source->name }}</option>
                        @endforeach
                    </select>
                    @error('source_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>


                <div>
                    <label for="fecha_primer_contacto" class="block mb-1 font-semibold">Fecha Primer Contacto</label>
                    <x-input type="date" id="fecha_primer_contacto"
                        wire:model="first_contact_date" class="border rounded px-3 py-2 w-full" />
                    @error('first_contact_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="responsible" class="block mb-1 font-semibold">Responsable</label>
                    <select id="responsible" wire:model="responsible_id"
                        class="mt-1 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2  focus:ring-yellow-500">
                        <option value="">Seleccione...</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('responsible_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="etiqueta" class="block mb-1 font-semibold">Etiqueta</label>
                    <select id="etiqueta"  wire:model="label_id"
                        class="mt-1 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2  focus:ring-yellow-500">
                        <option value="">Seleccione...</option>
                        @foreach ($labels as $label)
                            <option value="{{ $label->id }}">{{ $label->name }}</option>
                        @endforeach
                    </select>
                    @error('label_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>


                <div class="col-span-2">
                    <label for="observaciones" class="block mb-1 font-semibold">Observaciones</label>
                    <textarea id="observaciones" wire:model="notes" placeholder="Observaciones"
                        class="border rounded px-3 py-2 w-full" rows="5"></textarea>
                </div>

                <div class="col-span-2 flex justify-end space-x-2 mt-2">
                    <x-button type="submit">{{ $isEdit ? 'Actualizar' : 'Guardar' }} contacto</x-button>
                </div>
            </form>
        </div>
    </main>
</div>