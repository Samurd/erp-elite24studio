<div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <!-- Header -->
        <div class="flex items-center gap-3 p-6 border-b bg-gray-50">
            <div class="bg-yellow-700 text-white rounded p-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-3-3v6m-9 3h18M4 6h16" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ isset($approval) ? 'Editar Solicitud de Aprobación' : 'Nueva Solicitud de Aprobación' }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ isset($approval) ? 'Modifica los detalles de la solicitud existente' : 'Crea una nueva solicitud de aprobación para tu equipo' }}
                </p>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Nombre de la solicitud -->
            <div>
                <label for="name" class="block font-medium text-gray-700 mb-1">Nombre de la solicitud <span
                        class="text-red-500">*</span></label>
                <input id="name" type="text" wire:model="form.name"
                    placeholder="Usa un nombre que sea fácil de entender"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-700 focus:border-yellow-700" />
                @error('form.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Is it a purchase request? -->
            <div>
                <div class="flex gap-2 items-center">
                    <input id="buy" type="checkbox" wire:model="form.buy"
                        class="rounded text-yellow-700 focus:ring-yellow-700" />
                    <label for="buy" class="block font-medium text-gray-700">¿Es una solicitud de compra?</label>
                </div>
                @error('form.buy') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Aprobadores -->
            <div>
                <label class="block font-medium text-gray-700 mb-1">Aprobadores</label>
                <select wire:model="form.approvers" multiple
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-700 focus:border-yellow-700 mb-2 h-32">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Mantén presionado Ctrl/Cmd para seleccionar múltiples</p>
                @error('form.approvers') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                <div class="flex items-center gap-2 mt-2">
                    <input id="all-approvers" type="checkbox" wire:model="form.all_approvers"
                        class="rounded text-yellow-700 focus:ring-yellow-700" />
                    <label for="all-approvers" class="text-gray-700 text-sm">Solicitar una respuesta de todos los
                        destinatarios</label>
                </div>
            </div>

            <!-- Prioridad -->
            <div>
                <label for="priority_id" class="block font-medium text-gray-700 mb-1">Prioridad</label>
                <select id="priority_id" wire:model="form.priority_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-700 focus:border-yellow-700">
                    <option value="">Selecciona una prioridad</option>
                    @foreach ($priorities as $priority)
                        <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                    @endforeach
                </select>
                @error('form.priority_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Detalles adicionales -->
            <div>
                <label for="description" class="block font-medium text-gray-700 mb-1">Detalles adicionales</label>
                <textarea id="description" rows="4" wire:model="form.description"
                    placeholder="Si es necesario, agrega información adicional que ayude a los destinatarios a obtener más información acerca de la solicitud"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-700 focus:border-yellow-700"></textarea>
                @error('form.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            @if(isset($approval))
                @livewire('modules.cloud.components.model-attachments', [
                    'model' => $approval,
                    'area' => 'aprobaciones'
                ])
            @else
                @livewire('modules.cloud.components.model-attachments-creator', [
                    'modelClass' => \App\Models\Approval::class,
                    'areaSlug' => 'aprobaciones'
                ])

               @endif
        </div>

               

                       <!-- Footer -->

               

                                              <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
            <a href="{{ route('approvals.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                Cancelar
            </a>
            <button wire:click="save" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 bg-yellow-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-800 focus:bg-yellow-800 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <span wire:loading.remove wire:target="save">{{ isset($approval) ? 'Actualizar' : 'Crear' }} Solicitud</span>
                <span wire:loading wire:target="save">Guardando...</span>
            </button>
        </div>
    </div>
</div>