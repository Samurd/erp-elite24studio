<div>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                {{ isset($birthday) ? 'Editar Cumpleaños' : 'Nuevo Cumpleaños' }}
            </h1>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Selección de Tipo (Empleado vs Contacto) -->
                <div class="md:col-span-2" x-data="{ isEmployee: @entangle('form.is_employee') }">
                    <div class="flex items-center space-x-4 mb-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="contact_type" x-model="isEmployee" :value="1"
                                class="form-radio text-purple-600">
                            <span class="ml-2">Empleado</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="contact_type" x-model="isEmployee" :value="0"
                                class="form-radio text-purple-600">
                            <span class="ml-2">Contacto</span>
                        </label>
                    </div>

                    <!-- Empleado -->
                    <div x-show="isEmployee == 1" x-transition>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Empleado</label>
                        <select id="employee_id" wire:model="form.employee_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Seleccione un empleado</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                            @endforeach
                        </select>
                        @error('form.employee_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Contacto -->
                    <div x-show="isEmployee == 0" x-transition style="display: none;">
                        <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-2">Contacto</label>
                        <select id="contact_id" wire:model="form.contact_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Seleccione un contacto</option>
                            @foreach($contacts as $contact)
                                <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                            @endforeach
                        </select>
                        @error('form.contact_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Fecha de Cumpleaños -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de
                        Cumpleaños</label>
                    <input type="date" id="date" wire:model="form.date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    @error('form.date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- WhatsApp -->
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-2">WhatsApp</label>
                    <input type="text" id="whatsapp" wire:model="form.whatsapp"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                        placeholder="Ej: +57 300 123 4567">
                    @error('form.whatsapp')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Responsable -->
                <div>
                    <label for="responsible_id" class="block text-sm font-medium text-gray-700 mb-2">Responsable de
                        Registro</label>
                    <select id="responsible_id" wire:model="form.responsible_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Seleccione un responsable</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('form.responsible_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Comentarios -->
                <div class="md:col-span-2">
                    <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">Comentarios</label>
                    <textarea id="comments" wire:model="form.comments" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                        placeholder="Comentarios adicionales..."></textarea>
                    @error('form.comments')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('rrhh.birthdays.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    {{ isset($birthday) ? 'Actualizar Cumpleaños' : 'Guardar Cumpleaños' }}
                </button>
            </div>
        </form>
    </div>
</div>