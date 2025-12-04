<!-- Layout estilo Slack/Microsoft Teams -->
<div class="flex h-screen bg-gray-50">
    <!-- Notificaciones flotantes -->
    @if (session()->has('message'))
        <div class="fixed top-4 right-4 z-50 bg-green-500 text-white p-4 rounded-lg shadow-lg animate-fade-in">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 z-50 bg-red-500 text-white p-4 rounded-lg shadow-lg animate-fade-in">
            {{ session('error') }}
        </div>
    @endif

    @if($isPrivateTeamNonMember)
        <div class="flex-1 flex flex-col items-center justify-center bg-gray-50">
            <div class="text-center max-w-md px-4">
                <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-lock text-gray-400 text-4xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">No estás en este equipo</h2>
                <p class="text-gray-600 mb-8">
                    Parece que aún no te has unido a este equipo. Si deseas participar, puedes solicitar acceso a un
                    administrador.
                </p>
                <a href="{{ route('teams.index') }}"
                    class="inline-flex items-center text-yellow-600 hover:text-yellow-700 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver a mis equipos
                </a>
            </div>
        </div>
    @else

            <!-- Componente para el formulario de canales (modal) -->
            {{-- <livewire:modules.teams.channel-form :team="$team" /> --}}

            <!-- Sidebar izquierda -->
            <div class="w-64 bg-gray-100 text-gray-900 flex flex-col">
                <!-- Header -->
                <div class="p-4 border-b border-gray-300">
                    <div class="flex flex-col space-y-3">
                        <div class="flex items-center justify-between">
                            <h1 class="text-xl font-bold flex items-center">
                                <i class="fas fa-users mr-2"></i>
                                {{ $team->name }}
                            </h1>
                            @if($isMember && $currentUserRole)
                                @if(($currentUserRole['slug'] ?? '') === 'owner')
                                    <span
                                        class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full border border-yellow-200 flex items-center">
                                        <i class="fas fa-crown mr-1 text-xs"></i>{{ $currentUserRole['name'] }}
                                    </span>
                                @elseif(($currentUserRole['slug'] ?? '') === 'member')
                                    <span
                                        class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full border border-blue-200 flex items-center">
                                        <i class="fas fa-user mr-1 text-xs"></i>{{ $currentUserRole['name'] }}
                                    </span>
                                @else
                                    <span
                                        class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full border border-gray-200 flex items-center">
                                        {{ $currentUserRole['name'] ?? 'Miembro' }}
                                    </span>
                                @endif
                            @endif
                        </div>

                        @if(!$isMember && $team->isPublic)
                            <button wire:click="joinTeam"
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center text-sm font-medium">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Unirse al Equipo
                            </button>
                        @endif

                        @if($isMember)
                            <button wire:click="leaveTeam"
                                wire:confirm="¿Estás seguro de que quieres salir de este equipo? Podrás volver a unirte más tarde si el equipo es público."
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center text-sm font-medium">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Salir del Equipo
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Información del equipo -->
                <div class="p-4 border-b border-gray-300">
                    <div class="text-sm text-gray-600 space-y-2">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>{{ $team->isPublic ? 'Equipo Público' : 'Equipo Privado' }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-calendar mr-2"></i>
                            <span>Creado: {{ $team->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-user-friends mr-2"></i>
                            <span>{{ count($members) }} miembros</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-hashtag mr-2"></i>
                            <span>{{ count($channels) }} canales</span>
                        </div>
                    </div>
                </div>

                <!-- Miembros del equipo -->
                <div class="p-4 border-b border-gray-300">
                    <h3 class="text-sm font-semibold mb-3">Miembros del Equipo</h3>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($members as $member)
                            <div class="flex items-center p-2 hover:bg-gray-200 rounded-lg transition-colors group">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-yellow-600 font-medium text-sm">
                                        {{ substr($member['name'], 0, 2) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-black truncate">{{ $member['name'] }}</div>
                                    <div class="text-sm text-gray-400 truncate">{{ $member['email'] }}</div>
                                </div>
                                @if($member['id'] != Auth::id())
                                    <a href="{{ route('teams.chats', $member['id']) }}" wire:navigate
                                        class="opacity-0 group-hover:opacity-100 transition-all ml-2 p-1.5 rounded  text-yellow-400 hover:text-yellow-500"
                                        title="Chat con {{ $member['name'] }}">
                                        <x-lucide-message-circle class="w-6 h-6" />
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="p-4 space-y-2">
                    <a href="{{ route('teams.chats') }}"
                        class="flex items-center space-x-2 text-gray-500 hover:text-black transition-colors p-2 rounded hover:bg-gray-200">
                        <i class="fas fa-comments"></i>
                        <span>Chats privados</span>
                    </a>
                    <a href="{{ route('teams.index') }}"
                        class="flex items-center space-x-2 text-gray-500 hover:text-black transition-colors p-2 rounded hover:bg-gray-200">
                        <i class="fas fa-arrow-left"></i>
                        <span>Volver a equipos</span>
                    </a>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="flex-1 overflow-hidden">
                @if($channel)
                    <!-- Vista de canal específico -->
                    <div class="h-full flex flex-col">
                        <!-- Header del canal -->
                        <div class="bg-white border-b border-gray-200 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('teams.channels.index', $team->id) }}" wire:navigate
                                        class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-colors"
                                        title="Volver a canales">
                                        <x-lucide-arrow-left class="w-6 h-6" />
                                    </a>

                                    <div>
                                        <h2 class="text-xl font-bold text-gray-900">{{ $channel->name }}</h2>
                                        <p class="text-sm text-gray-600">{{ $team->name }} -
                                            {{ $channel->is_private ? 'Canal Privado' : 'Canal Público' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Área de mensajes del canal -->
                        <div x-data x-ref="messagesContainer"
                            @messages-loaded.window="
                                                                                                                                                                                                                                                                                                                                                                                                await $nextTick();
                                                                                                                                                                                                                                                                                                                                                                                                $refs.messagesContainer.scrollTop = $refs.messagesContainer.scrollHeight;
                                                                                                                                                                                                                                                                                                                                                                                            "
                            @message-added.window="
                                                                                                                                                                                                                                                                                                                                                                                                await $nextTick();
                                                                                                                                                                                                                                                                                                                                                                                                $refs.messagesContainer.scrollTop = $refs.messagesContainer.scrollHeight;
                                                                                                                                                                                                                                                                                                                                                                                            "
                            class="flex-1 p-6 overflow-y-auto bg-gray-50">
                            <div class="max-w-4xl mx-auto space-y-4">
                                @php
                                    // Verificar membresía del canal usando la lógica del componente
                                    $isChannelMember = false;
                                    if ($isMember) {
                                        if (!$channel->is_private) {
                                            // Canal público: todos los miembros del equipo tienen acceso
                                            $isChannelMember = true;
                                        } else {
                                            // Canal privado: verificar si está en la tabla pivot
                                            $isChannelMember = $channel->members()->where('user_id', Auth::id())->exists();
                                        }
                                    }
                                @endphp

                                @if(!$isMember)
                                    <!-- Overlay para no miembros del equipo -->
                                    <div class="flex flex-col items-center justify-center h-full py-12">
                                        <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                                            @if($team->isPublic)
                                                <i class="fas fa-users text-gray-400 text-2xl"></i>
                                            @else
                                                <i class="fas fa-lock text-gray-400 text-2xl"></i>
                                            @endif
                                        </div>

                                        @if($team->isPublic)
                                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Únete al equipo {{ $team->name }}</h3>
                                            <p class="text-gray-600 mb-6 text-center max-w-md">
                                                Este es un equipo público. Para ver los mensajes y participar en este canal, primero debes
                                                unirte al equipo.
                                            </p>
                                            <button wire:click="joinTeam"
                                                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium">
                                                <i class="fas fa-sign-in-alt mr-2"></i>
                                                Unirse al Equipo
                                            </button>
                                        @else
                                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Equipo Privado</h3>
                                            <p class="text-gray-600 mb-6 text-center max-w-md">
                                                Este es un equipo privado. Solo los miembros invitados pueden ver su contenido y canales.
                                            </p>
                                            <div class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-lg">
                                                <i class="fas fa-info-circle mr-2"></i> Contacta a un administrador para solicitar acceso.
                                            </div>
                                        @endif
                                    </div>
                                @elseif(!$isChannelMember)
                                    <!-- Overlay para miembros del equipo que no están en el canal -->
                                    <div class="flex flex-col items-center justify-center h-full py-12">
                                        <div
                                            class="w-16 h-16 bg-gradient-to-br {{ $channel->is_private ? 'from-gray-500 to-gray-700' : 'from-blue-500 to-blue-700' }} rounded-full flex items-center justify-center mb-4">
                                            <i
                                                class="fas {{ $channel->is_private ? 'fa-lock' : 'fa-hashtag' }} text-white text-2xl"></i>
                                        </div>

                                        @if($channel->is_private)
                                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Canal Privado</h3>
                                            <p class="text-gray-600 mb-6 text-center max-w-md">
                                                Este es un canal privado. Solo los miembros invitados por un administrador pueden ver los
                                                mensajes y participar.
                                            </p>
                                            <div class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-lg">
                                                <i class="fas fa-info-circle mr-2"></i> Contacta a un administrador del equipo para
                                                solicitar
                                                acceso.
                                            </div>
                                        @else
                                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Te damos la bienvenida a
                                                #{{ $channel->name }}
                                            </h3>
                                            <p class="text-gray-600 mb-6 text-center max-w-md">
                                                Para ver el historial de mensajes y enviar los tuyos, únete a este canal público.
                                            </p>
                                            <button wire:click="joinChannel({{ $channel->id }})"
                                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                                <i class="fas fa-sign-in-alt mr-2"></i>
                                                Unirse al Canal
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <!-- Lista de mensajes (Solo para miembros) -->
                                    @if($messages && count($messages) > 0)
                                        @foreach($messages as $message)
                                            @php
                                                // Ensure we have the data we need
                                                $userId = $message['user_id'] ?? null;
                                                $userName = $message['user']['name'] ?? 'Unknown';
                                                $content = $message['content'] ?? '';
                                                $createdAt = $message['created_at'] ?? now();
                                            @endphp
                                            <div
                                                class="flex items-start space-x-3 group {{ $userId == Auth::user()->id ? 'justify-end' : '' }}">
                                                @if($userId != Auth::user()->id)
                                                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                        <span class="text-yellow-600 font-medium text-sm">
                                                            {{ substr($userName, 0, 2) }}
                                                        </span>
                                                    </div>
                                                @endif

                                                <div class="max-w-xs lg:max-w-md">
                                                    @if($userId != Auth::user()->id)
                                                        <div class="flex items-center space-x-2 mb-1">
                                                            <span class="text-xs text-gray-500">{{ $userName }}</span>
                                                            <a href="{{ route('teams.chats', $userId) }}" wire:navigate
                                                                class="opacity-0 group-hover:opacity-100 text-yellow-500 hover:text-yellow-600"
                                                                title="Chat con {{ $userName }}">
                                                                <x-lucide-message-circle class="w-3 h-3" />
                                                            </a>
                                                        </div>
                                                    @endif

                                                    <div
                                                        class="{{ $userId == Auth::user()->id ? 'bg-yellow-600 text-white' : 'bg-white text-gray-900 border border-gray-200' }} rounded-lg px-4 py-2 shadow-sm">
                                                        <p class="text-sm">{{ $content }}</p>

                                                        @if(isset($message['files']) && count($message['files']) > 0)
                                                            <div class="mt-2 space-y-1">
                                                                @foreach($message['files'] as $file)
                                                                    <a href="{{ $file['url'] }}" target="_blank"
                                                                        class="flex items-center p-2 rounded-md {{ $userId == Auth::user()->id ? 'bg-yellow-700 hover:bg-yellow-800 text-white' : 'bg-gray-50 hover:bg-gray-100 text-gray-700' }} transition-colors text-xs group">
                                                                        <x-lucide-file class="w-4 h-4 mr-2 opacity-70" />
                                                                        <span class="truncate max-w-[150px]">{{ $file['name'] }}</span>
                                                                        <span class="ml-2 opacity-60">{{ $file['readable_size'] }}</span>
                                                                        <x-lucide-download
                                                                            class="w-3 h-3 ml-auto opacity-0 group-hover:opacity-100 transition-opacity" />
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div
                                                        class="text-xs text-gray-500 mt-1 flex items-center justify-{{ $userId == Auth::user()->id ? 'end' : 'start' }}">
                                                        <span>{{ \Carbon\Carbon::parse($createdAt)->timezone('America/Bogota')->format('g:i A') }}</span>
                                                        @if(\Carbon\Carbon::parse($createdAt)->format('Y-m-d') != now()->format('Y-m-d'))
                                                            <span class="ml-1">{{ \Carbon\Carbon::parse($createdAt)->format('d/m/Y') }}</span>
                                                        @endif

                                                        <!-- Indicador de entrega para mensajes propios -->
                                                        @if($userId == Auth::user()->id)
                                                            <span class="ml-2 text-xs">
                                                                @if(isset($message['is_sender']) && $message['is_sender'])
                                                                    <i class="fas fa-check text-blue-500" title="Entregado"></i>
                                                                @endif
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($userId == Auth::user()->id)
                                                    <div class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                        <span class="text-white font-medium text-sm">
                                                            {{ substr(Auth::user()->name, 0, 2) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <!-- Mensaje de bienvenida al canal -->
                                        <div class="text-center py-12">
                                            @if($channel)
                                                <div
                                                    class="w-16 h-16 bg-gradient-to-br {{ $channel['is_private'] ? 'from-gray-500 to-gray-700' : 'from-blue-500 to-blue-700' }} rounded-full flex items-center justify-center mx-auto mb-4">
                                                    <i
                                                        class="fas {{ $channel['is_private'] ? 'fa-lock' : 'fa-hashtag' }} text-white text-2xl"></i>
                                                </div>
                                                <h3 class="text-xl font-semibold text-gray-900 mb-2">Bienvenido a {{ $channel['name'] }}
                                                </h3>
                                                @if($channel['description'])
                                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4 max-w-md mx-auto">
                                                        <div class="flex items-start space-x-2">
                                                            <i class="fas fa-info-circle text-gray-500 mt-0.5"></i>
                                                            <p class="text-sm text-gray-600 text-left">{{ $channel['description'] }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                                    <i class="fas fa-exclamation-triangle text-gray-400 text-2xl"></i>
                                                </div>
                                                <h3 class="text-xl font-semibold text-gray-900 mb-2">No se encontró el canal</h3>
                                            @endif
                                            <p class="text-sm text-gray-500">Este es el inicio del canal. Los mensajes aparecerán aquí.
                                            </p>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Área de entrada de mensajes -->
                        @php
                            // Verificar si el usuario tiene acceso para enviar mensajes
                            $canSendMessage = false;
                            if ($isMember) {
                                if (!$channel->is_private) {
                                    // Canal público: todos los miembros del equipo pueden enviar mensajes
                                    $canSendMessage = true;
                                } else {
                                    // Canal privado: verificar si está en la tabla pivot
                                    $canSendMessage = $channel->members()->where('user_id', Auth::id())->exists();
                                }
                            }
                        @endphp

                        @if($canSendMessage)
                            <div class="bg-white border-t border-gray-200 px-6 py-4">
                                <div class="max-w-4xl mx-auto">
                                    <form wire:submit.prevent="sendMessage" class="w-full">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <span class="text-yellow-600 font-medium text-sm">
                                                    {{ substr(Auth::user()->name, 0, 2) }}
                                                </span>
                                            </div>


                                            <!-- Componente de Adjuntos -->
                                            <livewire:modules.cloud.components.chat-attachments />

                                            <!-- Emoji Picker -->
                                            <div x-data="{ showEmojiPicker: false }" class="relative">
                                                <button type="button" @click="showEmojiPicker = !showEmojiPicker"
                                                    class="text-gray-500 hover:text-gray-700 transition-colors p-1">
                                                    <x-lucide-smile class="w-5 h-5" />
                                                </button>

                                                <div x-show="showEmojiPicker" @click.away="showEmojiPicker = false" x-transition
                                                    class="absolute bottom-full right-0 mb-2 z-50 shadow-xl rounded-lg overflow-hidden"
                                                    style="display: none;">
                                                    <emoji-picker @emoji-click="
                                                                            $wire.newMessage = ($wire.newMessage || '') + $event.detail.unicode;
                                                                            showEmojiPicker = false;
                                                                        " class="light">
                                                    </emoji-picker>
                                                </div>
                                            </div>

                                            <input type="text" wire:model="newMessage" wire:loading.attr="disabled"
                                                wire:target="sendMessage" placeholder="Escribe un mensaje en {{ $channel->name }}..."
                                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed">
                                            <button type="submit" wire:loading.attr="disabled" wire:target="sendMessage"
                                                class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed relative"
                                                :disabled="!@this.newMessage">
                                                <span wire:loading.remove wire:target="sendMessage">
                                                    <x-lucide-send-horizontal class="w-6 h-6" />
                                                </span>
                                                <span wire:loading wire:target="sendMessage">
                                                    <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                            stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            </button>
                                        </div>
                                        @error('newMessage')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Vista de Tabs -->
                    <div x-data="{ activeTab: 'canales' }" class="flex flex-col h-full">
                        <!-- Header superior -->
                        <div class="bg-white border-b border-gray-200 px-6 py-4">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">{{ $team->name }}</h2>
                                    <p class="text-gray-600 mt-1">Gestión del equipo</p>
                                </div>
                            </div>

                            <!-- Tabs Navigation -->
                            <div class="flex space-x-1 border-b border-gray-200">
                                <button @click="activeTab = 'canales'"
                                    :class="activeTab === 'canales' ? 'border-yellow-600 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
                                    <i class="fas fa-hashtag mr-2"></i>
                                    Canales
                                </button>
                                <button @click="activeTab = 'miembros'"
                                    :class="activeTab === 'miembros' ? 'border-yellow-600 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
                                    <i class="fas fa-user-friends mr-2"></i>
                                    Miembros
                                </button>
                                <button @click="activeTab = 'configuracion'"
                                    :class="activeTab === 'configuracion' ? 'border-yellow-600 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="px-4 py-2 border-b-2 font-medium text-sm transition-colors">
                                    <i class="fas fa-cog mr-2"></i>
                                    Configuración
                                </button>
                            </div>
                        </div>

                        <!-- Tab Content -->
                        <div class="flex-1 overflow-y-auto p-6" style="height: calc(100vh - 160px);">
                            <!-- Tab de Canales -->
                            <div x-show="activeTab === 'canales'" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform translate-x-4"
                                x-transition:enter-end="opacity-100 transform translate-x-0">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900">Canales de Comunicación</h3>
                                    @if($isMember && ($currentUserRole['slug'] ?? '') === 'owner')
                                        <a href="{{ route('teams.channels.create', $team->id) }}"
                                            class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center">
                                            <i class="fas fa-plus mr-2"></i>
                                            Nuevo Canal
                                        </a>
                                    @endif
                                </div>

                                @forelse($channels as $channel)
                                    <div
                                        class="bg-white rounded-lg shadow-sm border border-gray-200 px-2 py-2 mb-2 hover:shadow-sm transition-shadow">
                                        <div class="flex items-center justify-between">
                                            <!-- Info del canal -->
                                            <div class="flex items-start space-x-4 flex-1 ml-2">

                                                <!-- Detalles del canal -->
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center space-x-3">
                                                        <h3 class="text-lg font-semibold text-gray-900">
                                                            {{ $channel['name'] }}
                                                        </h3>
                                                        @if($channel['is_private'])
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                <i class="fas fa-lock mr-1"></i>Privado
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <i class="fas fa-globe mr-1"></i>Público
                                                            </span>
                                                        @endif


                                                        <!-- Estadísticas del canal -->
                                                        <div class="flex items-center space-x-6 text-sm text-gray-500">
                                                            <div class="flex items-center">
                                                                <i class="fas fa-user-friends mr-2"></i>
                                                                <span
                                                                    class="font-medium text-gray-900">{{ $channel['members_count'] ?? 0 }}</span>
                                                                <span class="ml-1">miembros</span>
                                                            </div>
                                                            {{-- <div class="flex items-center">
                                                                <i class="fas fa-comment mr-2"></i>
                                                                <span class="font-medium text-gray-900">{{ $channel['messages_count'] ??
                                                                    0 }}</span>
                                                                <span class="ml-1">mensajes</span>
                                                            </div> --}}
                                                        </div>

                                                        @if($channel['description'])
                                                            <div class="flex items-start space-x-2">
                                                                <i class="fas fa-info-circle text-gray-400 text-xs"></i>
                                                                <p class="text-sm text-gray-600 line-clamp-1" style="-webkit-line-clamp: 1">
                                                                    {{ Str::limit($channel['description'], 80) }}…
                                                                </p>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Información de acceso -->
                                                    @if(!$isMember || !$channel['is_channel_member'])
                                                        <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                                                            @if(!$isMember)
                                                                @if($team->isPublic)
                                                                    <div class="flex items-center text-sm text-blue-600">
                                                                        <i class="fas fa-info-circle mr-2"></i>
                                                                        <span>Únete al equipo para acceder a este canal</span>
                                                                    </div>
                                                                @else
                                                                    <div class="flex items-center text-sm text-gray-600">
                                                                        <i class="fas fa-lock mr-2"></i>
                                                                        <span>Equipo privado - Acceso restringido</span>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                @if($channel['is_private'])
                                                                    <div class="flex items-center text-sm text-orange-600">
                                                                        <i class="fas fa-lock mr-2"></i>
                                                                        <span>Canal privado - Solicita acceso a un administrador del equipo</span>
                                                                    </div>
                                                                @else
                                                                    <div class="flex items-center text-sm text-blue-600">
                                                                        <i class="fas fa-globe mr-2"></i>
                                                                        <span>Canal público - Únete para participar</span>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @endif

                                                </div>
                                            </div>

                                            <!-- Acciones -->
                                            <div class="flex items-center space-x-2 ml-4">
                                                @if($isMember)
                                                    @php
                                                        // Usar la variable is_channel_member que ya se calculó en el componente
                                                        $isChannelMember = $channel['is_channel_member'] ?? false;
                                                    @endphp

                                                    @if(!$isChannelMember)
                                                        @if(!$channel['is_private'])
                                                            <button wire:click="joinChannel({{ $channel['id'] }})"
                                                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition-colors flex items-center text-sm"
                                                                title="Unirse al canal público">
                                                                <i class="fas fa-sign-in-alt mr-2"></i>
                                                                Unirse al Canal
                                                            </button>
                                                        @else
                                                            <div class="text-sm text-gray-500 bg-gray-100 px-3 py-2 rounded-lg">
                                                                <i class="fas fa-lock mr-2"></i>
                                                                Canal Privado
                                                            </div>
                                                        @endif
                                                    @else
                                                        <a href="{{ route('teams.channels.show', [$team->id, $channel['id']]) }}"
                                                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded-lg transition-colors flex items-center text-sm"
                                                            title="Entrar al canal">
                                                            <i class="fas fa-sign-in-alt mr-2"></i>
                                                            Entrar
                                                        </a>

                                                        @if($channel['is_private'])
                                                            <button wire:click="leaveChannel({{ $channel['id'] }})"
                                                                class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                                                title="Abandonar canal">
                                                                <x-lucide-arrow-left-to-line class="w-5 h-5" />


                                                            </button>
                                                        @endif

                                                        @if(($currentUserRole['slug'] ?? '') === 'owner')
                                                            <a href="{{ route('teams.channels.edit', [$team->id, $channel['id']]) }}"
                                                                class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors"
                                                                title="Editar canal">
                                                                <x-lucide-edit class="w-5 h-5" />
                                                            </a>

                                                            <button wire:click="deleteChannel({{ $channel['id'] }})"
                                                                wire:confirm="¿Estás seguro de eliminar el canal '{{ $channel['name'] }}'? Esta acción no se puede deshacer."
                                                                class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                                                title="Eliminar canal">
                                                                <x-fluentui-delete-48-o class="w-5 h-5" />
                                                            </button>
                                                        @endif
                                                    @endif
                                                @else
                                                    @if($team->isPublic && !$channel['is_private'])
                                                        <!-- No es miembro del equipo pero el canal es público -->
                                                        <div class="text-sm text-gray-500 bg-gray-100 px-3 py-2 rounded-lg">
                                                            <i class="fas fa-info-circle mr-2"></i>
                                                            Únete al equipo para acceder
                                                        </div>
                                                    @else
                                                        <!-- No es miembro del equipo y el canal es privado o el equipo es privado -->
                                                        <div class="text-sm text-gray-500 bg-gray-100 px-3 py-2 rounded-lg">
                                                            <i class="fas fa-lock mr-2"></i>
                                                            Acceso restringido
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-hashtag text-gray-400 text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay canales</h3>
                                        <p class="text-gray-500 mb-6">Este equipo aún no tiene canales de comunicación.</p>
                                        @if($isMember && ($currentUserRole['slug'] ?? '') === 'owner')
                                            <a href="{{ route('teams.channels.create', $team->id) }}"
                                                class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors">
                                                <i class="fas fa-plus mr-2"></i>
                                                Crear Primer Canal
                                            </a>
                                        @endif
                                    </div>
                                @endforelse
                            </div>

                            <!-- Tab de Miembros -->
                            <div x-show="activeTab === 'miembros'" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform translate-x-4"
                                x-transition:enter-end="opacity-100 transform translate-x-0" x-data="{ showAddMember: false }">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900">Miembros del Equipo</h3>
                                    @if($isMember && ($currentUserRole['slug'] ?? '') === 'owner')
                                        <button @click="showAddMember = !showAddMember"
                                            class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center">
                                            <i class="fas fa-user-plus mr-2"></i>
                                            Agregar Miembro
                                        </button>
                                    @endif
                                </div>

                                <!-- Formulario para agregar miembro -->
                                <div x-show="showAddMember" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                                    <h4 class="text-md font-semibold text-gray-900 mb-4">Agregar Nuevo Miembro</h4>
                                    <form wire:submit.prevent="addMember" class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Seleccionar Usuario
                                            </label>
                                            <select wire:model="selectedUserId"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                                <option value="">Seleccione un usuario...</option>
                                                @foreach($availableUsers ?? [] as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                                @endforeach
                                            </select>
                                            @error('selectedUserId')
                                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="flex space-x-3">
                                            <button type="submit"
                                                class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                                                <i class="fas fa-check mr-2"></i>
                                                Agregar
                                            </button>
                                            <button type="button" @click="showAddMember = false"
                                                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                                                <i class="fas fa-times mr-2"></i>
                                                Cancelar
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Lista de miembros separados por rol -->
                                @php
                                    $owners = collect($members)->filter(fn($m) => ($m['role_slug'] ?? '') === 'owner');
                                    $regularMembers = collect($members)->filter(fn($m) => ($m['role_slug'] ?? '') === 'member');
                                    $ownerRole = collect($teamRoles)->firstWhere('slug', 'owner');
                                    $memberRole = collect($teamRoles)->firstWhere('slug', 'member');
                                @endphp

                                <!-- Sección de Propietarios -->
                                @if($owners->count() > 0)
                                    <div class="mb-6">
                                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                            <i class="fas fa-crown text-yellow-500 mr-2"></i>
                                            Propietarios ({{ $owners->count() }})
                                        </h4>
                                        <div class="space-y-3">
                                            @foreach($owners as $member)
                                                <div
                                                    class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center space-x-4 flex-1">
                                                            <div
                                                                class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-full flex items-center justify-center">
                                                                <span class="text-white font-medium text-lg">
                                                                    {{ substr($member['name'], 0, 2) }}
                                                                </span>
                                                            </div>
                                                            <div class="flex-1">
                                                                <div class="flex items-center gap-2">
                                                                    <h4 class="font-semibold text-gray-900">{{ $member['name'] }}</h4>
                                                                    <span
                                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                        <i class="fas fa-crown mr-1"></i>
                                                                        {{ $member['role_name'] ?? 'Propietario' }}
                                                                    </span>
                                                                </div>
                                                                <p class="text-sm text-gray-600">{{ $member['email'] }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            @if($member['id'] != Auth::id())
                                                                <a href="{{ route('teams.chats', $member['id']) }}" wire:navigate
                                                                    class="p-2 text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 rounded-lg transition-colors"
                                                                    title="Chat con {{ $member['name'] }}">
                                                                    <x-lucide-message-circle class="w-5 h-5" />
                                                                </a>
                                                            @endif

                                                            @can('manageMembers', $team)
                                                                <!-- Dropdown para cambiar rol -->
                                                                <div x-data="{ open: false }" class="relative">
                                                                    <button @click="open = !open" type="button"
                                                                        class="p-2 text-gray-600 hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                                                                        title="Cambiar rol">
                                                                        <x-clarity-menu-line class="w-6 h-6" />
                                                                    </button>
                                                                    <div x-show="open" @click.away="open = false"
                                                                        x-transition:enter="transition ease-out duration-100"
                                                                        x-transition:enter-start="transform opacity-0 scale-95"
                                                                        x-transition:enter-end="transform opacity-100 scale-100"
                                                                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                                                        <div class="py-1">
                                                                            <div class="px-4 py-2 text-xs text-gray-500 border-b">
                                                                                Cambiar rol a:
                                                                            </div>
                                                                            @foreach($teamRoles as $role)
                                                                                @if($role['id'] != $member['role_id'])
                                                                                    <button type="button"
                                                                                        wire:click="changeRole({{ $member['id'] }}, {{ $role['id'] }})"
                                                                                        @click="open = false" @if($owners->count() <= 1 && $role['slug'] === 'member') disabled
                                                                                        title="No puedes cambiar el rol del único propietario" @endif
                                                                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                                                        <i
                                                                                            class="fas {{ $role['slug'] === 'owner' ? 'fa-crown' : 'fa-user' }} mr-2"></i>
                                                                                        {{ $role['name'] }}
                                                                                    </button>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Botón eliminar (solo si no es el único owner) -->
                                                                @if($owners->count() > 1)
                                                                    <button wire:click="removeMember({{ $member['id'] }})"
                                                                        wire:confirm="¿Estás seguro de eliminar a {{ $member['name'] }} del equipo?"
                                                                        class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                                                        title="Eliminar miembro">
                                                                        <x-clarity-remove-line class="w-6 h-6" />
                                                                    </button>
                                                                @endif
                                                            @endcan

                                                            @if($member['id'] == Auth::id())
                                                                <span
                                                                    class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">
                                                                    Tú
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Sección de Miembros -->
                                @if($regularMembers->count() > 0)
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                            <i class="fas fa-users text-blue-500 mr-2"></i>
                                            Miembros ({{ $regularMembers->count() }})
                                        </h4>
                                        <div class="space-y-3">
                                            @foreach($regularMembers as $member)
                                                <div
                                                    class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center space-x-4 flex-1">
                                                            <div
                                                                class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                                <span class="text-blue-600 font-medium text-lg">
                                                                    {{ substr($member['name'], 0, 2) }}
                                                                </span>
                                                            </div>
                                                            <div class="flex-1">
                                                                <div class="flex items-center gap-2">
                                                                    <h4 class="font-semibold text-gray-900">{{ $member['name'] }}</h4>
                                                                    <span
                                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                        <i class="fas fa-user mr-1"></i>
                                                                        {{ $member['role_name'] ?? 'Miembro' }}
                                                                    </span>
                                                                </div>
                                                                <p class="text-sm text-gray-600">{{ $member['email'] }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            @if($member['id'] != Auth::id())
                                                                <a href="{{ route('teams.chats', $member['id']) }}" wire:navigate
                                                                    class="p-2 text-yellow-600 hover:text-yellow-700 hover:bg-yellow-50 rounded-lg transition-colors"
                                                                    title="Chat con {{ $member['name'] }}">
                                                                    <x-lucide-message-circle class="w-5 h-5" />
                                                                </a>
                                                            @endif

                                                            @can('manageMembers', $team)
                                                                <!-- Dropdown para cambiar rol -->
                                                                <div x-data="{ open: false }" class="relative">
                                                                    <button @click="open = !open" type="button"
                                                                        class="p-2 text-gray-600 hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                                                                        title="Cambiar rol">
                                                                        <x-clarity-menu-line class="w-6 h-6" />
                                                                    </button>
                                                                    <div x-show="open" @click.away="open = false"
                                                                        x-transition:enter="transition ease-out duration-100"
                                                                        x-transition:enter-start="transform opacity-0 scale-95"
                                                                        x-transition:enter-end="transform opacity-100 scale-100"
                                                                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                                                        <div class="py-1">
                                                                            <div class="px-4 py-2 text-xs text-gray-500 border-b">
                                                                                Cambiar rol a:
                                                                            </div>
                                                                            @foreach($teamRoles as $role)
                                                                                @if($role['id'] != $member['role_id'])
                                                                                    <button type="button"
                                                                                        wire:click="changeRole({{ $member['id'] }}, {{ $role['id'] }})"
                                                                                        @click="open = false"
                                                                                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 transition-colors">
                                                                                        <i
                                                                                            class="fas {{ $role['slug'] === 'owner' ? 'fa-crown' : 'fa-user' }} mr-2"></i>
                                                                                        {{ $role['name'] }}
                                                                                    </button>
                                                                                @endif
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Botón eliminar -->
                                                                <button wire:click="removeMember({{ $member['id'] }})"
                                                                    wire:confirm="¿Estás seguro de eliminar a {{ $member['name'] }} del equipo?"
                                                                    class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                                                    title="Eliminar miembro">
                                                                    <x-clarity-remove-line class="w-6 h-6" />
                                                                </button>
                                                            @endcan

                                                            @if($member['id'] == Auth::id())
                                                                <span
                                                                    class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">
                                                                    Tú
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($owners->count() === 0 && $regularMembers->count() === 0)
                                    <div class="text-center py-12">
                                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-users text-gray-400 text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay miembros</h3>
                                        <p class="text-gray-500">Agrega el primer miembro al equipo.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Tab de Configuración -->
                            <div x-show="activeTab === 'configuracion'" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform translate-x-4"
                                x-transition:enter-end="opacity-100 transform translate-x-0">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6">Configuración del Equipo</h3>

                                <form wire:submit.prevent="updateTeam" class="space-y-6">
                                    <!-- Nombre del equipo -->
                                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                        <h4 class="font-semibold text-gray-900 mb-4">Información Básica</h4>
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Nombre del Equipo
                                                </label>
                                                <input type="text" wire:model="teamName" @if(($currentUserRole['slug'] ?? '') !== 'owner') readonly @endif
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500
                                                                                                                                                                                                                                                                                                                                                                        @if(($currentUserRole['slug'] ?? '') !== 'owner') bg-gray-100 cursor-not-allowed @endif"
                                                    placeholder="Nombre del equipo">
                                                @error('teamName')
                                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    Descripción
                                                </label>
                                                <textarea wire:model="teamDescription" rows="3" @if(($currentUserRole['slug'] ?? '') !== 'owner') readonly @endif
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500
                                                                                                                                                                                                                                                                                                                                                                        @if(($currentUserRole['slug'] ?? '') !== 'owner') bg-gray-100 cursor-not-allowed @endif"
                                                    placeholder="Descripción del equipo"></textarea>
                                                @error('teamDescription')
                                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Privacidad -->
                                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                        <h4 class="font-semibold text-gray-900 mb-4">Privacidad del Equipo</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <!-- Opción Público -->
                                            <label class="relative cursor-pointer">
                                                <input type="radio" wire:model="teamIsPublic" value="1"
                                                    @if(($currentUserRole['slug'] ?? '') !== 'owner') disabled @endif
                                                    class="peer sr-only">
                                                <div
                                                    class="p-4 border-2 rounded-lg transition-all peer-checked:border-yellow-600 peer-checked:bg-yellow-50 hover:bg-gray-50
                                                                                                                                                                                                                                                                                                                                                                        @if(($currentUserRole['slug'] ?? '') !== 'owner') opacity-60 cursor-not-allowed @endif">
                                                    <div class="flex items-center space-x-3 mb-2">
                                                        <div
                                                            class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-globe text-green-600"></i>
                                                        </div>
                                                        <div class="flex-1">
                                                            <h5 class="font-semibold text-gray-900">Público</h5>
                                                        </div>
                                                        <div
                                                            class="w-5 h-5 border-2 rounded-full peer-checked:border-yellow-600 peer-checked:bg-yellow-600 flex items-center justify-center">
                                                            <i
                                                                class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                                                        </div>
                                                    </div>
                                                    <p class="text-xs text-gray-600">
                                                        Visible para todos los usuarios de la plataforma
                                                    </p>
                                                    @if(($currentUserRole['slug'] ?? '') !== 'owner')
                                                        <p class="text-xs text-orange-600 mt-1">
                                                            <i class="fas fa-lock mr-1"></i>Solo los owners pueden cambiar esto
                                                        </p>
                                                    @endif
                                                </div>
                                            </label>

                                            <!-- Opción Privado -->
                                            <label class="relative cursor-pointer">
                                                <input type="radio" wire:model="teamIsPublic" value="0"
                                                    @if(($currentUserRole['slug'] ?? '') !== 'owner') disabled @endif
                                                    class="peer sr-only">
                                                <div
                                                    class="p-4 border-2 rounded-lg transition-all peer-checked:border-yellow-600 peer-checked:bg-yellow-50 hover:bg-gray-50
                                                                                                                                                                                                                                                                                                                                                                        @if(($currentUserRole['slug'] ?? '') !== 'owner') opacity-60 cursor-not-allowed @endif">
                                                    <div class="flex items-center space-x-3 mb-2">
                                                        <div
                                                            class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-lock text-gray-600"></i>
                                                        </div>
                                                        <div class="flex-1">
                                                            <h5 class="font-semibold text-gray-900">Privado</h5>
                                                        </div>
                                                        <div
                                                            class="w-5 h-5 border-2 rounded-full peer-checked:border-yellow-600 peer-checked:bg-yellow-600 flex items-center justify-center">
                                                            <i
                                                                class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                                                        </div>
                                                    </div>
                                                    <p class="text-xs text-gray-600">
                                                        Solo visible para los miembros del equipo
                                                    </p>
                                                    @if(($currentUserRole['slug'] ?? '') !== 'owner')
                                                        <p class="text-xs text-orange-600 mt-1">
                                                            <i class="fas fa-lock mr-1"></i>Solo los owners pueden cambiar esto
                                                        </p>
                                                    @endif
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Acciones -->
                                    @if($isMember && ($currentUserRole['slug'] ?? '') === 'owner')
                                        <div class="flex justify-between items-center pt-4">
                                            <button type="submit"
                                                class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                                                <i class="fas fa-save mr-2"></i>
                                                Guardar Cambios
                                            </button>

                                            <button type="button" wire:click="deleteTeam"
                                                wire:confirm="¿Estás seguro de eliminar este equipo? Esta acción no se puede deshacer."
                                                class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                                <i class="fas fa-trash mr-2"></i>
                                                Eliminar Equipo
                                            </button>
                                        </div>
                                    @else
                                        <div class="bg-gray-100 rounded-lg p-4 text-center">
                                            <i class="fas fa-info-circle text-gray-500 text-2xl mb-2"></i>
                                            <p class="text-sm text-gray-600">
                                                Solo los <strong>owners</strong> del equipo pueden modificar la configuración y eliminar
                                                el
                                                equipo.
                                            </p>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        </div>
    @endif

<script>
    document.addEventListener('livewire:init', () => {
        // Auto-scroll al final de los mensajes
        const scrollMessagesToEnd = () => {
            const messagesContainer = document.querySelector('[x-ref="messagesContainer"]');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        };

        // Scroll al final cuando se cargan los mensajes
        setTimeout(scrollMessagesToEnd, 100);

        // Escuchar evento de mensajes cargados
        Livewire.on('messagesLoaded', () => {
            setTimeout(scrollMessagesToEnd, 100);
        });

        // Escuchar evento de mensaje agregado
        Livewire.on('messageAdded', () => {
            setTimeout(scrollMessagesToEnd, 100);
        });

        // Scroll al final cuando se actualiza el componente
        Livewire.hook('component.updated', () => {
            if (window.location.href.includes('/channels/')) {
                setTimeout(scrollMessagesToEnd, 100);
            }
        });
    });
</script>

@if($channel && $team)
    <script>
        document.addEventListener('livewire:initialized', () => {
            if (typeof window.Echo !== 'undefined') {
                const channelName = 'teams.{{ $team->id }}.channels.{{ is_object($channel) ? $channel->id : ($channel['id'] ?? '') }}';

                // Limpiar suscripciones anteriores para evitar duplicados
                if (window.currentEchoChannel) {
                    window.Echo.leave(window.currentEchoChannel);
                }

                window.currentEchoChannel = window.Echo.private(channelName)
                    .listen('.MessageSent', (e) => {
                        // console.log('Message received via Echo:', e);
                        // Emitir evento de Livewire para refrescar mensajes
                        Livewire.dispatch('message-received', e);
                    });
            }
        });
    </script>
@endif