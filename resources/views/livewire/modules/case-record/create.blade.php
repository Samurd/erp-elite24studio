<div class="flex flex-col flex-1">
    <main class="flex-1 p-10 bg-gray-100 overflow-y-auto">
        <div class="bg-white w-full p-4">
            <div class="mb-3">
                <!-- Línea con título + iconos -->
                <div class="flex flex-col px-2">
                    <h3 class="text-2xl font-bold leading-none">Registro de casos</h3>
                    <!-- Subtítulo -->
                    <p class="">Area Comercial</p>
                    <p class="text-sm text-gray-500 mt-2">RELLENAR
                        TODOS LOS DATOS, ADJUNTO Y DEMAS.</p>
                </div>
            </div>

            <form wire:submit.prevent="save">

                <div class="text-[12px] font-sans w-full ">
                    <!-- Bloque de formulario -->
                    <div class="grid grid-cols-4 gap-y-3 gap-x-4 w-full text-sm text-black">
                        <!-- Fecha -->
                        <label for="date" class="font-semibold flex items-center">Fecha:</label>
                        <div class="col-span-3">
                            <x-input id="date" type="date" value="{{ date('Y-m-d') }}"
                                class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-yellow-400 focus:outline-none text-[13px]"
                                wire:model="form.date" />
                            @error('form.date')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Canal -->
                        <label for="channel" class="font-semibold flex items-center">Canal:</label>
                        <div>
                            <x-input id="channel" type="text" placeholder="WhatsApp"
                                class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-yellow-400 focus:outline-none text-[13px]"
                                wire:model="form.channel" />
                            @error('form.channel')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tipo de caso -->
                        <label for="case_type_id" class="font-semibold flex items-center">Tipo de Caso:</label>
                        <div>
                            <select id="case_type_id"
                                class="w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2  focus:ring-yellow-500"
                                wire:model="form.case_type_id">
                                <option value="">Seleccionar</option>
                                @foreach ($case_types as $case_type)
                                    <option value="{{ $case_type->id }}">{{ $case_type->name }}</option>
                                @endforeach
                            </select>
                            @error('form.case_type_id')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <label for="status_id" class="font-semibold flex items-center">Estado:</label>
                        <div>
                            <select id="status_id"
                                class=" w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2  focus:ring-yellow-500"
                                wire:model="form.status_id">
                                <option value="">Seleccionar</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                            @error('form.status_id')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Asesor -->
                        <label for="assigned_to_id" class="font-semibold flex items-center">Asesor:</label>
                        <div>
                            <select id="assigned_to_id"
                                class="w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2  focus:ring-yellow-500"
                                wire:model="form.assigned_to_id">
                                <option value="">Seleccione...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ $user->id == $defaultUserId ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('form.assigned_to_id')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Contacto -->
                        <label for="contact_id" class="font-semibold flex items-center">Nombre Contacto:</label>
                        <div class="col-span-3">
                            <select id="contact_id"
                                class="w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2  focus:ring-yellow-500"
                                wire:model="form.contact_id">
                                <option value="">Seleccione un contacto</option>
                                @foreach($contacts as $contact)
                                    <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                                @endforeach
                            </select>
                            @error('form.contact_id')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <!-- Descripción debajo, mismo ancho, alineado a la izquierda -->
                    <div class="w-full mt-4">
                        <h3 class="font-bold text-2xl">Descripción de Registro</h3>
                        <textarea placeholder="observaciones" id="" rows="8"
                            class="mt-2 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2  focus:ring-yellow-500"
                            wire:model="form.description"></textarea>

                        @if(isset($form->case_record))
                            @livewire('modules.cloud.components.model-attachments', [
                                'model' => $form->case_record,
                                'area' => 'registro-casos'
                            ])
                        @else
                            @livewire('modules.cloud.components.model-attachments-creator', [
                                'modelClass' => \App\Models\CaseRecord::class,
                                'areaSlug' => 'registro-casos'
                            ])
                        @endif

                        {{-- Switch inferior derecha --}}
                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('case-record.index') }}" class="text-blue-400 text-[18px]">Volver</a>

                            <label for="btn-save" class="flex items-center cursor-pointer">
                                <x-button type="submit">{{ $isEdit ? 'Actualizar' : 'Registrar' }}</x-button>
                            </label>
                        </div>
                    </div>
            </form>
        </div>
    </main>

    {{-- Componente de notificaciones --}}
    @livewire('components.notification')

    {{-- Modal selector de carpetas --}}

</div>