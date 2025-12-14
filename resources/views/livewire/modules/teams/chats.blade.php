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

            <!-- Lista de usuarios y chats con estado local para selección instantánea -->
            <div class="flex-1 overflow-hidden flex flex-col" x-data="{ 
                activeUserId: {{ $selectedUser ? $selectedUser->id : 'null' }} 
            }">
                <!-- Chats recientes -->
                @if(count($chats) > 0)
                    <div class="border-b border-gray-200">
                        <div class="p-3 bg-gray-50">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase">Conversaciones recientes</h3>
                        </div>
                        <div class="overflow-y-auto max-h-64">
                            @foreach($chats as $chat)
                                @if($chat['other_user'])
                                    @php $otherUserId = $chat['other_user']['id']; @endphp
                                    <div @click="activeUserId = {{ $otherUserId }}; $wire.selectUser({{ $otherUserId }})"
                                        class="w-full flex items-center p-3 hover:bg-gray-100 transition-colors border-b border-gray-100 cursor-pointer"
                                        :class="{ 'bg-yellow-50 border-l-4 border-l-yellow-600': activeUserId === {{ $otherUserId }} }">

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
                                    </div>
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
                            <div @click="activeUserId = {{ $user['id'] }}; $wire.selectUser({{ $user['id'] }})"
                                class="w-full flex items-center p-3 hover:bg-gray-100 transition-colors border-b border-gray-100 cursor-pointer"
                                :class="{ 'bg-yellow-50 border-l-4 border-l-yellow-600': activeUserId === {{ $user['id'] }} }">

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
                            </div>
                        @endforeach
                    </div>
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
        <div x-data="{
                optimisticMessages: [],
                isLoadingOld: false,
                previousScrollHeight: 0,
                currentAttachmentCount: 0,
                
                scrollToBottom() {
                    if (this.$refs.messagesContainer) {
                        this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                    }
                },
                
                init() {
                    // Initial load scroll
                    this.$nextTick(() => {
                        this.scrollToBottom();
                        // Delay observer to prevent immediate trigger before scroll completes
                        setTimeout(() => this.initIntersectionObserver(), 500); 
                    });
                    
                    // Maintain scroll position when older messages load
                    Livewire.on('oldMessagesLoaded', () => {
                        this.restoreScrollPosition();
                    });
                    
                    // Standard new message / initial load
                    Livewire.on('messagesLoaded', () => {
                        if (!this.isLoadingOld) {
                             setTimeout(() => this.scrollToBottom(), 100);
                        }
                        this.isLoadingOld = false;
                    });
                    
                    // Listen for attachment updates
                    Livewire.on('attachments-updated', (data) => {
                         let payload = Array.isArray(data) ? data[0] : data;
                         this.currentAttachmentCount = payload?.count || 0;
                    });
                     
                    Livewire.on('messageAdded', (data) => {
                        let payload = Array.isArray(data) ? data[0] : data;
                        let receivedTempId = payload?.tempId;
                        let receivedContent = payload?.content;

                        let removed = false;
                        
                        // Strategy 1: Remove by ID
                        if (receivedTempId) {
                            const originalLength = this.optimisticMessages.length;
                            this.optimisticMessages = this.optimisticMessages.filter(m => m.id !== receivedTempId);
                            if (this.optimisticMessages.length < originalLength) {
                                removed = true;
                            }
                        }
                        
                        // Strategy 2: Remove by Content (Fallback)
                        if (!removed && receivedContent) {
                            const index = this.optimisticMessages.findIndex(m => m.content === receivedContent);
                            if (index !== -1) {
                                this.optimisticMessages.splice(index, 1);
                            }
                        }
                        
                        // Scroll to bottom for new messages
                        setTimeout(() => this.scrollToBottom(), 100);
                    });
                    
                    this.initIntersectionObserver();
                },
                
                initIntersectionObserver() {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting && !this.isLoadingOld && this.$wire.messages.length >= 20) {
                                console.log('Loading more messages...');
                                this.isLoadingOld = true;
                                this.previousScrollHeight = this.$refs.messagesContainer.scrollHeight;
                                this.$wire.loadMore();
                            }
                        });
                    }, { root: this.$refs.messagesContainer, threshold: 0.1 }); 
                    
                    if (this.$refs.sentinel) {
                        observer.observe(this.$refs.sentinel);
                    }
                },
                
                restoreScrollPosition() {
                    this.$nextTick(() => {
                        const newScrollHeight = this.$refs.messagesContainer.scrollHeight;
                        const diff = newScrollHeight - this.previousScrollHeight;
                        if (diff > 0) {
                            this.$refs.messagesContainer.scrollTop = diff;
                        }
                        this.isLoadingOld = false;
                    });
                }
            }" class="flex-1 flex flex-col relative">

            <!-- GLOBAL LOADING STATE (Instantly replaces content) -->
            <div wire:loading.flex wire:target="selectUser" class="absolute inset-0 bg-gray-50 flex flex-col z-50">
                <!-- Skeleton Header -->
                <div class="bg-white border-b border-gray-200 px-6 py-4">
                    <div class="animate-pulse flex items-center space-x-4">
                        <div class="rounded-full bg-gray-200 h-12 w-12"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                            <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                        </div>
                    </div>
                </div>
                <!-- Skeleton Messages -->
                <div class="flex-1 p-6 space-y-6 overflow-hidden">
                    <div class="animate-pulse flex flex-col space-y-4">
                        <div class="flex justify-end">
                            <div class="h-10 bg-gray-200 rounded-lg w-1/3"></div>
                        </div>
                        <div class="flex justify-start">
                            <div class="h-10 bg-gray-200 rounded-lg w-1/2"></div>
                        </div>
                        <div class="flex justify-end">
                            <div class="h-16 bg-gray-200 rounded-lg w-1/4"></div>
                        </div>
                        <div class="flex justify-start">
                            <div class="h-8 bg-gray-200 rounded-lg w-1/3"></div>
                        </div>
                        <div class="flex justify-start">
                            <div class="h-12 bg-gray-200 rounded-lg w-2/3"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACTUAL CONTENT (Hidden while loading) -->
            <div wire:loading.remove wire:target="selectUser" class="flex-1 flex flex-col h-full">
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
                    <div x-ref="messagesContainer" class="flex-1 p-6 overflow-y-auto bg-gray-50 relative">
                        <!-- Sentinel for infinite scroll -->
                        <div x-ref="sentinel" class="h-4 w-full"></div>

                        <!-- Loading Spinner for Infinite Scroll (Top) -->
                        <div x-show="isLoadingOld" class="flex justify-center py-2">
                            <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>

                        <div class="max-w-4xl mx-auto space-y-4">
                            @if(count($messages) > 0)
                                @foreach($messages as $message)
                                    @php
                                        $userId = $message['user_id'] ?? null;
                                        $userName = $message['user']['name'] ?? 'Unknown';
                                        $content = $message['content'] ?? '';
                                        $createdAt = $message['created_at'] ?? now();
                                        $isMe = $userId == Auth::user()->id;
                                    @endphp
                                    <div class="flex items-start space-x-3 {{ $isMe ? 'justify-end' : '' }}">
                                        @if(!$isMe)
                                            <div
                                                class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span class="text-yellow-600 font-medium text-sm">
                                                    {{ substr($userName, 0, 2) }}
                                                </span>
                                            </div>
                                        @endif

                                        <div class="max-w-xs lg:max-w-md">
                                            @if(!$isMe)
                                                <div class="text-xs text-gray-500 mb-1">{{ $userName }}</div>
                                            @endif

                                            <div
                                                class="{{ $isMe ? 'bg-yellow-600 text-white' : 'bg-white text-gray-900 border border-gray-200' }} rounded-lg px-4 py-2 shadow-sm relative group">
                                                <p class="text-sm">{{ $content }}</p>

                                                @if(isset($message['files']) && count($message['files']) > 0)
                                                    <div class="mt-2 space-y-1">
                                                        @foreach($message['files'] as $file)
                                                            <a href="{{ $file['url'] }}" target="_blank"
                                                                class="flex items-center p-2 rounded-md {{ $isMe ? 'bg-yellow-700 hover:bg-yellow-800 text-white' : 'bg-gray-50 hover:bg-gray-100 text-gray-700' }} transition-colors text-xs group-file">
                                                                <x-lucide-file class="w-4 h-4 mr-2 opacity-70" />
                                                                <span class="truncate max-w-[150px]">{{ $file['name'] }}</span>
                                                                <span class="ml-2 opacity-60">{{ $file['readable_size'] }}</span>
                                                                <x-lucide-download
                                                                    class="w-3 h-3 ml-auto opacity-0 group-hover:opacity-100 transition-opacity" />
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <!-- Message Status Icon (for my messages) -->
                                                @if($isMe)
                                                    <div class="flex items-center justify-end space-x-1 mt-1">
                                                        <span class="text-[10px] {{ $isMe ? 'text-yellow-100' : 'text-gray-400' }}">
                                                            {{ \Carbon\Carbon::parse($createdAt)->timezone('America/Bogota')->format('g:i A') }}
                                                        </span>
                                                        <!-- Single Check (Sent by server) -->
                                                        <x-lucide-check class="w-3 h-3 {{ $isMe ? 'text-white' : 'text-blue-500' }}" />
                                                    </div>
                                                @endif
                                            </div>

                                            @if(!$isMe)
                                                <div class="text-xs text-gray-500 -bottom-4 mt-1">
                                                    <span>{{ \Carbon\Carbon::parse($createdAt)->timezone('America/Bogota')->format('g:i A') }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        @if($isMe)
                                            <div
                                                class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span class="text-white font-medium text-sm">
                                                    {{ substr(Auth::user()->name, 0, 2) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                                <!-- Optimistic Messages Loop -->
                                <template x-for="msg in optimisticMessages" :key="msg.id">
                                    <div class="flex items-start space-x-3 justify-end">
                                        <div class="max-w-xs lg:max-w-md">
                                            <div class="bg-yellow-600 text-white rounded-lg px-4 py-2 shadow-sm relative">
                                                <p class="text-sm" x-text="msg.content"></p>

                                                <!-- Optimistic Files placeholder -->
                                                <template x-if="msg.hasAttachments">
                                                    <div class="mt-2 text-xs italic opacity-90 flex items-center gap-1">
                                                        <x-lucide-paperclip class="w-3 h-3" />
                                                        <span x-text="msg.attachmentCount + ' adjunto(s)...'"></span>
                                                    </div>
                                                </template>

                                                <div class="flex items-center justify-end space-x-1 mt-1">
                                                    <span class="text-[10px] text-yellow-100"
                                                        x-text="new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                                                    <!-- Clock Icon (Sending) -->
                                                    <x-lucide-clock class="w-3 h-3 text-yellow-100" />
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-white font-medium text-sm">
                                                {{ substr(Auth::user()->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </template>

                            @else
                                <!-- Mensaje de bienvenida -->
                                <div class="text-center py-12" x-show="optimisticMessages.length === 0">
                                    <div
                                        class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-comments text-white text-2xl"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Inicia una conversación</h3>
                                    <p class="text-sm text-gray-500">Este es el inicio de tu chat con {{ $selectedUser->name }}
                                    </p>
                                </div>

                                <!-- Optimistic Messages (if empty state was showing) -->
                                <template x-for="msg in optimisticMessages" :key="msg.id">
                                    <div class="flex items-start space-x-3 justify-end mt-4">
                                        <div class="max-w-xs lg:max-w-md">
                                            <div class="bg-yellow-600 text-white rounded-lg px-4 py-2 shadow-sm relative">
                                                <p class="text-sm" x-text="msg.content"></p>
                                                <div class="flex items-center justify-end space-x-1 mt-1">
                                                    <span class="text-[10px] text-yellow-100"
                                                        x-text="new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                                                    <x-lucide-clock class="w-3 h-3 text-yellow-100" />
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-white font-medium text-sm">
                                                {{ substr(Auth::user()->name, 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </template>
                            @endif
                        </div>
                    </div>

                    <!-- Área de entrada de mensajes -->
                    <div class="bg-white border-t border-gray-200 px-6 py-4">
                        <div class="max-w-4xl mx-auto">
                            <form @submit.prevent="
                                                                    const content = $wire.newMessage;
                                                                    const count = currentAttachmentCount; 
                                                                    if(!content && count === 0) return;

                                                                    const tempId = 'temp-' + Date.now();

                                                                    // Add to optimistic list
                                                                    optimisticMessages.push({
                                                                        id: tempId,
                                                                        content: content,
                                                                        hasAttachments: count > 0,
                                                                        attachmentCount: count
                                                                    });

                                                                    // Reset count locally
                                                                    currentAttachmentCount = 0;

                                                                    $wire.newMessage = '';
                                                                    $nextTick(() => scrollToBottom());

                                                                    $wire.sendMessage(content, tempId).then(() => {
                                                                        // Success handled by Livewire.on('messageAdded')
                                                                    });
                                                                " class="w-full">
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

                                    <input type="text" wire:model="newMessage"
                                        placeholder="Escribe un mensaje a {{ $selectedUser->name }}..."
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed">
                                    <button type="submit"
                                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed relative"
                                        :class="{ 'opacity-50 cursor-not-allowed': !@this.newMessage }">
                                        <span>
                                            <x-lucide-send-horizontal class="w-6 h-6" />
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