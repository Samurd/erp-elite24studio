<div class="flex flex-col flex-1">
    <main class="flex-1 p-10 bg-gray-100 overflow-y-auto">
        <div class="flex flex-col">
            <div class="bg-white shadow-md rounded-lg p-4 mb-10">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Filtros de busqueda</h2>
                <x-input type="text" wire:model.live="search" placeholder="Buscar por asesor, contacto..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500" />

                <!-- Filtros adicionales -->
                <!-- 🔍 Barra de filtros -->
                <div class="flex flex-wrap items-center gap-3 my-4">

                    <input type="text" wire:model.live="canal" placeholder="Canal"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-yellow-500 focus:border-yellow-500">



                    <select wire:model.live="tipo_caso"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">Tipo de caso</option>
                        @if ($case_types && count($case_types))
                            @foreach ($case_types as $case_type)
                                <option value="{{ $case_type->id }}">{{ $case_type->name }}</option>
                            @endforeach
                        @endif
                    </select>

                    <select wire:model.live="estado"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">Estado</option>
                        @if ($states && count($states))
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        @endif
                    </select>


                    <select wire:model.live="asesor"
                        class="border border-gray-300 rounded-lg text-sm py-2 focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">Asesor</option>
                        @if ($users && count($users))
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        @endif
                    </select>

                    <!-- Fecha -->

                    <x-input type="date" id="fecha" wire:model.live="fecha" class="text-sm" />

                    <button wire:click="resetFilters"
                        class="px-4 py-2 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition">
                        Limpiar filtros
                    </button>
                </div>
                {{--
                <div class="flex justify-end mt-4 space-x-2">
                    <x-button type="submit" class="">
                        <i class="fa-solid fa-arrow-down-short-wide mr-2"></i> Filtrar
                    </x-button>

                    <a href=""
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-md text-sm w-32 text-center inline-flex items-center justify-center">
                        <i class="fa-solid fa-trash mr-2"></i> Limpiar
                    </a>
                </div> --}}
            </div>
        </div>
        <div class="bg-white w-full p-4 rounded-xl shadow">
            <div class="mb-3">
                <!-- Línea con título + iconos -->
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold leading-none">Área comercial</h3>
                    @canArea('create', 'registro-casos')
                    <div class="flex space-x-3">
                        <div
                            class="rounded-full p-2 flex justify-end mt-6 bg-gradient-to-r from-black via-amber-900 to-amber-500">
                            <label for="switch-subir" class="flex items-center cursor-pointer">
                                <span class="ml-3 text-sm font-medium text-white mr-2">Nuevo registro</span>
                                <div class="relative">
                                    <input type="checkbox" id="switch-subir" class="sr-only peer"
                                        onchange="window.location.href = '{{ route('case-record.create') }}'" />
                                    <div class="w-10 h-5 bg-gray-300 rounded-full peer-checked:bg-black transition">
                                    </div>
                                    <div
                                        class="absolute w-4 h-4 bg-white rounded-full top-0.5 left-0.5 peer-checked:translate-x-5 transition-transform">
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    @endCanArea
                </div>
                <!-- Subtítulo -->
                <h3 class="text-1xl font-bold leading-none">Elite 24 STUDIO S.A.S</h3>
            </div>

            <div class="mt-8">
                <div class="relative overflow-x-auto">
                    <!-- Contenedor scrollable horizontal -->
                    <div class="overflow-x-auto w-full rounded-xl bg-white shadow">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 whitespace-nowrap">
                            <thead
                                class="text-xs text-white uppercase bg-gradient-to-r from-black via-amber-900 to-amber-500">
                                <tr>
                                    <th class="px-6 py-5">Cod</th>
                                    <th class="px-6 py-5">Fecha</th>
                                    <th class="px-6 py-5">Nombre Contacto</th>
                                    <th class="px-6 py-5">Nombre Asesor</th>
                                    <th class="px-6 py-5">Tipo de caso</th>
                                    <th class="px-6 py-5">Estado</th>
                                    <th class="px-6 py-5">Canal</th>
                                    <th class="px-6 py-5">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $record)
                                    <tr class="">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $record->id ?? '—' }}</td>
                                        <td class="px-6 py-4">{{ $record->date }}</td>
                                        <td class="px-6 py-4">
                                            @if ($record->contact)
                                                <a href="{{ route('contacts.index', ['search' => $record->contact->name]) }}"
                                                    title="Buscar a {{ $record->contact->name }}" target="_blank"
                                                    class="text-blue-600 hover:text-blue-800 cursor-pointer">
                                                    <div class="flex items-center gap-2">

                                                        <div class="flex flex-col leading-tight">
                                                            <span>{{$record->contact->name}}</span>
                                                        </div>
                                                        <x-ri-external-link-line class="w-4 " />

                                                    </div>
                                                </a>
                                            @else
                                                <p>No contacto relacionado</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($record->assignedTo)

                                                <span>{{$record->assignedTo->name}}</span>
                                            @else
                                                <p>No asesor relacionado</p>

                                            @endif
                                        </td>
                                        <td class="px-6 py-4">{{ ucfirst(str_replace('-', ' ', $record->type->name)) }}</td>
                                        <td class="px-6 py-4 flex items-center">
                                            @php
                                                $colores = [
                                                    'Abierto' => 'green',
                                                    'En proceso' => 'yellow',
                                                    'Escalado' => 'red',
                                                    'Cerrado' => 'gray'
                                                ];
                                                $color = $colores[$record->status->name] ?? 'black';
                                            @endphp
                                            <span
                                                style="display:inline-block; width:10px; height:10px; background-color:{{ $color }}; border-radius:50%; margin-right:5px;"></span>
                                            {{ $record->status->name }}
                                        </td>
                                        <td class="px-6 py-4">{{ $record->channel }}</td>
                                        <td class="px-6 py-4 flex space-x-3 text-lg text-gray-600">

                                            <div class="flex flex-wrap gap-4  items-center text-sm">
                                                @canArea('update', 'registro-casos')
                                                <a href="{{ route("case-record.edit", ['caseRecord' => $record->id]) }}"
                                                    class="flex items-center gap-2 px-2 uppercase">
                                                    Editar
                                                </a>
                                                @endCanArea

                                                @canArea('delete', 'registro-casos')
                                                <x-button type="button" title="Eliminar"
                                                    class="bg-transparent hover:bg-transparent focus:bg-transparent active:bg-transparent focus:ring-0 focus:ring-transparent focus:outline-none hover:ring-0 focus:ring-offset-0 !text-red-500"
                                                    wire:click="openDeleteModal({{ $record->id }})">
                                                    {{-- <x-fas-delete-left class="w-4 h-4 mr-2" /> --}}
                                                    Eliminar
                                                </x-button>
                                                @endCanArea
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No hay registros
                                            disponibles.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>




    <x-confirmation-modal wire:model="isDeleteModalOpen">
        <x-slot name="title">
            Eliminar Contacto
        </x-slot>

        <x-slot name="content">
            @if ($selectedRecord)

                ¿Estás seguro de que deseas eliminar el registro con el codigo:<strong> {{$selectedRecord->id}}?</strong>

            @endif

            <p> Esta acción no se puede deshacer.</p>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('isDeleteModalOpen', false)" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="deleteRecord" wire:loading.attr="disabled">
                Eliminar
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

</div>