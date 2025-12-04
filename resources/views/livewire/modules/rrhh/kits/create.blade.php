<div>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                {{ isset($kit) ? 'Editar Kit' : 'Nuevo Kit' }}
            </h1>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Solicitado por -->
                <div>
                    <label for="requested_by_user_id" class="block text-sm font-medium text-gray-700 mb-2">Solicitado
                        por</label>
                    <select id="requested_by_user_id" wire:model="form.requested_by_user_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccione un usuario</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('form.requested_by_user_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Cargo o Área -->
                <div>
                    <label for="position_area" class="block text-sm font-medium text-gray-700 mb-2">Cargo o
                        Área</label>
                    <input type="text" id="position_area" wire:model="form.position_area"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Ej: Desarrollador, Marketing">
                    @error('form.position_area')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Nombre del destinatario -->
                <div>
                    <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-2">Nombre del
                        Destinatario</label>
                    <input type="text" id="recipient_name" wire:model="form.recipient_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Nombre completo">
                    @error('form.recipient_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Rol del destinatario -->
                <div>
                    <label for="recipient_role" class="block text-sm font-medium text-gray-700 mb-2">Rol del
                        Destinatario</label>
                    <input type="text" id="recipient_role" wire:model="form.recipient_role"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Ej: Senior, Junior, Lead">
                    @error('form.recipient_role')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tipo de Kit -->
                <div>
                    <label for="kit_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Kit</label>
                    <input type="text" id="kit_type" wire:model="form.kit_type"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Ej: Básico, Avanzado, Premium">
                    @error('form.kit_type')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fecha de solicitud -->
                <div>
                    <label for="request_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de
                        Solicitud</label>
                    <input type="date" id="request_date" wire:model="form.request_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.request_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fecha de entrega -->
                <div>
                    <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de
                        Entrega</label>
                    <input type="date" id="delivery_date" wire:model="form.delivery_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.delivery_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="status_id" wire:model="form.status_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccione un estado</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('form.status_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Responsable de entrega -->
                <div>
                    <label for="delivery_responsible_user_id"
                        class="block text-sm font-medium text-gray-700 mb-2">Responsable
                        de Entrega</label>
                    <select id="delivery_responsible_user_id" wire:model="form.delivery_responsible_user_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccione un responsable</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('form.delivery_responsible_user_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Contenido del Kit -->
                <div class="md:col-span-2">
                    <label for="kit_contents" class="block text-sm font-medium text-gray-700 mb-2">Contenido del
                        Kit</label>
                    <textarea id="kit_contents" wire:model="form.kit_contents" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Descripción detallada del contenido del kit..."></textarea>
                    @error('form.kit_contents')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Observaciones -->
                <div class="md:col-span-2">
                    <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea id="observations" wire:model="form.observations" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Observaciones adicionales..."></textarea>
                    @error('form.observations')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('rrhh.kits.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    {{ isset($kit) ? 'Actualizar Kit' : 'Guardar Kit' }}
                </button>
            </div>
        </form>
    </div>
</div>