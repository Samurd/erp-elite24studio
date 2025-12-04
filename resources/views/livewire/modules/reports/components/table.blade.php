

            <div class="mt-8">
                <div class="relative overflow-x-auto rounded-lg shadow">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-gray-700  bg-gray-50  dark:text-gray-400 uppercase">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Cod
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Titulo
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Responsable
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Fecha
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Hora
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Estado
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
                            @foreach($reports as $report)
                                <tr class="bg-white border-b">
                                    <td class="px-6 py-4">{{ $report->id }}</td>
                                    <td class="px-6 py-4">{{ $report->title }}</td>
                                    <td class="px-6 py-4">{{ $report->user->name }}</td>
                                    <td class="px-6 py-4">
                                        {{ Carbon\Carbon::parse($report->date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $report->hour ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-gray-100 font-bold rounded-md p-2">{{ $report->status->name ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($report->attachment)
                                            <a href="{{ asset($report->attachment) }}" target="_blank"
                                                class="text-blue-600 underline">Ver archivo</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 flex gap-2">
                                        <div class="flex flex-wrap gap-1">
                                            @canArea("update", "reportes")
                                            <a href="{{ route('reports.edit', $report) }}"
                                                >
                                            <x-button>Editar</x-button>
                                            </a>
                                            @endCanArea
                                            @canArea("delete", "reportes")
                                            <x-button wire:click="$dispatch('open-del-modal', { id: {{ $report->id }} })">Eliminar</x-button>
                                            @endCanArea
                                            </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                                <div class="mt-4">
                {{ $reports->links() }}
            </div>
                </div>
            </div>
