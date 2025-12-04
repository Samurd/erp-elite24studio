<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ $isUpdating ?? false ? 'Actualizar Vacante' : 'Nueva Vacante' }}
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ $isUpdating ?? false ? 'Modifica los datos de la vacante' : 'Completa los datos para crear una nueva vacante' }}
                </p>
            </div>
            <div class="flex space-x-3">
                <button wire:click="cancel"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </button>
                <button wire:click="save"
                        class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ $isUpdating ?? false ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Título -->
                <div class="md:col-span-2">
                    <x-label for="title" value="Título de la Vacante" />
                    <x-input 
                        wire:model="form.title" 
                        id="title" 
                        type="text" 
                        class="block w-full mt-1" 
                        placeholder="Ej: Desarrollador Senior" 
                        required />
                    <x-input-error for="form.title" class="mt-2" />
                </div>

                <!-- Área -->
                <div>
                    <x-label for="area" value="Área" />
                    <x-input 
                        wire:model="form.area" 
                        id="area" 
                        type="text" 
                        class="block w-full mt-1" 
                        placeholder="Ej: Tecnología, Marketing, etc." 
                        required />
                    <x-input-error for="form.area" class="mt-2" />
                </div>

                <!-- Tipo de Contrato -->
                <div>
                    <x-label for="contract_type_id" value="Tipo de Contrato" />
                    <select 
                        wire:model="form.contract_type_id"
                        id="contract_type_id"
                        class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                        required>
                        <option value="">Seleccionar tipo de contrato</option>
                        @foreach($contractTypes as $contractType)
                            <option value="{{ $contractType->id }}">{{ $contractType->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="form.contract_type_id" class="mt-2" />
                </div>

                <!-- Fecha de Publicación -->
                <div>
                    <x-label for="published_at" value="Fecha de Publicación" />
                    <x-input 
                        wire:model="form.published_at" 
                        id="published_at" 
                        type="date" 
                        class="block w-full mt-1" />
                    <x-input-error for="form.published_at" class="mt-2" />
                </div>

                <!-- Responsable -->
                <div>
                    <x-label for="user_id" value="Responsable" />
                    <select
                        wire:model="form.user_id"
                        id="user_id"
                        class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                        required>
                        <option value="">Seleccionar responsable</option>
                        @foreach($rrhhUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="form.user_id" class="mt-2" />
                </div>

                <!-- Estado -->
                <div>
                    <x-label for="status_id" value="Estado" />
                    <select 
                        wire:model="form.status_id"
                        id="status_id"
                        class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                        required>
                        <option value="">Seleccionar estado</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="form.status_id" class="mt-2" />
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <x-label for="description" value="Descripción" />
                    <textarea 
                        wire:model="form.description"
                        id="description"
                        rows="6"
                        class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                        placeholder="Describe los requisitos, responsabilidades y beneficios de la vacante..."></textarea>
                    <x-input-error for="form.description" class="mt-2" />
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex justify-end space-x-3 mt-8">
                <button type="button"
                        wire:click="cancel"
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </button>
                <button type="submit"
                        class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ $isUpdating ?? false ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </form>
    </div>
</div>
