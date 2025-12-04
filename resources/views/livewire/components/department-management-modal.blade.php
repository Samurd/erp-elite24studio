<x-dialog-modal wire:model="showModal" position="center">
    <x-slot name="title">
        <div class="flex border-b p-4">
            <div class="flex items-center">
                <x-fas-building class="w-5 h-5 mr-2 text-purple-500" />
                <h3 class="text-xl font-bold">
                    {{ $editingDepartmentId ? 'Editar Departamento' : 'Nuevo Departamento' }}
                </h3>
            </div>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="p-4 bg-gray-50">
            <!-- Department Form -->
            <form wire:submit.prevent="{{ $editingDepartmentId ? 'updateDepartment' : 'createDepartment' }}"
                class="space-y-4">
                <div>
                    <label for="departmentName" class="block text-sm font-medium text-gray-700">Nombre del
                        Departamento</label>
                    <input type="text" id="departmentName" wire:model="departmentName"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                        placeholder="Ej: Ventas, Finanzas, Recursos Humanos" required>
                    @error('departmentName')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="departmentDescription"
                        class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea id="departmentDescription" wire:model="departmentDescription" rows="3"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500"
                        placeholder="Descripción opcional del departamento"></textarea>
                    @error('departmentDescription')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="departmentParentId" class="block text-sm font-medium text-gray-700">Departamento
                        Padre</label>
                    <select id="departmentParentId" wire:model="departmentParentId"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Ninguno</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('departmentParentId')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </form>
        </div>
    </x-slot>

    <x-slot name="footer">
        <div class="flex justify-end px-4 py-3 bg-gray-50 border-t">
            <div class="space-x-2">
                <button type="button" wire:click="closeModal"
                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                    Cancelar
                </button>
                <button type="button" wire:click="{{ $editingDepartmentId ? 'updateDepartment' : 'createDepartment' }}"
                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                    {{ $editingDepartmentId ? 'Actualizar Departamento' : 'Crear Departamento' }}
                </button>
            </div>
        </div>
    </x-slot>
</x-dialog-modal>