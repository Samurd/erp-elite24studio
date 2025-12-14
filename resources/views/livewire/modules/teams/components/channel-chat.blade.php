<div class="flex flex-col h-full bg-gray-50" x-data="{
        optimisticMessages: [],
        isLoadingOld: false,
        previousScrollHeight: 0,
        tempMessage: '',
        currentAttachmentCount: 0,
        
        scrollToBottom() {
            if (this.$refs.messagesContainer) {
                this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
            }
        },
        
        init() {
            this.$nextTick(() => {
                this.scrollToBottom();
                setTimeout(() => this.initIntersectionObserver(), 500);
            });

            Livewire.on('oldMessagesLoaded', () => this.restoreScrollPosition());
            
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

                 setTimeout(() => this.scrollToBottom(), 100);
            });
            
            // Listen for attachment updates
            Livewire.on('attachments-updated', (data) => {
                 let payload = Array.isArray(data) ? data[0] : data;
                 this.currentAttachmentCount = payload?.count || 0;
            });
            
            this.initIntersectionObserver();
        },
        
        sendMessage() {
            if (!this.tempMessage.trim() && this.currentAttachmentCount === 0) return;
            
            let tempId = 'temp_' + Date.now();
            let content = this.tempMessage;
            let attachmentCount = this.currentAttachmentCount;
            
            // Add optimistic message
            this.optimisticMessages.push({
                id: tempId,
                content: content,
                user_id: {{ Auth::id() }},
                created_at: new Date().toISOString(),
                user: { name: '{{ Auth::user()->name }}' },
                is_optimistic: true,
                hasAttachments: attachmentCount > 0,
                attachmentCount: attachmentCount
            });
            
            this.tempMessage = '';
            // Reset count locally
            this.currentAttachmentCount = 0; 
            
            this.scrollToBottom();
            
            // Send to backend
            $wire.sendMessage(content, tempId);
        },
        
        initIntersectionObserver() {
             const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !this.isLoadingOld && this.$wire.messages.length >= 20) {
                        this.isLoadingOld = true;
                        this.previousScrollHeight = this.$refs.messagesContainer.scrollHeight;
    this.$wire.loadMore();
    }
    });
    }, { root: this.$refs.messagesContainer, threshold: 0.1 });

    if (this.$refs.sentinel) observer.observe(this.$refs.sentinel);
    },

    restoreScrollPosition() {
    this.$nextTick(() => {
    const diff = this.$refs.messagesContainer.scrollHeight - this.previousScrollHeight;
    if (diff > 0) this.$refs.messagesContainer.scrollTop = diff;
    this.isLoadingOld = false;
    });
    }
    }">

    <!-- Header del Canal -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center shadow-sm z-10">
        <div>
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <span class="text-gray-400 mr-2 text-2xl">#</span> {{ $channel->name }}
            </h2>
            <p class="text-sm text-gray-500">{{ $channel->description ?? 'Sin descripción' }}</p>
        </div>
        <div class="flex items-center space-x-2 text-sm text-gray-500">
            <x-lucide-users class="w-4 h-4" />
            <span>
                {{ $channel->is_private
    ? $channel->members()->count()
    : $team->members()->distinct('users.id')->count('users.id') 
                }} miembros
            </span>
        </div>
    </div>

    <!-- Area de Mensajes -->
    <div x-ref="messagesContainer" class="flex-1 p-6 overflow-y-auto space-y-6">
        <!-- Sentinel -->
        <div x-ref="sentinel" class="h-1 w-full"></div>

        <!-- Loading Spinner -->
        <div x-show="isLoadingOld" class="flex justify-center py-4">
            <x-lucide-loader-2 class="w-6 h-6 animate-spin text-gray-400" />
        </div>

        <!-- Mensajes Reales -->
        @foreach($messages as $msg)
            @php
                $isMe = $msg['user_id'] == Auth::id();
            @endphp
            <div class="flex items-start space-x-3 {{ $isMe ? 'justify-end' : '' }}">
                @if(!$isMe)
                    <div
                        class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-700 font-bold shrink-0">
                        {{ substr($msg['user']['name'], 0, 2) }}
                    </div>
                @endif

                <div class="max-w-[70%]">
                    @if(!$isMe)
                        <div class="text-xs text-gray-500 mb-1 px-1">
                            {{ $msg['user']['name'] }}
                        </div>
                    @endif

                    <div
                        class="px-4 py-2 rounded-2xl shadow-sm text-sm 
                                                        {{ $isMe ? 'bg-yellow-500 text-white rounded-tr-none' : 'bg-white text-gray-800 rounded-tl-none border border-gray-100' }}">
                        <p>{{ $msg['content'] }}</p>

                        @if(isset($msg['files']) && count($msg['files']) > 0)
                            <div class="mt-2 space-y-1">
                                @foreach($msg['files'] as $file)
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

                        <div
                            class="flex items-center {{ $isMe ? 'justify-end text-yellow-100' : 'justify-start text-gray-400' }} space-x-1 mt-1 text-[10px]">
                            <span>{{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}</span>
                            @if($isMe)
                                <x-lucide-check class="w-3 h-3 text-white" />
                            @endif
                        </div>
                    </div>
                </div>

                @if($isMe)
                    <div
                        class="w-10 h-10 rounded-full bg-yellow-500 flex items-center justify-center text-white font-bold shrink-0 ml-3">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                @endif
            </div>
        @endforeach

        <!-- Mensajes Optimistas -->
        <template x-for="msg in optimisticMessages" :key="msg.id">
            <div class="flex items-start space-x-3 justify-end opacity-70">
                <div class="max-w-[70%]">
                    <div class="px-4 py-2 rounded-2xl shadow-sm text-sm bg-yellow-500 text-white rounded-tr-none">
                        <p x-text="msg.content"></p>

                        <template x-if="msg.hasAttachments">
                            <div class="mt-2 text-xs italic opacity-90 flex items-center gap-1">
                                <x-lucide-paperclip class="w-3 h-3" />
                                <span x-text="msg.attachmentCount + ' adjunto(s)...'"></span>
                            </div>
                        </template>

                        <div class="flex items-center justify-end space-x-1 mt-1 text-[10px] text-yellow-100">
                            <span
                                x-text="new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                            <x-lucide-clock class="w-3 h-3" />
                        </div>
                    </div>
                </div>
                <div
                    class="w-10 h-10 rounded-full bg-yellow-500 flex items-center justify-center text-white font-bold shrink-0 ml-3">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
            </div>
        </template>
    </div>

    <!-- Input Area -->
    <div class="bg-white border-t border-gray-200 p-4">
        <form @submit.prevent="sendMessage"
            class="flex items-end gap-2 bg-gray-50 p-2 rounded-xl border border-gray-200 focus-within:border-yellow-500 focus-within:ring-1 focus-within:ring-yellow-500 transition-all">
            <!-- Componente de Adjuntos -->
            <livewire:modules.cloud.components.chat-attachments />

            <!-- Emoji Picker -->
            <div x-data="{ showEmojiPicker: false }" class="relative">
                <button type="button" @click="showEmojiPicker = !showEmojiPicker"
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">
                    <x-lucide-smile class="w-5 h-5" />
                </button>

                <div x-show="showEmojiPicker" @click.away="showEmojiPicker = false" x-transition
                    class="absolute bottom-full left-0 mb-2 z-50 shadow-xl rounded-lg overflow-hidden"
                    style="display: none;">
                    <emoji-picker @emoji-click="
                                            tempMessage = (tempMessage || '') + $event.detail.unicode;
                                            showEmojiPicker = false;
                                        " class="light">
                    </emoji-picker>
                </div>
            </div>

            <textarea x-model="tempMessage" @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                class="flex-1 bg-transparent border-none focus:ring-0 resize-none max-h-32 py-2" rows="1"
                placeholder="Escribe un mensaje en #{{ $channel->name }}..." style="min-height: 44px;"></textarea>

            <button type="submit" :disabled="!tempMessage.trim()"
                class="p-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm">
                <x-lucide-send-horizontal class="w-5 h-5" />
            </button>
        </form>
    </div>
</div>