<div>
    <!-- Navegación superior -->
    <div class="bg-gray-900 text-white px-6 py-3">
        <div class="flex items-center space-x-6">
            <a href="{{ route('teams.index') }}"
                class="flex items-center space-x-2 text-gray-300 hover:text-white transition-colors">
                <i class="fas fa-users"></i>
                <span>Equipos</span>
            </a>
            <a href="{{ route('teams.chats') }}" class="flex items-center space-x-2 text-white">
                <i class="fas fa-comments"></i>
                <span>Chats</span>
            </a>
        </div>
    </div>

    <!-- Layout estilo Microsoft Teams -->
    <div class="flex bg-gray-50" style="height: calc(100vh - 48px);">
        <!-- Sidebar izquierda - Lista de usuarios y chats -->
        <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-comments mr-2 text-yellow-600"></i>
                    Chats
                </h1>
                <p class="text-sm text-gray-600 mt-1">Mensajes privados</p>
            </div>

            <!-- Chats recientes -->
            @if(count($chats) > 0)
                <div class="border-b border-gray-200">
                    <div class="p-3 bg-gray-50">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase">Conversaciones recientes</h3>
                    </div>
                    <div class="overflow-y-auto max-h-64">
                        @foreach($chats as $chat)
                            @if($chat['other_user'])
                                <a href="{{ route('teams.chats', $chat['other_user']['id']) }}" wire:navigate
                                    class="w-full flex items-center p-3 hover:bg-gray-100 transition-colors border-b border-gray-100 {{ $selectedChat && $selectedChat->id == $chat['id'] ? 'bg-yellow-50 border-l-4 border-l-yellow-600' : '' }}">
                                    <div
                                        class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                        <span class="text-yellow-600 font-medium">
                                            {{ substr($chat['other_user']['name'], 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0 text-left">
                                        <div class="font-medium text-gray-900 truncate">{{ $chat['other_user']['name'] }}</div>
                                        @if($chat['last_message'])
                                            <div class="text-sm text-gray-500 truncate">{{ $chat['last_message']['content'] }}</div>
                                        @else
                                            <div class="text-sm text-gray-400 italic">Sin mensajes</div>
                                        @endif
                                    </div>
                                    @if($chat['last_message'])
                                        <span class="text-xs text-gray-400 ml-2">{{ $chat['last_message']['created_at'] }}</span>
                                    @endif
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Lista de usuarios -->
            <div class="flex-1 overflow-hidden flex flex-col">
                <div class="p-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase">Todos los usuarios</h3>
                </div>
                <div class="flex-1 overflow-y-auto">
                    @foreach($users as $user)
                        <a href="{{ route('teams.chats', $user['id']) }}" wire:navigate
                            class="w-full flex items-center p-3 hover:bg-gray-100 transition-colors border-b border-gray-100 {{ $selectedUser && $selectedUser->id == $user['id'] ? 'bg-yellow-50 border-l-4 border-l-yellow-600' : '' }}">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-blue-600 font-medium text-sm">
                                    {{ substr($user['name'], 0, 2) }}
                                </span>
                            </div>
                            <div class="flex-1 text-left">
                                <div class="font-medium text-gray-900">{{ $user['name'] }}</div>
                                <div class="text-sm text-gray-500">{{ $user['email'] }}</div>
                            </div>
                            <i class="fas fa-comment text-gray-400"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Botón volver -->
            <div class="p-4 border-t border-gray-200">
                <a href="{{ route('teams.index') }}"
                    class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver a equipos</span>
                </a>
            </div>
        </div>

        <!-- Contenido principal - Área de chat -->
        <div class="flex-1 flex flex-col">
            @if($selectedUser && $selectedChat)
                <!-- Header del chat -->
                <div class="bg-white border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-full flex items-center justify-center">
                            <span class="text-white font-medium text-lg">
                                {{ substr($selectedUser->name, 0, 2) }}
                            </span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $selectedUser->name }}</h2>
                            <p class="text-sm text-gray-600">{{ $selectedUser->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Área de mensajes -->
                <div x-data x-ref="messagesContainer" @messages-loaded.window="
                                                                                     await $nextTick();
                                                                                     $refs.messagesContainer.scrollTop = $refs.messagesContainer.scrollHeight;
                                                                                 " @message-added.window="
                                                                                     await $nextTick();
                                                                                     $refs.messagesContainer.scrollTop = $refs.messagesContainer.scrollHeight;
                                                                                 "
                    class="flex-1 p-6 overflow-y-auto bg-gray-50">
                    <div class="max-w-4xl mx-auto space-y-4">
                        @if(count($messages) > 0)
                            @foreach($messages as $message)
                                @php
                                    $userId = $message['user_id'] ?? null;
                                    $userName = $message['user']['name'] ?? 'Unknown';
                                    $content = $message['content'] ?? '';
                                    $createdAt = $message['created_at'] ?? now();
                                @endphp
                                <div class="flex items-start space-x-3 {{ $userId == Auth::user()->id ? 'justify-end' : '' }}">
                                    @if($userId != Auth::user()->id)
                                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-yellow-600 font-medium text-sm">
                                                {{ substr($userName, 0, 2) }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="max-w-xs lg:max-w-md">
                                        @if($userId != Auth::user()->id)
                                            <div class="text-xs text-gray-500 mb-1">{{ $userName }}</div>
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
                            <!-- Mensaje de bienvenida -->
                            <div class="text-center py-12">
                                <div
                                    class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-comments text-white text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">Inicia una conversación</h3>
                                <p class="text-sm text-gray-500">Este es el inicio de tu chat con {{ $selectedUser->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Área de entrada de mensajes -->
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
                                    wire:target="sendMessage"
                                    placeholder="Escribe un mensaje a {{ $selectedUser->name }}..."
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
            @else
                <!-- Vista cuando no hay chat seleccionado -->
                <div class="flex-1 flex items-center justify-center bg-gray-50">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comments text-yellow-600 text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900 mb-2">Selecciona un usuario</h3>
                        <p class="text-gray-500">Elige un usuario de la lista para iniciar una conversación</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

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
    });
</script>

@if($selectedChat)
    <script>
        document.addEventListener('livewire:initialized', () => {
            if (typeof window.Echo !== 'undefined') {
                const chatId = {{ $selectedChat->id }};
                const channelName = `private-chat.${chatId}`;

                // Limpiar suscripciones anteriores
                if (window.currentChatEchoChannel) {
                    window.Echo.leave(window.currentChatEchoChannel);
                }

                window.currentChatEchoChannel = window.Echo.private(channelName)
                    .listen('.PrivateMessageSent', (e) => {
                        // console.log('Private message received via Echo:', e);
                        Livewire.dispatch('message-received', e);
                    });

                // console.log('Subscribed to private chat channel:', channelName);
            }
        });
    </script>
@endif