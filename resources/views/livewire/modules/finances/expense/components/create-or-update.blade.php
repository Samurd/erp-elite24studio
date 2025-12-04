<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex border-b pb-4 mb-4">
            <h3 class="text-xl font-bold">{{ isset($expense) ? 'Actualizar Gasto' : 'Añadir nuevo Gasto' }}</h3>
        </div>

        <div id="expenseForm" class="flex flex-col gap-4">

            <!-- Nombre -->
            <div class="mb-3">
                <label for="name" class="block font-semibold mb-1">Nombre</label>
                <x-input id="name" type="text" class="w-full" placeholder="Nombre" wire:model="form.name" />

                @error('form.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Categoria -->
            <div class="mb-3">
                <label for="category" class="block font-semibold mb-1">Categoria</label>
                <select id="category" wire:model="form.category_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600">
                    <option value="">Seleccionar</option>
                    @foreach($categories as $category)
                        <option wire:key="{{ $category->id }}" value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

                @error('form.category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>


            <x-money-input label="Monto" model="form.amount" placeholder="$0.00" />


            <div>
                <label for="observations" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea wire:model="form.description" id="observations"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                    rows="3" placeholder="Agregue sus observaciones"></textarea>

                @error('form.description')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Fecha emisión -->
                <div class="mb-3">
                    <label for="date" class="block font-semibold mb-1">Fecha</label>
                    <x-input id="date" type="date" class="w-full" wire:model="form.date" />

                    @error('form.date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Responsable -->
                <div>
                    <label for="responsible" class="block font-medium mb-1">Reponsable</label>
                    <select id="responsible" wire:model="form.created_by_id"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600">
                        <option value="">Seleccionar</option>
                        @foreach($this->users as $user)
                            <option wire:key="{{ $user->id }}" value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>

                    @error('form.created_by_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Resultado -->
            <div>
                <label for="result_id" class="block font-medium mb-1">Resultado</label>
                <select id="result_id" wire:model="form.result_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600">
                    <option value="">Seleccionar</option>
                    @foreach($this->results as $result)
                        <option wire:key="{{ $result->id }}" value="{{ $result->id }}">{{ $result->name }}</option>
                    @endforeach
                </select>

                @error('form.result_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

        </div>

        <div class="flex justify-end mt-6 pt-4 border-t">
            <x-button href="{{ route('finances.expenses.index') }}" wire:navigate
                class="bg-gray-600 hover:bg-gray-700 text-white mr-2">
                Cancelar
            </x-button>
            <x-button wire:click="save" class="bg-yellow-700 hover:bg-yellow-800 text-white">
                {{ $expense ? 'Actualizar' : 'Crear' }}
            </x-button>
        </div>
    </div>
</div>