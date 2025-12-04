<!-- Layout estilo Slack/Microsoft Teams -->
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar izquierda -->
    <div class="w-64 bg-gray-100 text-gray-900 flex flex-col">
        <!-- Header -->
        <div class="p-4 border-b border-gray-300">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold flex items-center">
                    <i class="fas fa-users mr-2"></i>
                    Teams
                </h1>
                @canArea('create', 'teams')
                <a href="{{ route('teams.create') }}"
                    class="bg-yellow-600 hover:bg-yellow-700 text-white p-2 rounded-lg transition-colors">
                    Crear equipo
                </a>
                @endCanArea
            </div>
        </div>

        <!-- Filtros -->
        <div class="p-4 border-b border-gray-300">
            <div class="space-y-3">
                <!-- Búsqueda -->
                <div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar equipos..."
                        class="w-full px-3 py-2 bg-gray-200 border border-gray-700 rounded-lg text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                </div>

                <!-- Filtro por tipo -->
                <div>
                    <select wire:model.live="isPublicFilter"
                        class="w-full px-3 py-2 bg-gray-200 border border-gray-700 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Todos los equipos</option>
                        <option value="1">Públicos</option>
                        <option value="0">Privados</option>
                    </select>
                </div>

                <!-- Registros por página -->
                <div>
                    <select wire:model.live="perPage"
                        class="w-full px-3 py-2 bg-gray-200 border border-gray-700 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="10">10 equipos</option>
                        <option value="25">25 equipos</option>
                        <option value="50">50 equipos</option>
                        <option value="100">100 equipos</option>
                    </select>
                </div>

                <!-- Botón limpiar -->
                <button wire:click="clearFilters"
                    class="w-full bg-gray-400 hover:bg-gray-600 text-white px-3 py-2 rounded-lg transition-colors">
                    <i class="fas fa-times mr-2"></i>Limpiar filtros
                </button>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="p-4 border-b border-gray-300">
            <div class="text-sm text-gray-400">
                <div class="flex justify-between">
                    <span>Total de equipos:</span>
                    <span class="text-black font-semibold">{{ $teams->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="flex-1 overflow-hidden">
        <!-- Navegación superior -->
        <div class="bg-gray-200 text-gray-900 px-6 py-3">
            <div class="flex items-center space-x-6">
                <a href="{{ route('teams.index') }}"
                    class="flex items-center space-x-2 text-gray-900 hover:text-white transition-colors">
                    <i class="fas fa-users"></i>
                    <span>Equipos</span>
                </a>
                <a href="{{ route('teams.chats') }}"
                    class="flex items-center space-x-2 text-gray-900 hover:text-white transition-colors">
                    <i class="fas fa-comments"></i>
                    <span>Chats</span>
                </a>
                @canArea('create', 'teams')
                <a href="{{ route('teams.create') }}"
                    class="flex items-center space-x-2 text-gray-900 hover:text-white transition-colors">
                    <i class="fas fa-plus"></i>
                    <span>Nuevo Equipo</span>
                </a>
                @endCanArea
            </div>
        </div>

        <!-- Header superior -->
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Todos los equipos</h2>
                    <p class="text-gray-600 mt-1">Gestiona tus equipos y colaboraciones</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500">
                        Mostrando {{ $teams->firstItem() ?? 0 }} - {{ $teams->lastItem() ?? 0 }} de
                        {{ $teams->total() }} equipos
                    </span>
                </div>
            </div>
        </div>

        <!-- Grid de equipos estilo Teams -->
        <div class="p-6 overflow-y-auto" style="height: calc(100vh - 88px);">
            @forelse($teams as $team)
                <a href="{{ route('teams.channels.index', $team->id) }}"
                    class="block bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4 hover:shadow-sm hover:border-yellow-300 transition-all cursor-pointer">
                    <div class="flex items-start justify-between">
                        <!-- Info principal del equipo -->
                        <div class="flex items-start space-x-4 flex-1">
                            <!-- Avatar del equipo -->
                            <div class="flex-shrink-0">
                                <div
                                    class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-users text-white text-2xl"></i>
                                </div>
                            </div>

                            <!-- Detalles del equipo -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $team->name }}
                                    </h3>
                                    @if($team->isPublic)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-globe mr-1"></i>Público
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-lock mr-1"></i>Privado
                                        </span>
                                    @endif

                                    <!-- Badge del rol del usuario -->
                                    @if($team->members->first())
                                        @php
                                            $roleId = $team->members->first()->pivot->role_id;
                                            $role = \App\Models\TeamRole::find($roleId);
                                        @endphp
                                        @if($role && $role->slug === 'owner')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-crown mr-1"></i>Propietario
                                            </span>
                                        @elseif($role && $role->slug === 'member')
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-user mr-1"></i>Miembro
                                            </span>
                                        @endif
                                    @endif
                                </div>

                                @if($team->description)
                                    <p class="mt-2 text-sm text-gray-600 line-clamp-2">
                                        {{ $team->description }}
                                    </p>
                                @endif

                                <!-- Estadísticas del equipo -->
                                <div class="mt-4 flex items-center space-x-6">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-user-friends mr-2 text-yellow-500"></i>
                                        <span class="font-medium text-gray-900">{{ $team->members_count }}</span>
                                        <span class="ml-1">miembros</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-hashtag mr-2 text-blue-500"></i>
                                        <span class="font-medium text-gray-900">{{ $team->channels_count }}</span>
                                        <span class="ml-1">canales</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron equipos</h3>
                    <p class="text-gray-500 mb-6">No hay equipos que coincidan con tu búsqueda.</p>
                    <button wire:click="clearFilters"
                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>Limpiar filtros
                    </button>
                </div>
            @endforelse

            <!-- Paginación -->
            @if($teams->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $teams->links() }}
                </div>
            @endif
        </div>
    </div>
</div>