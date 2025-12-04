<div class="flex flex-col flex-1">

    <main class="flex-1 p-10 bg-gray-100 overflow-y-auto">
        <div class="bg-white shadow-md rounded-lg p-4 mb-10">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Filtros de busqueda</h2>
            <input type="text" wire:model.live="search" placeholder="Buscar por nombre o correo"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500">

            <!-- Filtros adicionales -->
            <!-- 🔍 Barra de filtros -->
            <div class="flex flex-wrap items-center gap-3 my-4">

                <input type="text" wire:model.live="empresa" placeholder="Empresa"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-yellow-500 focus:border-yellow-500">

                <select wire:model.live="estado"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-yellow-500 focus:border-yellow-500">
                    <option value="">Estado</option>
                    @if ($states && count($states))
                        @foreach ($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    @endif
                </select>

                <select wire:model.live="responsable"
                    class="border border-gray-300 rounded-lg text-sm py-2 focus:ring-yellow-500 focus:border-yellow-500">
                    <option value="">Responsable</option>
                    @if ($users && count($users))
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    @endif
                </select>

                <button wire:click="resetFilters"
                    class="px-4 py-2 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition">
                    Limpiar filtros
                </button>
            </div>
        </div>
        <div class="bg-white shadow-md rounded-lg p-4">
            <!-- Título y Botón -->
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">Base de Datos</h2>

                <div class="flex gap-2">
                    @canArea('create', 'contactos')
                    <a href="{{ route('contacts.create', ) }}"
                        class="bg-black hover:bg-gray-950 text-white px-4 py-2 rounded-lg text-sm shadow flex items-center">
                        <i class="fas fa-plus mr-2"></i> Nuevo </a>
                    @endCanArea
                </div>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table class="min-w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-white uppercase bg-gradient-to-r from-black via-yellow-700 to-amber-500">
                        <tr>
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Empresa</th>
                            <th class="px-6 py-3">Estado</th>
                            <th class="px-6 py-3">Responsable</th>
                            <th class="px-6 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($contacts as $contact)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-3">{{ $contact->name }}</td>
                                <td class="px-6 py-3">{{ $contact->email }}</td>
                                <td class="px-6 py-3">{{ $contact->company ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    {{ optional($contact->status)->name ?? '—' }}
                                </td>
                                <td class="px-6 py-3">
                                    {{ $contact->responsible->name ?? '—' }}
                                </td>
                                <td class="px-6 py-3 flex flex-wrap gap-2">
                                    <button wire:click="openContactModal({{ $contact->id }})"
                                        class="hover:underline uppercase">Ver</button>
                                    @canArea('update', 'contactos')
                                    <a href="{{ route('contacts.edit', ['contact' => $contact->id]) }}"
                                        class="flex items-center gap-2 px-2 uppercase">
                                        Editar
                                    </a>
                                    @endCanArea

                                    @canArea('delete', 'contactos')
                                    <x-button
                                        class="bg-transparent hover:bg-transparent focus:bg-transparent active:bg-transparent focus:ring-0 focus:ring-transparent focus:outline-none hover:ring-0 focus:ring-offset-0 !text-red-500"
                                        wire:click="openDeleteModal({{ $contact->id }})">
                                        {{-- <x-fas-delete-left class="w-4 h-4 mr-2" />  --}}

                                            Eliminar </x-button>
                                    @endCanArea
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-400">No hay resultados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $contacts->links() }}
            </div>
        </div>
    </main>


    <x-confirmation-modal wire:model="isDeleteModalOpen">
        <x-slot name="title">
            Eliminar Contacto
        </x-slot>

        <x-slot name="content">
            @if ($selectedContact)

                ¿Estás seguro de que deseas eliminar {{$selectedContact->name}}?
                <p>Esta acción no se puede deshacer.</p>


                Otros relaciones que quedaran afectadas o con el valor null {{`( vacio )`}}

                <div class="flex flex-col gap-2 my-2">
                    <strong>Registro de casos</strong>

                    @foreach ($selectedContact->cases as $case)
                        <div class="flex flex-col gap-2 bg-gray-200 p-2 rounded-lg">
                            <span>Codigo: {{ $case->id }}</span>
                            <span>Estado: {{ $case->status->name }}</span>
                        </div>

                    @endforeach

                </div>
            @endif

        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('isDeleteModalOpen', false)" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="deleteContact" wire:loading.attr="disabled">
                Eliminar
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>



    <x-dialog-modal wire:model="isInfoModalOpen" position="center">
        <x-slot name="title">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-100 rounded-full">

                </div>
                <h2 class="text-xl font-semibold text-gray-800">Información del Contacto</h2>
            </div>
        </x-slot>

        <x-slot name="content">
            @if($selectedContact)
                <div class="space-y-4">
                    <!-- Datos principales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nombre</p>
                            <p class="font-medium text-gray-900">{{ $selectedContact->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium text-gray-900">{{ $selectedContact->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Empresa</p>
                            <p class="font-medium text-gray-900">{{ $selectedContact->company }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Teléfono</p>
                            <p class="font-medium text-gray-900">{{ $selectedContact->phone }}</p>
                        </div>
                    </div>

                    <hr class="my-2">

                    <!-- Info detallada -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Tipo de contacto</p>
                            <p class="font-medium text-gray-900">{{ $selectedContact->contactType?->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tipo de relación</p>
                            <p class="font-medium text-gray-900">{{ $selectedContact->relationType?->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Estado</p>
                            <p class="font-medium text-gray-900">{{ $selectedContact->status?->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Fuente</p>
                            <p class="font-medium text-gray-900">{{ $selectedContact->source?->name }}</p>
                        </div>
                    </div>

                    <hr class="my-2">

                    <!-- Más info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Fecha del primer contacto</p>
                            <p class="font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($selectedContact->first_contact_date)->format('d/m/Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Responsable</p>
                            <p class="font-medium text-gray-900">{{ $selectedContact->responsible?->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Etiqueta</p>
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-500 ">
                                {{ $selectedContact->label?->name }}
                            </span>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    @if($selectedContact->notes)
                        <div>
                            <p class="text-sm text-gray-500">Observaciones</p>
                            <p class="mt-1 p-3 rounded-md bg-gray-50 text-gray-700 text-sm">
                                {{ $selectedContact->notes }}
                            </p>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-gray-500">Selecciona un contacto para ver su información</p>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-button wire:click="closeContactModal" class="bg-gray-600 hover:bg-gray-700 text-white">
                Cerrar
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
