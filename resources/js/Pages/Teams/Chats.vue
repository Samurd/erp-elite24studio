<script setup>
import { ref, watch, nextTick, onMounted, computed } from 'vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { 
    SendHorizontal, 
    Smile, 
    Paperclip, 
    MessageSquare, 
    Users, 
    ArrowLeft, 
    Clock, 
    Check, 
    FileText, 
    Download 
} from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    users: Array,
    chats: Array,
    initialSelectedUser: Object,
    initialSelectedChat: Object,
    initialMessages: Array,
});

const selectedUser = ref(props.initialSelectedUser);
const selectedChat = ref(props.initialSelectedChat);
const messages = ref(props.initialMessages || []);

const chatsList = ref(props.chats);
const activeUserId = ref(props.initialSelectedUser?.id || null);

const messagesContainer = ref(null);
const isLoadingOld = ref(false);
const newMessage = ref('');
const showEmojiPicker = ref(false);
const fileInput = ref(null);
const chatUploads = ref([]); // File objects

const currentUser = usePage().props.auth.user;

// Form for sending (mainly for file handling, though we might custom post)
const form = useForm({
    content: '',
    recipient_id: null,
    files: [],
    temp_id: null,
});

// Optimistic UI updates
const optimisticMessages = ref([]);

const selectUser = (userId) => {
    if (activeUserId.value === userId) return;
    
    // Navigate to same route with different param to trigger standard Inertia reload
    // giving us fresh props (messages, selectedChat, etc)
    router.get(route('teams.chats', userId), {}, {
        preserveState: true, // Keep sidebar state? 
        // Actually, we want to replace the main content. 
        // A full visit is easiest to ensure data integrity without manual axios calls.
        preserveScroll: true,
        only: ['initialSelectedUser', 'initialSelectedChat', 'initialMessages'],
        onSuccess: (page) => {
             activeUserId.value = userId;
             selectedUser.value = page.props.initialSelectedUser;
             selectedChat.value = page.props.initialSelectedChat;
             messages.value = page.props.initialMessages || [];
             optimisticMessages.value = [];
             scrollToBottom();
             setupEcho();
        }
    });
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
    // Reset input so same file selection triggers change again if needed
    e.target.value = ''; 
};

const removeAttachment = (index) => {
    chatUploads.value.splice(index, 1);
};

const sendMessage = () => {
    if (!newMessage.value.trim() && chatUploads.value.length === 0) return;

    const content = newMessage.value;
    const tempId = 'temp-' + Date.now();
    const files = [...chatUploads.value]; // Copy for optimistic display

    // Optimistic add
    optimisticMessages.value.push({
        id: tempId,
        content: content,
        type: 'text',
        created_at: new Date().toISOString(),
        user_id: currentUser.id,
        user: currentUser,
        is_sender: true, // Helper
        status: 'sending',
        files: files.map(f => ({
             name: f.name,
             readable_size: (f.size / 1024).toFixed(2) + ' KB', // Simple format
             url: '#' // No URL yet
        })),
        hasAttachments: files.length > 0,
        attachmentCount: files.length
    });

    // Reset inputs
    newMessage.value = '';
    chatUploads.value = [];
    scrollToBottom();

    // Send
    const formData = new FormData();
    formData.append('content', content);
    formData.append('recipient_id', selectedUser.value.id);
    formData.append('temp_id', tempId);
    
    files.forEach((file, index) => {
        formData.append(`files[${index}]`, file);
    });

    axios.post(route('teams.chats.store'), formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    }).then(response => {
        // Remove optimistic, add real (via Echo or manually here?)
        // Best to just mark optimistic as sent or replace it.
        // For simplicity, let's remove optimistic and let Echo handle the addition for consistency,
        // OR add the real message and remove optimistic.
        
        // Since we broadcast toOthers(), we won't get our own message via Echo usually.
        // So we must add it manually.
        
        const realMsg = response.data.message;
        const msgTempId = response.data.temp_id;

        // Remove optimistic
        optimisticMessages.value = optimisticMessages.value.filter(m => m.id !== msgTempId);
        
        // Add real
        messages.value.push(transformMessage(realMsg));
        scrollToBottom();
        
        // Update chat list last message
        refreshChats();

    }).catch(error => {
        console.error("Send error", error);
        // Mark optimistic as failed?
    });
};

// Transform matches controller output format
const transformMessage = (msg) => {
    // Controller returns user and files relations already transformed mostly or raw models?
    // The controller returns a JSON with `message` model. We need to match the shape expected by template.
    // The controller `store` returns `message` model. 
    // The controller `index`/`loadMore` returns transformed array.
    
    // We need to normalize.
    // Let's assume response.data.message is the Eloquent model serialized.
    return {
        id: msg.id,
        content: msg.content,
        type: msg.type,
        created_at: msg.created_at,
        user_id: msg.user_id,
        user: msg.user || currentUser, 
        files: msg.files ? msg.files.map(f => ({
             id: f.id,
             name: f.name,
             url: f.url,
             readable_size: f.readable_size,
             mime_type: f.mime_type
        })) : []
    };
};

const refreshChats = () => {
    // Could reload just chats prop
    router.reload({ only: ['chats'] });
};

// Infinite Scroll
const loadMore = () => {
    if (isLoadingOld.value || messages.value.length === 0) return;
    
    const oldestId = messages.value[0].id;
    isLoadingOld.value = true;
    
    axios.get(route('teams.chats.load-more', selectedChat.value.id), {
        params: { cursor: oldestId }
    }).then(res => {
        const newMessages = res.data.messages;
        if (newMessages.length > 0) {
            messages.value = [...newMessages, ...messages.value];
            // Maintain scroll? implementation detail
        }
        isLoadingOld.value = false;
    });
};

// Echo
let echoChannel = null;

const setupEcho = () => {
    if (echoChannel) {
        window.Echo.leave(echoChannel);
    }
    
    if (selectedChat.value) {
        const channelName = `private-chat.${selectedChat.value.id}`;
        echoChannel = channelName;
        
        window.Echo.private(channelName)
            .listen('.PrivateMessageSent', (e) => {
                if (e.message.user_id !== currentUser.id) {
                     messages.value.push(e.message);
                     scrollToBottom();
                     refreshChats(); // Update sidebar
                }
            });
    }
};

onMounted(() => {
    scrollToBottom();
    setupEcho();
});

watch(() => props.initialSelectedUser, (newVal) => {
    // Sync if updated via props
    if (newVal) {
        selectedUser.value = newVal;
        activeUserId.value = newVal.id;
    }
});

watch(() => props.initialMessages, (newVal) => {
    if (newVal) messages.value = newVal;
});

// Format date helper
const formatTime = (dateStr) => {
    return new Date(dateStr).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
};

const isMe = (userId) => userId === currentUser.id;

</script>

<template>
    <AppLayout title="Chats de Equipo">
        <!-- Navegación superior -->
        <div class="bg-gray-900 text-white px-6 py-3">
            <div class="flex items-center space-x-6">
                <Link :href="route('teams.index')"
                    class="flex items-center space-x-2 text-gray-300 hover:text-white transition-colors">
                    <Users class="w-5 h-5" />
                    <span>Equipos</span>
                </Link>
                <Link :href="route('teams.chats')" class="flex items-center space-x-2 text-white">
                    <MessageSquare class="w-5 h-5" />
                    <span>Chats</span>
                </Link>
            </div>
        </div>

        <!-- Layout estilo Microsoft Teams -->
        <div class="flex bg-gray-50 border-t border-gray-200" style="height: calc(100vh - 115px);"> <!-- Adjusted height for layout -->
            <!-- Sidebar izquierda -->
            <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
                 <!-- Header -->
                <div class="p-4 border-b border-gray-200">
                    <h1 class="text-xl font-bold text-gray-900 flex items-center">
                        <MessageSquare class="mr-2 text-yellow-600 w-6 h-6" />
                        Chats
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Mensajes privados</p>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <!-- Chats recientes -->
                    <div v-if="chatsList.length > 0" class="border-b border-gray-200">
                        <div class="p-3 bg-gray-50">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase">Conversaciones recientes</h3>
                        </div>
                        <div>
                             <div v-for="chat in chatsList" :key="chat.id"
                                @click="selectUser(chat.other_user.id)"
                                class="w-full flex items-center p-3 hover:bg-gray-100 transition-colors border-b border-gray-100 cursor-pointer"
                                :class="{ 'bg-yellow-50 border-l-4 border-l-yellow-600': activeUserId === chat.other_user.id }">
                                
                                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                    <span class="text-yellow-600 font-medium">
                                        {{ chat.other_user.name.substring(0, 2).toUpperCase() }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0 text-left">
                                    <div class="font-medium text-gray-900 truncate">{{ chat.other_user.name }}</div>
                                    <div v-if="chat.last_message" class="text-sm text-gray-500 truncate">
                                        {{ chat.last_message.content }}
                                    </div>
                                    <div v-else class="text-sm text-gray-400 italic">Sin mensajes</div>
                                </div>
                                <span v-if="chat.last_message" class="text-xs text-gray-400 ml-2 whitespace-nowrap">
                                    {{ chat.last_message.created_at }} // Fix format
                                </span>
                             </div>
                        </div>
                    </div>

                    <!-- Lista de usuarios -->
                    <div class="p-3 bg-gray-50 border-b border-gray-200 mt-2">
                         <h3 class="text-xs font-semibold text-gray-500 uppercase">Todos los usuarios</h3>
                    </div>
                    <div>
                         <div v-for="user in users" :key="user.id"
                            @click="selectUser(user.id)"
                            class="w-full flex items-center p-3 hover:bg-gray-100 transition-colors border-b border-gray-100 cursor-pointer"
                            :class="{ 'bg-yellow-50 border-l-4 border-l-yellow-600': activeUserId === user.id }">
                            
                             <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-blue-600 font-medium text-sm">
                                    {{ user.name.substring(0, 2).toUpperCase() }}
                                </span>
                            </div>
                            <div class="flex-1 text-left">
                                <div class="font-medium text-gray-900">{{ user.name }}</div>
                                <div class="text-sm text-gray-500">{{ user.email }}</div>
                            </div>
                            <MessageSquare class="w-4 h-4 text-gray-400" />
                         </div>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="flex-1 flex flex-col h-full bg-gray-50 relative">
                <div v-if="selectedUser" class="flex flex-col h-full">
                    <!-- Header Chat -->
                    <div class="bg-white border-b border-gray-200 px-6 py-4 flex-shrink-0">
                        <div class="flex items-center space-x-4">
                             <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-full flex items-center justify-center">
                                <span class="text-white font-medium text-lg">
                                    {{ selectedUser.name.substring(0, 2).toUpperCase() }}
                                </span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">{{ selectedUser.name }}</h2>
                                <p class="text-sm text-gray-600">{{ selectedUser.email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Mensajes -->
                    <div ref="messagesContainer" class="flex-1 p-6 overflow-y-auto space-y-4">
                        <!-- Boton cargar mas -->
                        <div v-if="!isLoadingOld && messages.length >= 20" class="flex justify-center">
                            <button @click="loadMore" class="text-xs text-gray-500 hover:text-gray-700">
                                Cargar mensajes anteriores
                            </button>
                        </div>
                        <div v-if="isLoadingOld" class="flex justify-center">
                             <span class="loading loading-spinner loading-xs text-gray-400"></span>
                        </div>

                        <!-- Lista Mensajes -->
                        <template v-if="messages.length > 0">
                             <div v-for="msg in messages" :key="msg.id" 
                                class="flex items-start space-x-3" 
                                :class="{ 'justify-end': isMe(msg.user_id) }">
                                
                                <div v-if="!isMe(msg.user_id)" class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                     <span class="text-yellow-600 font-medium text-sm">
                                         {{ msg.user.name.substring(0, 2).toUpperCase() }}
                                     </span>
                                </div>

                                <div class="max-w-xs lg:max-w-md">
                                     <div v-if="!isMe(msg.user_id)" class="text-xs text-gray-500 mb-1">{{ msg.user.name }}</div>
                                     
                                     <div class="rounded-lg px-4 py-2 shadow-sm relative group"
                                        :class="isMe(msg.user_id) ? 'bg-yellow-600 text-white' : 'bg-white text-gray-900 border border-gray-200'">
                                        
                                        <p class="text-sm pb-1">{{ msg.content }}</p>

                                        <!-- Files -->
                                        <div v-if="msg.files && msg.files.length > 0" class="mt-2 space-y-1">
                                            <a v-for="file in msg.files" :key="file.id" :href="file.url" target="_blank"
                                                class="flex items-center p-2 rounded-md transition-colors text-xs group-file"
                                                :class="isMe(msg.user_id) ? 'bg-yellow-700 hover:bg-yellow-800 text-white' : 'bg-gray-50 hover:bg-gray-100 text-gray-700'">
                                                 <FileText class="w-4 h-4 mr-2 opacity-70" />
                                                 <span class="truncate max-w-[150px]">{{ file.name }}</span>
                                                  <span class="ml-2 opacity-60">{{ file.readable_size }}</span>
                                                  <Download class="w-3 h-3 ml-auto opacity-70" />
                                            </a>
                                        </div>

                                        <!-- Meta -->
                                        <div class="flex items-center justify-end space-x-1 mt-1">
                                            <span class="text-[10px]" :class="isMe(msg.user_id) ? 'text-yellow-100' : 'text-gray-400'">
                                                {{ formatTime(msg.created_at) }}
                                            </span>
                                            <Check v-if="isMe(msg.user_id)" class="w-3 h-3 text-white" />
                                        </div>
                                     </div>
                                </div>

                                <div v-if="isMe(msg.user_id)" class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center flex-shrink-0">
                                     <span class="text-white font-medium text-sm">
                                         {{ currentUser.name.substring(0, 2).toUpperCase() }}
                                     </span>
                                </div>
                             </div>
                        </template>
                        
                        <!-- Empty State -->
                        <div v-else-if="optimisticMessages.length === 0" class="text-center py-12">
                             <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <MessageSquare class="text-white w-8 h-8" />
                             </div>
                             <h3 class="text-xl font-semibold text-gray-900 mb-2">Inicia una conversación</h3>
                             <p class="text-sm text-gray-500">Este es el inicio de tu chat con {{ selectedUser.name }}</p>
                        </div>

                        <!-- Optimistic Messages -->
                        <div v-for="msg in optimisticMessages" :key="msg.id" class="flex items-start space-x-3 justify-end opacity-70">
                             <div class="max-w-xs lg:max-w-md">
                                  <div class="bg-yellow-600 text-white rounded-lg px-4 py-2 shadow-sm relative">
                                        <p class="text-sm pb-1">{{ msg.content }}</p>
                                        <div v-if="msg.hasAttachments" class="mt-2 text-xs italic opacity-90 flex items-center gap-1">
                                            <Paperclip class="w-3 h-3" />
                                            <span>{{ msg.attachmentCount }} adjunto(s)...</span>
                                        </div>
                                        <div class="flex items-center justify-end space-x-1 mt-1">
                                            <span class="text-[10px] text-yellow-100">{{ formatTime(new Date()) }}</span>
                                            <Clock class="w-3 h-3 text-yellow-100" />
                                        </div>
                                  </div>
                             </div>
                              <div class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center flex-shrink-0">
                                     <span class="text-white font-medium text-sm">
                                         {{ currentUser.name.substring(0, 2).toUpperCase() }}
                                     </span>
                                </div>
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="bg-white border-t border-gray-200 px-6 py-4 flex-shrink-0">
                         <div class="max-w-4xl mx-auto w-full">
                              <!-- File Preview -->
                              <div v-if="chatUploads.length > 0" class="flex flex-wrap gap-2 mb-2 p-2 bg-gray-50 rounded-lg">
                                  <div v-for="(file, idx) in chatUploads" :key="idx" class="flex items-center bg-white border border-gray-200 rounded px-2 py-1 text-xs">
                                      <span class="truncate max-w-[150px]">{{ file.name }}</span>
                                      <button @click="removeAttachment(idx)" class="ml-2 text-red-500 hover:text-red-700">×</button>
                                  </div>
                              </div>

                              <form @submit.prevent="sendMessage" class="flex items-center space-x-3">
                                   <!-- Attachments -->
                                   <div class="relative">
                                       <button type="button" @click="$refs.fileInput.click()" class="text-gray-500 hover:text-gray-700 p-1">
                                            <Paperclip class="w-5 h-5" />
                                       </button>
                                       <input ref="fileInput" type="file" multiple class="hidden" @change="handleFileChange">
                                   </div>

                                   <!-- Input -->
                                   <input v-model="newMessage" type="text" 
                                    :placeholder="`Escribe un mensaje a ${selectedUser.name}...`"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                                   
                                   <!-- Send -->
                                   <button type="submit" 
                                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50 flex items-center justify-center p-2 h-10 w-12"
                                    :disabled="!newMessage && chatUploads.length === 0">
                                        <SendHorizontal class="w-5 h-5" />
                                   </button>
                              </form>
                         </div>
                    </div>
                </div>

                <!-- No User Selected -->
                <div v-else class="flex-1 flex items-center justify-center">
                      <div class="text-center">
                            <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <MessageSquare class="text-yellow-600 w-12 h-12" />
                            </div>
                            <h3 class="text-2xl font-semibold text-gray-900 mb-2">Selecciona un usuario</h3>
                            <p class="text-gray-500">Elige un usuario de la lista para iniciar una conversación</p>
                      </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
