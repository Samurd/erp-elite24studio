<div class="bg-white rounded-2xl shadow-lg p-6 max-h-[50rem]">
    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
        <h3 class="text-xl font-bold">Ingresos Transacción</h3>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar ingresos..."
                    class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:border-yellow-500">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <a href="{{ route('finances.gross.create') }}">Nuevo Ingreso</a>
        </div>
    </div>

    <div class="overflow-y-auto max-h-[40rem] rounded-lg ">
        <table class="w-full text-sm">
            <thead class="bg-gradient-to-r from-black to-yellow-600 text-white uppercase font-semibold sticky top-0">
                <tr>
                    <th class="p-2 text-left">Descripción</th>
                    <th class="p-2 text-center">Fecha</th>
                    <th class="p-2 text-center">Monto</th>
                    <th class="p-2 text-center">Resultado</th>
                    <th class="p-2 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-800">
                @forelse($incomes as $income)
                    <tr class="hover:bg-gray-50">
                        <td class="p-2">{{ $income->name }} @if($income->description) <span
                        class="block text-xs text-gray-500">{{ $income->description }}</span> @endif</td>
                        <td class="p-2 text-center">{{ $income->date }}</td>
                        <td class="p-2 text-center text-green-600 font-semibold">{{ $income->amount_formatted }}</td>
                        <td class="p-2 text-center"><span
                                class="bg-gray-100  text-xs px-2 py-1 rounded">{{ $income->result->name }}</span></td>
                        <td class="p-2 text-center">
                            <a href="{{ route('finances.gross.edit', $income) }}" wire:navigate
                                class="text-yellow-600 hover:text-yellow-800 font-semibold text-xs mr-2">Editar</a>
                            <button wire:click="delete({{ $income->id }})"
                                    wire:confirm="¿Estás seguro de eliminar este ingreso?"
                                    class="text-red-600 hover:text-red-800 font-semibold text-xs">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-4">No hay ingresos</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-2">
            {{$incomes->links()}}
        </div>
    </div>

    {{-- <p class="text-center text-sm text-yellow-600 mt-3 font-semibold cursor-pointer">
        Show All My Transactions
    </p> --}}
</div>