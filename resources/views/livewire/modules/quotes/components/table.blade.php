
        <div class="mt-8">
                <div class="relative overflow-x-auto rounded-lg shadow">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-gray-700  bg-gray-50  dark:text-gray-400 uppercase">
                            <tr>
                                <th scope="col" ="px-6 py-3">
                                    Cod
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Cliente / Proyecto
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Fecha de emisión
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Valor total
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Estado
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Reponsable
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Adjuntos
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-900">
                            @foreach($quotes as $quote)
                                <tr class="bg-white border-b">
                                    <td class="px-6 py-4">{{ $quote->id }}</td>
                                    <td class="px-6 py-4">{{ $quote->user->name }}</td>
                                    <td class="px-6 py-4">{{ Carbon\Carbon::parse($quote->issued_at)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">
                                        {{ App\Services\MoneyFormatterService::format($quote->total) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $quote->status->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-gray-100 font-bold rounded-md p-2">{{ $quote->user->name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{-- @if($quote->attachment)
                                            <a href="{{ asset($report->attachment) }}" target="_blank"
                                                class="text-blue-600 underline">Ver archivo</a>
                                        @else
                                            -
                                        @endif --}}
                                    </td>
                                    <td class="px-6 py-4 flex gap-2">
                                        <div class="flex flex-wrap gap-1">
                                            @canArea("update", "cotizaciones")
                                            <a href="{{ route('quotes.edit', $quote->id) }}"
                                                >
                                            <x-button>Editar</x-button>
                                            </a>
                                            @endCanArea
                                            @canArea("delete", "cotizaciones")
                                            <x-button wire:click="$dispatch('open-del-modal', { id: {{ $quote->id }} })">Eliminar</x-button>
                                            @endCanArea

                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                                <div class="mt-4">
                {{ $quotes->links() }}
            </div>
                </div>
            </div>
