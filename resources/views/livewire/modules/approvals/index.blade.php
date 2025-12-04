<div x-data="tabs()" class="flex flex-col flex-1">
    <main class="flex-1 p-10 bg-gray-100">
        <div class="bg-white rounded-lg shadow border border-gray-200 font-sans">
            <!-- Tabs principales -->
            <div class="flex justify-between items-center border-b border-gray-200 text-sm px-5">
                <div class="flex">
                    <button
                        :class="mainTab === 'recibida' ? 'px-5 py-3 border-b-2 border-yellow-700 font-semibold' : 'px-5 py-3 text-gray-600 hover:text-gray-900'"
                        @click="mainTab = 'recibida'; subTab = 'aprobaciones'" type="button">Recibida</button>
                    <button
                        :class="mainTab === 'enviada' ? 'px-5 py-3 border-b-2 border-yellow-700 font-semibold' : 'px-5 py-3 text-gray-600 hover:text-gray-900'"
                        @click="mainTab = 'enviada'; subTab = 'aprobaciones'" type="button">Enviada</button>
                </div>
                <button class="p-2 text-gray-500 hover:text-gray-700" title="Filtro" type="button">
                    <!-- Icono filtro -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 019 21v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                    </svg>
                </button>
            </div>

            <!-- Subpestañas -->
            <div class="flex flex-wrap justify-between items-center gap-3 border-b border-gray-200 px-5 py-3">
                <div class="flex space-x-6 text-sm font-medium">
                    <button
                        :class="subTab === 'aprobaciones' ? 'text-yellow-700 border-b-[3px] border-yellow-700 pb-1' : 'text-gray-400 hover:text-gray-600'"
                        @click="subTab = 'aprobaciones'" type="button">Aprobaciones</button>
                    <button
                        :class="subTab === 'contratos' ? 'text-yellow-700 border-b-[3px] border-yellow-700 pb-1' : 'text-gray-400 hover:text-gray-600'"
                        @click="subTab = 'contratos'" type="button">Contratos/Firmas</button>
                    <button
                        :class="subTab === 'solicitud' ? 'text-yellow-700 border-b-[3px] border-yellow-700 pb-1' : 'text-gray-400 hover:text-gray-600'"
                        @click="subTab = 'solicitud'" type="button">Solicitud de Compra</button>
                </div>
                <div class="flex items-center space-x-3">
                    @canArea('create', 'aprobaciones')
                    <a href="{{ route("approvals.create") }}">
                        <button
                            class="bg-yellow-700 hover:bg-yellow-800 text-white px-4 py-2 rounded-md text-sm font-medium shadow-sm"
                            type="button">
                            + Nueva solicitud de aprobación
                        </button>
                    </a>
                    @endCanArea

                    <button class="text-sm text-gray-500 hover:text-gray-700" type="button">Exportar</button>
                </div>
            </div>

            <!-- Contenido -->
            <div class="p-5">
                <!-- Recibida -->
                <div x-show="mainTab === 'recibida'" class="space-y-4">
                    <div x-show="subTab === 'aprobaciones'">
                        <div class="overflow-x-auto bg-white shadow rounded-lg">
                            <table class="min-w-full  divide-gray-200 text-sm">
                                <thead class="bg-gray-50 text-gray-700 font-semibold">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Prioridad</th>
                                        <th class="px-4 py-3 text-left">Título de la solicitud</th>
                                        <th class="px-4 py-3 text-left">Estado</th>
                                        <th class="px-4 py-3 text-left">Origen</th>
                                        <th class="px-4 py-3 text-left">Creado</th>
                                        <th class="px-4 py-3 text-left">Enviado por</th>
                                        <th class="px-4 py-3 text-left">Enviado a</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($received_approvals as $approval)
                                        <tr class="hover:bg-gray-50 transition duration-150">
                                            {{-- Prioridad --}}
                                            <td class="px-4 py-3">
                                                @if($approval->priority?->name == 'Alta')
                                                    <span class="text-red-500" title="{{ $approval->priority?->name }}">❗</span>
                                                @elseif($approval->priority?->name == 'Media')
                                                    <span class="text-yellow-500"
                                                        title="{{ $approval->priority?->name }}">⚠️</span>
                                                @else
                                                    <span class="text-green-500"
                                                        title="{{ $approval->priority?->name }}">🟢</span>
                                                @endif
                                            </td>

                                            {{-- Título --}}
                                            <td class="px-4 py-3 font-medium text-gray-800">
                                                {{ $approval->name }}
                                            </td>

                                            {{-- Estado --}}
                                            <td class="px-4 py-3">
                                                <span
                                                    class="px-2 py-1 rounded text-xs font-semibold
                                                                                                                                                                                    @if($approval->status?->name == 'Aprobado') bg-green-100 text-green-800
                                                                                                                                                                                    @elseif($approval->status?->name == 'Rechazado') bg-red-100 text-red-800
                                                                                                                                                                                    @elseif($approval->status?->name == 'En espera') bg-yellow-100 text-yellow-800
                                                                                                                                                                                    @elseif($approval->status?->name == 'Cancelado') bg-gray-100 text-gray-700
                                                                                                                                                                                    @else bg-gray-100 text-gray-600 @endif">
                                                    {{ $approval->status?->name ?? 'N/A' }}
                                                </span>
                                            </td>

                                            {{-- Origen --}}
                                            <td class="px-4 py-3">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 text-amber-800 rounded text-xs font-medium">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M4 4v16h16" />
                                                    </svg>
                                                    {{ $approval->buy ? 'Solicitud de Compra' : 'Aprobación' }}
                                                </span>
                                            </td>

                                            {{-- Fecha --}}
                                            <td class="px-4 py-3 text-gray-500 text-xs">
                                                {{ $approval->created_at->format('d/m/Y h:i:s a') }}
                                            </td>

                                            {{-- Enviado por --}}
                                            <td class="px-4 py-3 text-gray-700">
                                                <div class="flex items-center gap-2">
                                                    <img src="{{ $approval->creator?->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($approval->creator?->name) }}"
                                                        alt="{{ $approval->creator?->name }}" class="w-6 h-6 rounded-full">
                                                    <span>{{ $approval->creator?->name ?? 'N/A' }}</span>
                                                </div>
                                            </td>

                                            {{-- Enviado a --}}
                                            <td class="px-4 py-3">
                                                <div class="flex -space-x-2">
                                                    @foreach($approval->approvers->take(3) as $approver)
                                                        <img src="{{ $approver->user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($approver->user?->name) }}"
                                                            alt="{{ $approver->user?->name }}"
                                                            class="w-6 h-6 rounded-full border-2 border-white">
                                                    @endforeach
                                                    @if($approval->approvers->count() > 3)
                                                        <span
                                                            class="text-xs text-gray-500 pl-2">+{{ $approval->approvers->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Acciones --}}
                                            <td class="px-4 py-3 text-right relative" x-data="{ open: false }">
                                                <!-- Botón ⋮ -->
                                                <button @click="open = !open"
                                                    class="text-gray-500 hover:text-gray-800 focus:outline-none">
                                                    ⋮
                                                </button>

                                                <!-- Menú contextual -->
                                                <div x-show="open" @click.outside="open = false" x-transition
                                                    class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                                    <ul class="py-1 text-sm text-gray-700">
                                                        <li>
                                                            <button wire:click="showApproval({{ $approval->id }})"
                                                                @click="open = false; window.dispatchEvent(new CustomEvent('open-approval-modal'))"
                                                                class="flex w-full items-center px-4 py-2 hover:bg-gray-100">
                                                                Ver detalle
                                                            </button>
                                                        </li>
                                                        {{-- Aquí podrás agregar más acciones luego --}}
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- Paginación --}}
                            <div class="p-3 border-t border-gray-200">
                                {{ $approvals_sent->links() }}
                            </div>
                        </div>
                    </div>
                    <div x-show="subTab === 'contratos'">
                        <iframe src="https://elite-24-studio-sas.odoo.com/odoo/sign-documents" width="100%" height="800"
                            frameborder="0" style="border: none;"></iframe>

                    </div>
                    <div x-show="subTab === 'solicitud'">
                        <div class="overflow-x-auto bg-white shadow rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 text-gray-700 font-semibold">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Prioridad</th>
                                        <th class="px-4 py-3 text-left">Título de la solicitud</th>
                                        <th class="px-4 py-3 text-left">Estado</th>
                                        <th class="px-4 py-3 text-left">Origen</th>
                                        <th class="px-4 py-3 text-left">Creado</th>
                                        <th class="px-4 py-3 text-left">Enviado por</th>
                                        <th class="px-4 py-3 text-left">Enviado a</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($received_buy_approvals as $approval)
                                        <tr class="hover:bg-gray-50 transition duration-150">
                                            {{-- Prioridad --}}
                                            <td class="px-4 py-3">
                                                @if($approval->priority?->name == 'Alta')
                                                    <span class="text-red-500" title="{{ $approval->priority?->name }}">❗</span>
                                                @elseif($approval->priority?->name == 'Media')
                                                    <span class="text-yellow-500"
                                                        title="{{ $approval->priority?->name }}">⚠️</span>
                                                @else
                                                    <span class="text-green-500"
                                                        title="{{ $approval->priority?->name }}">🟢</span>
                                                @endif
                                            </td>

                                            {{-- Título --}}
                                            <td class="px-4 py-3 font-medium text-gray-800">
                                                {{ $approval->name }}
                                            </td>

                                            {{-- Estado --}}
                                            <td class="px-4 py-3">
                                                <span
                                                    class="px-2 py-1 rounded text-xs font-semibold
                                                                                                                                                                                    @if($approval->status?->name == 'Aprobado') bg-green-100 text-green-800
                                                                                                                                                                                    @elseif($approval->status?->name == 'Rechazado') bg-red-100 text-red-800
                                                                                                                                                                                    @elseif($approval->status?->name == 'En espera') bg-yellow-100 text-yellow-800
                                                                                                                                                                                    @elseif($approval->status?->name == 'Cancelado') bg-gray-100 text-gray-700
                                                                                                                                                                                    @else bg-gray-100 text-gray-600 @endif">
                                                    {{ $approval->status?->name ?? 'N/A' }}
                                                </span>
                                            </td>

                                            {{-- Origen --}}
                                            <td class="px-4 py-3">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 text-amber-800 rounded text-xs font-medium">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M4 4v16h16" />
                                                    </svg>
                                                    {{ $approval->buy ? 'Solicitud de Compra' : 'Aprobación' }}
                                                </span>
                                            </td>

                                            {{-- Fecha --}}
                                            <td class="px-4 py-3 text-gray-500 text-xs">
                                                {{ $approval->created_at->format('d/m/Y h:i:s a') }}
                                            </td>

                                            {{-- Enviado por --}}
                                            <td class="px-4 py-3 text-gray-700">
                                                <div class="flex items-center gap-2">
                                                    <img src="{{ $approval->creator?->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($approval->creator?->name) }}"
                                                        alt="{{ $approval->creator?->name }}" class="w-6 h-6 rounded-full">
                                                    <span>{{ $approval->creator?->name ?? 'N/A' }}</span>
                                                </div>
                                            </td>

                                            {{-- Enviado a --}}
                                            <td class="px-4 py-3">
                                                <div class="flex -space-x-2">
                                                    @foreach($approval->approvers->take(3) as $approver)
                                                        <img src="{{ $approver->user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($approver->user?->name) }}"
                                                            alt="{{ $approver->user?->name }}"
                                                            class="w-6 h-6 rounded-full border-2 border-white">
                                                    @endforeach
                                                    @if($approval->approvers->count() > 3)
                                                        <span
                                                            class="text-xs text-gray-500 pl-2">+{{ $approval->approvers->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Acciones --}}
                                            <td class="px-4 py-3 text-right relative" x-data="{ open: false }">
                                                <!-- Botón ⋮ -->
                                                <button @click="open = !open"
                                                    class="text-gray-500 hover:text-gray-800 focus:outline-none">
                                                    ⋮
                                                </button>

                                                <!-- Menú contextual -->
                                                <div x-show="open" @click.outside="open = false" x-transition
                                                    class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                                    <ul class="py-1 text-sm text-gray-700">
                                                        <li>
                                                            <button wire:click="showApproval({{ $approval->id }})"
                                                                @click="open = false; window.dispatchEvent(new CustomEvent('open-approval-modal'))"
                                                                class="flex w-full items-center px-4 py-2 hover:bg-gray-100">
                                                                Ver detalle
                                                            </button>
                                                        </li>
                                                        {{-- Aquí podrás agregar más acciones luego --}}
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- Paginación --}}
                            <div class="p-3 border-t border-gray-200">
                                {{ $approvals_sent->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enviada -->
                <div x-show="mainTab === 'enviada'" class="space-y-4">
                    <div x-show="subTab === 'aprobaciones'">
                        <div class="overflow-x-auto bg-white shadow rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 text-gray-700 font-semibold">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Prioridad</th>
                                        <th class="px-4 py-3 text-left">Título de la solicitud</th>
                                        <th class="px-4 py-3 text-left">Estado</th>
                                        <th class="px-4 py-3 text-left">Origen</th>
                                        <th class="px-4 py-3 text-left">Creado</th>
                                        <th class="px-4 py-3 text-left">Enviado por</th>
                                        <th class="px-4 py-3 text-left">Enviado a</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($approvals_sent as $approval)
                                        <tr class="hover:bg-gray-50 transition duration-150">
                                            {{-- Prioridad --}}
                                            <td class="px-4 py-3">
                                                @if($approval->priority?->name == 'Alta')
                                                    <span class="text-red-500">❗</span>
                                                @elseif($approval->priority?->name == 'Media')
                                                    <span class="text-yellow-500">⚠️</span>
                                                @else
                                                    <span class="text-green-500">🟢</span>
                                                @endif
                                            </td>

                                            {{-- Título --}}
                                            <td class="px-4 py-3 font-medium text-gray-800">
                                                {{ $approval->name }}
                                            </td>

                                            {{-- Estado --}}
                                            <td class="px-4 py-3">
                                                <span
                                                    class="px-2 py-1 rounded text-xs font-semibold
                                                                                                                                                                                            @if($approval->status?->name == 'Aprobado') bg-green-100 text-green-800
                                                                                                                                                                                            @elseif($approval->status?->name == 'Rechazado') bg-red-100 text-red-800
                                                                                                                                                                                            @elseif($approval->status?->name == 'En espera') bg-yellow-100 text-yellow-800
                                                                                                                                                                                            @elseif($approval->status?->name == 'Cancelado') bg-gray-100 text-gray-700
                                                                                                                                                                                            @else bg-gray-100 text-gray-600 @endif">
                                                    {{ $approval->status?->name ?? 'N/A' }}
                                                </span>
                                            </td>

                                            {{-- Origen --}}
                                            <td class="px-4 py-3">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 text-amber-800 rounded text-xs font-medium">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M4 4v16h16" />
                                                    </svg>
                                                    {{ $approval->buy ? 'Solicitud de Compra' : 'Aprobación' }}
                                                </span>
                                            </td>

                                            {{-- Fecha --}}
                                            <td class="px-4 py-3 text-gray-500 text-xs">
                                                {{ $approval->created_at->format('d/m/Y h:i:s a') }}
                                            </td>

                                            {{-- Enviado por --}}
                                            <td class="px-4 py-3 text-gray-700">
                                                <div class="flex items-center gap-2">
                                                    <img src="{{ $approval->creator?->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($approval->creator?->name) }}"
                                                        alt="{{ $approval->creator?->name }}" class="w-6 h-6 rounded-full">
                                                    <span>{{ $approval->creator?->name ?? 'N/A' }}</span>
                                                </div>
                                            </td>

                                            {{-- Enviado a --}}
                                            <td class="px-4 py-3">
                                                <div class="flex -space-x-2">
                                                    @foreach($approval->approvers->take(3) as $approver)
                                                        <img src="{{ $approver->user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($approver->user?->name) }}"
                                                            alt="{{ $approver->user?->name }}"
                                                            class="w-6 h-6 rounded-full border-2 border-white">
                                                    @endforeach
                                                    @if($approval->approvers->count() > 3)
                                                        <span
                                                            class="text-xs text-gray-500 pl-2">+{{ $approval->approvers->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Acciones --}}
                                            <td class="px-4 py-3 text-right relative" x-data="{ open: false }">
                                                <!-- Botón ⋮ -->
                                                <button @click="open = !open"
                                                    class="text-gray-500 hover:text-gray-800 focus:outline-none">
                                                    ⋮
                                                </button>

                                                <!-- Menú contextual -->
                                                <div x-show="open" @click.outside="open = false" x-transition
                                                    class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                                    <ul class="py-1 text-sm text-gray-700">
                                                        <li>
                                                            <button wire:click="showApproval({{ $approval->id }})"
                                                                @click="open = false; window.dispatchEvent(new CustomEvent('open-approval-modal'))"
                                                                class="flex w-full items-center px-4 py-2 hover:bg-gray-100">
                                                                Ver detalle
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('approvals.edit', $approval->id) }}"
                                                                class="flex w-full items-center px-4 py-2 hover:bg-gray-100">
                                                                Editar
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <button wire:click="deleteApproval({{ $approval->id }})"
                                                                wire:confirm="¿Estás seguro de que deseas eliminar esta solicitud?"
                                                                class="flex w-full items-center px-4 py-2 hover:bg-gray-100 text-red-600">
                                                                Eliminar
                                                            </button>
                                                        </li>


                                                        {{-- Aquí podrás agregar más acciones luego --}}
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- Paginación --}}
                            <div class="p-3 border-t border-gray-200">
                                {{ $approvals_sent->links() }}
                            </div>
                        </div>
                    </div>

                    <div x-show="subTab === 'contratos'">
                        <iframe src="https://elite-24-studio-sas.odoo.com/odoo/sign-documents" width="100%" height="800"
                            frameborder="0" style="border: none;"></iframe>

                    </div>

                    <div x-show="subTab === 'solicitud'">
                        <div class="overflow-x-auto bg-white shadow rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 text-gray-700 font-semibold">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Prioridad</th>
                                        <th class="px-4 py-3 text-left">Título de la solicitud</th>
                                        <th class="px-4 py-3 text-left">Estado</th>
                                        <th class="px-4 py-3 text-left">Origen</th>
                                        <th class="px-4 py-3 text-left">Creado</th>
                                        <th class="px-4 py-3 text-left">Enviado por</th>
                                        <th class="px-4 py-3 text-left">Enviado a</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($buy_approvals_sent as $approval)
                                        <tr class="hover:bg-gray-50 transition duration-150">
                                            {{-- Prioridad --}}
                                            <td class="px-4 py-3">
                                                @if($approval->priority?->name == 'Alta')
                                                    <span class="text-red-500" title="{{ $approval->priority?->name }}">❗</span>
                                                @elseif($approval->priority?->name == 'Media')
                                                    <span class="text-yellow-500"
                                                        title="{{ $approval->priority?->name }}">⚠️</span>
                                                @else
                                                    <span class="text-green-500"
                                                        title="{{ $approval->priority?->name }}">🟢</span>
                                                @endif
                                            </td>

                                            {{-- Título --}}
                                            <td class="px-4 py-3 font-medium text-gray-800">
                                                {{ $approval->name }}
                                            </td>

                                            {{-- Estado --}}
                                            <td class="px-4 py-3">
                                                <span
                                                    class="px-2 py-1 rounded text-xs font-semibold
                                                                                                                                                                                        @if($approval->status?->name == 'Aprobado') bg-green-100 text-green-800
                                                                                                                                                                                        @elseif($approval->status?->name == 'Rechazado') bg-red-100 text-red-800
                                                                                                                                                                                        @elseif($approval->status?->name == 'En espera') bg-yellow-100 text-yellow-800
                                                                                                                                                                                        @elseif($approval->status?->name == 'Cancelado') bg-gray-100 text-gray-700
                                                                                                                                                                                        @else bg-gray-100 text-gray-600 @endif">
                                                    {{ $approval->status?->name ?? 'N/A' }}
                                                </span>
                                            </td>

                                            {{-- Origen --}}
                                            <td class="px-4 py-3">
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 text-amber-800 rounded text-xs font-medium">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M4 4v16h16" />
                                                    </svg>
                                                    {{ $approval->buy ? 'Solicitud de Compra' : 'Aprobación' }}
                                                </span>
                                            </td>

                                            {{-- Fecha --}}
                                            <td class="px-4 py-3 text-gray-500 text-xs">
                                                {{ $approval->created_at->format('d/m/Y h:i:s a') }}
                                            </td>

                                            {{-- Enviado por --}}
                                            <td class="px-4 py-3 text-gray-700">
                                                <div class="flex items-center gap-2">
                                                    <img src="{{ $approval->creator?->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($approval->creator?->name) }}"
                                                        alt="{{ $approval->creator?->name }}" class="w-6 h-6 rounded-full">
                                                    <span>{{ $approval->creator?->name ?? 'N/A' }}</span>
                                                </div>
                                            </td>

                                            {{-- Enviado a --}}
                                            <td class="px-4 py-3">
                                                <div class="flex -space-x-2">
                                                    @foreach($approval->approvers->take(3) as $approver)
                                                        <img src="{{ $approver->user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($approver->user?->name) }}"
                                                            alt="{{ $approver->user?->name }}"
                                                            class="w-6 h-6 rounded-full border-2 border-white">
                                                    @endforeach
                                                    @if($approval->approvers->count() > 3)
                                                        <span
                                                            class="text-xs text-gray-500 pl-2">+{{ $approval->approvers->count() - 3 }}</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Acciones --}}
                                            <td class="px-4 py-3 text-right relative" x-data="{ open: false }">
                                                <!-- Botón ⋮ -->
                                                <button @click="open = !open"
                                                    class="text-gray-500 hover:text-gray-800 focus:outline-none">
                                                    ⋮
                                                </button>

                                                <!-- Menú contextual -->
                                                <div x-show="open" @click.outside="open = false" x-transition
                                                    class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                                    <ul class="py-1 text-sm text-gray-700">
                                                        <li>
                                                            <button wire:click="showApproval({{ $approval->id }})"
                                                                @click="open = false; window.dispatchEvent(new CustomEvent('open-approval-modal'))"
                                                                class="flex w-full items-center px-4 py-2 hover:bg-gray-100">
                                                                Ver detalle
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('approvals.edit', $approval->id) }}"
                                                                class="flex w-full items-center px-4 py-2 hover:bg-gray-100">
                                                                Editar
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <button wire:click="deleteApproval({{ $approval->id }})"
                                                                wire:confirm="¿Estás seguro de que deseas eliminar esta solicitud?"
                                                                class="flex w-full items-center px-4 py-2 hover:bg-gray-100 text-red-600">
                                                                Eliminar
                                                            </button>
                                                        </li>


                                                        {{-- Aquí podrás agregar más acciones luego --}}
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- Paginación --}}
                            <div class="p-3 border-t border-gray-200">
                                {{ $approvals_sent->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </main>

    <!-- MODAL INFO ROW -->

    <!-- MODAL INFO ROW -->

    <div x-data="{ open: false }" x-on:open-approval-modal.window="open = true"
        x-on:close-approval-modal.window="open = false" x-cloak wire:ignore.self>
        <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="open" @click.outside="open = false" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center gap-2"
                                    id="modal-title">
                                    <div class="bg-yellow-500 text-white p-2 rounded">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                    @if($selectedApproval)
                                        <span>Detalles de la solicitud de
                                            {{ $selectedApproval->buy ? 'compra' : 'aprobación' }}</span>
                                    @else
                                        <span>Cargando detalles...</span>
                                    @endif
                                </h3>

                                <div class="mt-4">
                                    <div wire:loading wire:target="showApproval" class="w-full text-center py-10">
                                        <svg class="animate-spin h-8 w-8 text-yellow-500 mx-auto"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <p class="mt-2 text-gray-500">Cargando información...</p>
                                    </div>

                                    <div wire:loading.remove wire:target="showApproval">
                                        @if ($selectedApproval)
                                            <div class="space-y-4">
                                                <div>
                                                    <span class="text-sm text-gray-500">Estado:</span>
                                                    <span class="px-2 py-1 rounded text-white
                                                                        @if($selectedApproval->status?->name == 'Aprobado') bg-green-600
                                                                        @elseif($selectedApproval->status?->name == 'Rechazado') bg-red-600
                                                                        @elseif($selectedApproval->status?->name == 'Cancelado') bg-gray-500
                                                                        @else bg-yellow-500 @endif">
                                                        {{ $selectedApproval->status?->name ?? 'N/A' }}
                                                    </span>
                                                </div>

                                                <div>
                                                    <h3 class="text-lg font-semibold">{{ $selectedApproval->name }}</h3>
                                                    <p class="text-gray-700">{{ $selectedApproval->description }}</p>
                                                </div>

                                                <div>
                                                    <h4 class="font-semibold text-gray-800">Aprobadores</h4>
                                                    <ul class="mt-2 space-y-1 text-sm w-full">
                                                        @foreach ($selectedApproval->approvers as $approver)
                                                            <li
                                                                class="flex flex-col items-start gap-2 w-full bg-gray-100 rounded-lg p-2">
                                                                <div class="flex items-center justify-between gap-2 w-full">
                                                                    <div class="flex items-center gap-2">
                                                                        <img src="{{ $approver->user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($approver->user?->name) }}"
                                                                            alt="{{ $approver->user?->name }}"
                                                                            class="w-6 h-6 rounded-full border-2 border-white">
                                                                        <span>{{ $approver->user->name }}</span>
                                                                    </div>
                                                                    <span
                                                                        class="ml-auto text-gray-500 text-xs font-bold">{{ $approver->status?->name ?? 'Pendiente' }}
                                                                    </span>
                                                                </div>
                                                                @if ($approver->comment)
                                                                    <div
                                                                        class=" p-2 w-full bg-white rounded border border-gray-200 text-gray-600 italic">
                                                                        "{{ $approver->comment }}"
                                                                    </div>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>

                                                @if ($selectedApproval->files && count($selectedApproval->files))
                                                    <div>
                                                        <h4 class="font-semibold text-gray-800 mb-2">Archivos adjuntos</h4>
                                                        <ul class="space-y-2">
                                                            @foreach ($selectedApproval->files as $file)
                                                                <li>
                                                                    <a href="{{ Storage::url($file->path) }}" target="_blank"
                                                                        class="text-blue-600 hover:underline flex items-center gap-2">
                                                                        <i class="fas fa-file"></i> {{ $file->name }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif

                                                @if ($hasResponded)
                                                    <div>
                                                        <label for="comment"
                                                            class="block text-sm font-medium text-gray-700">Comentarios</label>
                                                        <textarea wire:model.defer="comment" id="comment"
                                                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500 mt-1"
                                                            rows="3" placeholder="Agregue sus comentarios aquí..."></textarea>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <div wire:loading.remove wire:target="showApproval">
                            @if ($hasResponded && $selectedApproval)
                                <button wire:click="approve({{ $selectedApproval->id }})" type="button"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Aprobar
                                </button>
                                <button wire:click="reject({{ $selectedApproval->id }})" type="button"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Rechazar
                                </button>
                            @endif
                        </div>
                        <button @click="open = false" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>







    <script>
        function tabs() {
            return {
                mainTab: 'recibida',
                subTab: 'aprobaciones'
            }
        }
    </script>
</div>