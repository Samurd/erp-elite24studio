<div class="overflow-x-auto rounded-lg">
    <table class="min-w-full border border-gray-200">
        <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
            <tr>
                <th class="px-4 py-2 border">Cod</th>
                <th class="px-4 py-2 border">Plataforma</th>
                <th class="px-4 py-2 border">Tipo</th>
                <th class="px-4 py-2 border">Valor</th>
                <th class="px-4 py-2 border">Frecuencia</th>
                <th class="px-4 py-2 border">Próxima Renovación</th>
                <th class="px-4 py-2 border">Estado</th>
                <th class="px-4 py-2 border">Notificaciones</th>
                <th class="px-4 py-2 border">Responsable</th>
                <th class="px-4 py-2 border">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subs as $sub)
                <tr class="text-center">
                    <td class="px-4 py-2 border">{{ $sub->id }}</td>
                    <td class="px-4 py-2 border">{{ $sub->name }}</td>
                    <td class="px-4 py-2 border">{{ $sub->type ?? '-' }}</td>
                    <td class="px-4 py-2 border">{{ App\Services\MoneyFormatterService::format($sub->amount) }}</td>
                    <td class="px-4 py-2 border">{{ $sub->frequency->name }}</td>
                    <td class="px-4 py-2 border">{{ Carbon\Carbon::parse($sub->renewal_date)->format('d/m/Y') }}</td>
                    <td class="px-4 py-2 border">
                        {{ $sub->status->name }}
                    </td>
                    <td class="px-4 py-2 border">
                        @php
                            $activeNotifications = $sub->notificationTemplates()->where('is_active', true)->count();
                            $totalNotifications = $sub->notificationTemplates()->count();
                        @endphp
                        @if($activeNotifications > 0)
                            <span
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                </svg>
                                {{ $activeNotifications }} activa{{ $activeNotifications > 1 ? 's' : '' }}
                            </span>
                        @elseif($totalNotifications > 0)
                            <span
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                </svg>
                                Pausada
                            </span>
                        @else
                            <span class="text-xs text-gray-400">Sin notificaciones</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 border">{{ $sub->user->name }}</td>
                    <td class="px-4 py-2 border">
                        <a href="{{ route('subs.edit', $sub) }}">
                            @canArea("update", "suscripciones")
                            <x-button>
                                <i class="fa-solid fa-pen-to-square mr-2"></i> Editar
                            </x-button>
                            @endCanArea
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>

    <div class="mt-4">
        {{ $subs->links() }}
    </div>
</div>