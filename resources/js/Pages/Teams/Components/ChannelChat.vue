<script setup>
import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

// We might need a generic file component or use the one from Cloud module
import EmojiPicker from 'vue3-emoji-picker'; 
import 'vue3-emoji-picker/css';
import { Paperclip, FileText, Download, X } from 'lucide-vue-next';

const props = defineProps({
    channel: Object, // Active channel
    team: Object,
});

const messages = ref([]);
const optimisticMessages = ref([]);
const tempMessage = ref('');
const isLoadingOld = ref(false);
const messagesContainer = ref(null);
const sentinel = ref(null);
const showEmojiPicker = ref(false);
const fileInput = ref(null);
const chatUploads = ref([]); // File objects

const loadMessages = async () => {
    // Initial fetch via API to avoid massive payload on Inertia load if desired, 
    // OR we can pass initial messages via props (which might slow down initial render).
    // The Livewire component loaded 20 initially.
    // Let's assume we fetch them on mount to keep page load fast.
    try {
        const response = await axios.get(route('teams.channels.messages', [props.team.id, props.channel.id]));
        messages.value = response.data;
        scrollToBottom();
    } catch (e) {
        console.error("Error loading messages", e);
    }
};

const loadMore = async () => {
    if (messages.value.length === 0) return;
    isLoadingOld.value = true;
    const oldHeight = messagesContainer.value.scrollHeight;
    
    try {
        const oldestId = messages.value[0].id;
        const response = await axios.get(route('teams.channels.messages', [props.team.id, props.channel.id]), {
            params: { before_id: oldestId }
        });
        
        if (response.data.length > 0) {
            messages.value = [...response.data, ...messages.value];
            nextTick(() => {
                const newHeight = messagesContainer.value.scrollHeight;
                messagesContainer.value.scrollTop = newHeight - oldHeight;
            });
        }
    } catch (e) {
        console.error("Error loading more messages", e);
    } finally {
        isLoadingOld.value = false;
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

const handleFileChange = (e) => {
    const files = Array.from(e.target.files);
    chatUploads.value = [...chatUploads.value, ...files];
    e.target.value = ''; 
};

const removeAttachment = (index) => {
    chatUploads.value.splice(index, 1);
};

const sendMessage = async () => {
    if (!tempMessage.value.trim() && chatUploads.value.length === 0) return;
    
    const content = tempMessage.value;
    const tempId = 'temp_' + Date.now();
    const files = [...chatUploads.value];
    
    // Optimistic update
    optimisticMessages.value.push({
        id: tempId,
        content: content,
        user_id: usePage().props.auth.user.id,
        created_at: new Date().toISOString(),
        user: { name: usePage().props.auth.user.name },
        is_optimistic: true,
        files: files.map(f => ({
             name: f.name,
             readable_size: (f.size / 1024).toFixed(2) + ' KB',
             url: '#'
        })),
    });
    
    tempMessage.value = '';
    chatUploads.value = [];
    scrollToBottom();
    
    try {
        const formData = new FormData();
        formData.append('content', content);
        
        files.forEach((file, index) => {
            formData.append(`files[${index}]`, file);
        });

        const response = await axios.post(route('teams.channels.messages.store', [props.team.id, props.channel.id]), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        // Remove optimistic message
        optimisticMessages.value = optimisticMessages.value.filter(m => m.id !== tempId);
        
        // Add real message from response
        messages.value.push(response.data.message);
        scrollToBottom();
    } catch (e) {
        console.error("Error sending message", e);
        // Remove optimistic message if failed
        optimisticMessages.value = optimisticMessages.value.filter(m => m.id !== tempId);
    }
};

const handleEnter = (e) => {
    if (!e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
};

// Real-time
let echoChannel = null;

onMounted(() => {
    loadMessages();
    
    // Setup intersection observer for infinite scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !isLoadingOld.value && messages.value.length >= 20) {
                loadMore();
            }
        });
    }, { root: messagesContainer.value, threshold: 0.1 });
    
    if (sentinel.value) observer.observe(sentinel.value);

    // Echo
    if (window.Echo) {
        echoChannel = window.Echo.private(`teams.${props.team.id}.channels.${props.channel.id}`)
            .listen('.MessageSent', (e) => {
                const msg = e.message; 
                // Don't add if it's mine (handled optimistically), 
                // BUT we need to replace optimistic with real one to get ID/Timestamp correct?
                // Or just ignore if user_id matches me.
                if (msg.user_id !== usePage().props.auth.user.id) {
                     messages.value.push({
                        id: msg.id,
                        content: msg.content,
                        user_id: msg.user_id,
                        created_at: msg.created_at,
                        user: msg.user,
                        files: msg.files || []
                     });
                     scrollToBottom();
                } else {
                    // It's mine. Remove optimistic, push real?
                    // Or keep optimistic?
                    // Simple approach: Remove matching optimistic (by content?) and push real.
                    // Or just let Vue reactivity handle it if we structured it well.
                    // Let's just push real and filter optimistic.
                     messages.value.push({
                        id: msg.id,
                        content: msg.content,
                        user_id: msg.user_id,
                        created_at: msg.created_at,
                        user: msg.user,
                        files: msg.files || []
                     });
                     optimisticMessages.value = []; // Clear all optimistic for simplicity/safety
                     scrollToBottom();
                }
            });
    }
});

onUnmounted(() => {
    if (echoChannel) echoChannel.stopListening('.MessageSent');
});

</script>

<template>
    <div class="flex flex-col h-full bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center shadow-sm z-10">
            <div class="flex items-center">
                <div v-if="team" class="flex items-center mr-4 pr-4 border-r border-gray-200">
                    <img v-if="team.profile_photo_url" :src="team.profile_photo_url" :alt="team.name" class="w-8 h-8 rounded-md object-cover mr-2" />
                    <div v-else class="w-8 h-8 bg-yellow-100 text-yellow-700 rounded-md flex items-center justify-center font-bold text-xs mr-2">
                        {{ team.name.substring(0, 2).toUpperCase() }}
                    </div>
                    <span class="text-sm font-semibold text-gray-600 hidden md:block">{{ team.name }}</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <span class="text-gray-400 mr-2 text-2xl">#</span> {{ channel.name }}
                    </h2>
                    <p class="text-sm text-gray-500">{{ channel.description || 'Sin descripción' }}</p>
                </div>
            </div>
             <div class="flex items-center space-x-2 text-sm text-gray-500">
                <i class="fas fa-users w-4 h-4"></i>
                <span>{{ channel.members_count }} miembros</span>
            </div>
        </div>

        <!-- Messages Area -->
        <div ref="messagesContainer" class="flex-1 p-6 overflow-y-auto space-y-6">
            <div ref="sentinel" class="h-1 w-full"></div>
            
            <div v-if="isLoadingOld" class="flex justify-center py-4">
                <i class="fas fa-spinner fa-spin text-gray-400"></i>
            </div>
            
            <div v-for="msg in messages" :key="msg.id" 
                class="flex items-start space-x-3" 
                :class="{'justify-end': msg.user_id === $page.props.auth.user.id}">
                
                <div v-if="msg.user_id !== $page.props.auth.user.id" 
                    class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-700 font-bold shrink-0">
                    {{ msg.user.name.substring(0, 2) }}
                </div>
                
                <div class="max-w-[70%]">
                    <div v-if="msg.user_id !== $page.props.auth.user.id" class="text-xs text-gray-500 mb-1 px-1">
                        {{ msg.user.name }}
                    </div>
                    
                    <div class="px-4 py-2 rounded-2xl shadow-sm text-sm"
                        :class="msg.user_id === $page.props.auth.user.id ? 'bg-yellow-500 text-white rounded-tr-none' : 'bg-white text-gray-800 rounded-tl-none border border-gray-100'">
                        <p>{{ msg.content }}</p>
                        
                        <!-- Files (Simplified) -->
                        <div v-if="msg.files && msg.files.length" class="mt-2 space-y-1">
                             <a v-for="file in msg.files" :key="file.id" :href="file.url" target="_blank"
                                class="flex items-center p-2 rounded-md transition-colors text-xs group-file"
                                :class="msg.user_id === $page.props.auth.user.id ? 'bg-yellow-600 hover:bg-yellow-700 text-white' : 'bg-gray-50 hover:bg-gray-100 text-gray-700'">
                                 <FileText class="w-4 h-4 mr-2 opacity-70" />
                                 <span class="truncate max-w-[150px]">{{ file.name }}</span>
                                  <span class="ml-2 opacity-60">{{ file.readable_size }}</span>
                                  <Download class="w-3 h-3 ml-auto opacity-70" />
                            </a>
                        </div>
                        
                        <div class="flex items-center space-x-1 mt-1 text-[10px]"
                             :class="msg.user_id === $page.props.auth.user.id ? 'justify-end text-yellow-100' : 'justify-start text-gray-400'">
                             <span>{{ new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</span>
                             <i v-if="msg.user_id === $page.props.auth.user.id" class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>
                </div>
                
                <div v-if="msg.user_id === $page.props.auth.user.id" 
                    class="w-10 h-10 rounded-full bg-yellow-500 flex items-center justify-center text-white font-bold shrink-0 ml-3">
                    {{ $page.props.auth.user.name.substring(0, 2) }}
                </div>
            </div>
            
            <!-- Optimistic Messages -->
            <div v-for="msg in optimisticMessages" :key="msg.id" class="flex items-start space-x-3 justify-end opacity-70">
                 <!-- Same structure as above for me -->
                 <div class="max-w-[70%]">
                    <div class="px-4 py-2 rounded-2xl shadow-sm text-sm bg-yellow-500 text-white rounded-tr-none">
                        <p>{{ msg.content }}</p>
                        
                        <div v-if="msg.files && msg.files.length" class="mt-2 space-y-1">
                             <div v-for="(file, idx) in msg.files" :key="idx"
                                class="flex items-center p-2 rounded-md bg-yellow-600 text-white text-xs">
                                 <FileText class="w-4 h-4 mr-2 opacity-70" />
                                 <span class="truncate max-w-[150px]">{{ file.name }}</span>
                                 <span class="ml-2 opacity-60">{{ file.readable_size }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-1 mt-1 text-[10px] text-yellow-100">
                             <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                 <div class="w-10 h-10 rounded-full bg-yellow-500 flex items-center justify-center text-white font-bold shrink-0 ml-3">
                    {{ msg.user.name.substring(0, 2) }}
                </div>
            </div>
        </div>
        
        <!-- Input Area -->
        <div class="bg-white border-t border-gray-200 p-4">
             <!-- File Preview -->
             <div v-if="chatUploads.length > 0" class="flex flex-wrap gap-2 mb-2 p-2 bg-gray-50 rounded-lg">
                  <div v-for="(file, idx) in chatUploads" :key="idx" class="flex items-center bg-white border border-gray-200 rounded px-2 py-1 text-xs">
                      <span class="truncate max-w-[150px]">{{ file.name }}</span>
                      <button @click="removeAttachment(idx)" class="ml-2 text-red-500 hover:text-red-700">
                        <X class="w-3 h-3" />
                      </button>
                  </div>
              </div>

            <form @submit.prevent="sendMessage" class="flex items-end gap-2 bg-gray-50 p-2 rounded-xl border border-gray-200 focus-within:border-yellow-500 focus-within:ring-1 focus-within:ring-yellow-500 transition-all">
                <!-- Attachments Button -->
                <div class="relative">
                   <button type="button" @click="$refs.fileInput.click()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg transition-colors p-2">
                        <Paperclip class="w-5 h-5" />
                   </button>
                   <input ref="fileInput" type="file" multiple class="hidden" @change="handleFileChange">
               </div>
                
                <!-- Emoji Picker Toggle -->
                <div class="relative">
                    <button type="button" @click="showEmojiPicker = !showEmojiPicker"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">
                        <i class="far fa-smile w-5 h-5"></i>
                    </button>
                    <div v-if="showEmojiPicker" class="absolute bottom-full left-0 mb-2 z-50">
                        <EmojiPicker @select="e => { tempMessage += e.i; showEmojiPicker = false; }" />
                    </div>
                </div>
                
                <textarea v-model="tempMessage" @keydown.enter="handleEnter"
                    class="flex-1 bg-transparent border-none focus:ring-0 resize-none max-h-32 py-2 min-h-[44px]" rows="1"
                    placeholder="Escribe un mensaje..."></textarea>
                    
                <button type="submit" :disabled="!tempMessage.trim() && chatUploads.length === 0"
                    class="p-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm">
                    <i class="fas fa-paper-plane w-5 h-5"></i>
                </button>
            </form>
        </div>
    </div>
</template>
