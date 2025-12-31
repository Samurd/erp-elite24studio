<script setup>
import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

// We might need a generic file component or use the one from Cloud module
import EmojiPicker from 'vue3-emoji-picker'; 
import 'vue3-emoji-picker/css';
import { Paperclip, FileText, Download, X, Clock, Check, Smile, Plus } from 'lucide-vue-next';

const props = defineProps({
    channel: Object, // Active channel
    team: Object,
});

const emit = defineEmits(['back']);

const messages = ref([]);
const optimisticMessages = ref([]);
const tempMessage = ref('');
const isLoadingOld = ref(false);
const isLoadingMessages = ref(true);
const allLoaded = ref(false);
const messagesContainer = ref(null);
const topSentry = ref(null);
const showEmojiPicker = ref(false);
const fileInput = ref(null);
const chatUploads = ref([]); // File objects
const replyingToId = ref(null); // ID of the thread currently being replied to
const hoveredMessageId = ref(null);
const showReactionPickerId = ref(null);
const showFullReactionPickerId = ref(null);

// Common reactions
const REACTION_EMOJIS = ['👍', '❤️', '😂', '😮', '😢', '😡'];

// Computed to find the message we are replying to
import { computed } from 'vue';
const replyingToMessage = computed(() => {
    if (!replyingToId.value) return null;
    return messages.value.find(m => m.id === replyingToId.value);
});

watch(() => props.channel, (newChannel, oldChannel) => {
    if (newChannel && newChannel.id !== oldChannel?.id) {
        messages.value = [];
        allLoaded.value = false;
        loadMessages();
        // Re-init Echo listener
        if (echoChannel) echoChannel.stopListening('.MessageSent');
        setupEcho();
    }
});

const setupEcho = () => {
     if (window.Echo) {
        echoChannel = window.Echo.private(`teams.${props.team.id}.channels.${props.channel.id}`)
            .listen('.MessageSent', (e) => {
                const msg = e.message; 
                
                // If it's a reply (check parent_id), find parent and push
                if (msg.parent_id) {
                     const parent = messages.value.find(m => m.id === msg.parent_id);
                     if (parent) {
                         if (!parent.replies) parent.replies = [];
                         // Avoid dups if optimistic already added
                         if (!parent.replies.find(r => r.id === msg.id)) {
                             parent.replies.push({
                                id: msg.id,
                                content: msg.content,
                                user_id: msg.user_id,
                                created_at: msg.created_at,
                                user: msg.user,
                                files: msg.files || []
                             });
                         }
                     }
                     return;
                }

                // If root message
                if (msg.user_id !== usePage().props.auth.user.id) {
                     messages.value.push({
                        id: msg.id,
                        content: msg.content,
                        user_id: msg.user_id,
                        created_at: msg.created_at,
                        user: msg.user,
                        files: msg.files || [],
                        replies: []
                     });
                     scrollToBottom();
                } else {
                     // Check if we have optimistic pending
                     // For root messages, we usually just push or replace.
                     // Simple approach:
                     messages.value.push({
                        id: msg.id,
                        content: msg.content,
                        user_id: msg.user_id,
                        created_at: msg.created_at,
                        user: msg.user,
                        files: msg.files || [],
                        replies: []
                     });
                     optimisticMessages.value = []; 
                     scrollToBottom();
                }
            })
            .listen('.MessageReactionUpdated', (e) => {
                 // Find relevant message (root or reply)
                 const updateReactions = (list) => {
                     for (let m of list) {
                         if (m.id === e.messageId) {
                             m.reactions = e.reactions;
                             return true;
                         }
                         if (m.replies && updateReactions(m.replies)) return true;
                     }
                     return false;
                 };
                 updateReactions(messages.value);
            });
    }
}

const loadMessages = async () => {
    // Initial fetch via API to avoid massive payload on Inertia load if desired, 
    // OR we can pass initial messages via props (which might slow down initial render).
    // The Livewire component loaded 20 initially.
    // Let's assume we fetch them on mount to keep page load fast.
    isLoadingMessages.value = true;
    try {
        const response = await axios.get(route('teams.channels.messages', [props.team.id, props.channel.id]));
        messages.value = response.data;
        scrollToBottom();
    } catch (e) {
        // console.error("Error loading messages", e);
    } finally {
        isLoadingMessages.value = false;
        
        // Initial setup for infinite scroll
        nextTick(() => {
           setupInfiniteScroll();
        });
    }
};

const loadMore = async () => {
    if (isLoadingOld.value || messages.value.length === 0 || allLoaded.value) return;

    // Capture current height before loading to restore position
    const container = messagesContainer.value;
    const previousHeight = container ? container.scrollHeight : 0;

    isLoadingOld.value = true;
    
    try {
        const oldestId = messages.value[0].id;
        const response = await axios.get(route('teams.channels.messages', [props.team.id, props.channel.id]), {
            params: { before_id: oldestId }
        });
        
        const newMessages = response.data;

        if (newMessages.length < 20) {
            allLoaded.value = true;
        }

        if (newMessages.length > 0) {
            messages.value = [...newMessages, ...messages.value];
            nextTick(() => {
                if (container) {
                    const newHeight = container.scrollHeight;
                    container.scrollTop = newHeight - previousHeight;
                }
            });
        }
    } catch (e) {
        console.error("Error loading more messages", e);
    } finally {
        isLoadingOld.value = false;
    }
};

let observer = null;
const setupInfiniteScroll = () => {
    if (observer) observer.disconnect();
    
    observer = new IntersectionObserver((entries) => {
        const target = entries[0];
        if (target.isIntersecting && !isLoadingOld.value && messages.value.length >= 20) {
            loadMore();
        }
    }, {
        root: messagesContainer.value,
        rootMargin: '20px 0px 0px 0px',
        threshold: 0
    });

    if (topSentry.value) {
        observer.observe(topSentry.value);
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
    // Determine if it's a reply or new conversation based on state
    const parentId = replyingToId.value;
    const isReply = !!parentId;
    const content = tempMessage.value;
    const uploads = chatUploads.value;

    if (!content.trim() && uploads.length === 0) return;
    
    const tempId = 'temp_' + Date.now();
    const files = [...uploads];
    
    const optimisticMsg = {
        id: tempId,
        content: content,
        user_id: usePage().props.auth.user.id,
        created_at: new Date().toISOString(),
        user: { name: usePage().props.auth.user.name, profile_photo_url: usePage().props.auth.user.profile_photo_url },
        is_optimistic: true,
        files: files.map(f => ({
             name: f.name,
             readable_size: (f.size / 1024).toFixed(2) + ' KB',
             url: '#'
        })),
        replies: []
    };

    if (isReply) {
        // Find parent and add optimistic reply
        const parent = messages.value.find(m => m.id === parentId);
        if (parent) {
            if (!parent.replies) parent.replies = [];
            parent.replies.push(optimisticMsg);
        }
        
    } else {
        // New conversation
        optimisticMessages.value.push(optimisticMsg);
        scrollToBottom();
    }
    
    // Clear Input immediately
    tempMessage.value = '';
    chatUploads.value = [];
    // If it was a reply, we might keep replying open or close it? usually close it.
    if (isReply) {
        replyingToId.value = null; // Close reply context
    }
    
    try {
        const formData = new FormData();
        formData.append('content', content);
        if (parentId) formData.append('parent_id', parentId);
        
        files.forEach((file, index) => {
            formData.append(`files[${index}]`, file);
        });

        const response = await axios.post(route('teams.channels.messages.store', [props.team.id, props.channel.id]), formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (isReply) {
             const parent = messages.value.find(m => m.id === parentId);
             if (parent) {
                 // Replace optimistic with real
                 parent.replies = parent.replies.filter(r => r.id !== tempId);
                 parent.replies.push(response.data.message);
             }
        } else {
            // Remove optimistic message
            optimisticMessages.value = optimisticMessages.value.filter(m => m.id !== tempId);
            // Add real message from response
            messages.value.push(response.data.message);
            scrollToBottom();
        }

    } catch (e) {
        console.error("Error sending message", e);
        // Remove optimistic message if failed
        if (isReply) {
            const parent = messages.value.find(m => m.id === parentId);
            if (parent) {
                 parent.replies = parent.replies.filter(r => r.id !== tempId);
            }
        } else {
             optimisticMessages.value = optimisticMessages.value.filter(m => m.id !== tempId);
        }
    }
};

const onSelectEmoji = (msgId, emojiData) => {
    toggleReaction(msgId, emojiData.i); // .i contains the emoji character usually? Check vue3-emoji-picker docs. usually 'i' or 'native'.
    // vue3-emoji-picker event object has { i: "😀", n: ["grinning"], ... }
    showFullReactionPickerId.value = null;
};

const toggleReaction = async (msgId, emoji) => {
    // Optimistic Update
    const findAndUpdate = (list) => {
        for (let m of list) {
            if (m.id === msgId) {
                // Initialize if undefined
                if (!m.reactions) m.reactions = [];
                
                // Check if user already reacted with THIS emoji
                const existingGroup = m.reactions.find(r => r.emoji === emoji);
                const userReactedToEmoji = existingGroup?.user_reacted;
                
                // Remove my reaction from ALL groups first (enforce single reaction)
                m.reactions.forEach(g => {
                    if (g.user_reacted) {
                        g.count--;
                        g.user_reacted = false;
                    }
                });
                
                // Clean up empty groups
                m.reactions = m.reactions.filter(g => g.count > 0);
                
                // If it was a TOGGLE OFF (same emoji), we are done.
                if (userReactedToEmoji) {
                     // Done (removed above)
                } else {
                    // It was a TOGGLE ON or SWITCH. Add to new group.
                    let newGroup = m.reactions.find(r => r.emoji === emoji);
                    if (newGroup) {
                        newGroup.count++;
                        newGroup.user_reacted = true;
                    } else {
                        m.reactions.push({ emoji, count: 1, user_reacted: true });
                    }
                }
                
                return true;
            }
            if (m.replies && findAndUpdate(m.replies)) return true;
        }
        return false;
    };
    
    findAndUpdate(messages.value);
    showReactionPickerId.value = null;
    showFullReactionPickerId.value = null;

    try {
        await axios.post(route('teams.channels.messages.react', [props.team.id, props.channel.id, msgId]), {
            emoji
        });
    } catch (e) {
        console.error("Error reacting", e);
    }
};

// Ref for the main textarea
const mainInput = ref(null);

const startReply = (msgId) => {
    replyingToId.value = msgId;
    nextTick(() => {
        if (mainInput.value) mainInput.value.focus();
    });
};

const cancelReply = () => {
    replyingToId.value = null;
    tempMessage.value = ''; // Optional: clear if they cancel? Or keep draft? Teams clears contexts.
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
    
    setupInfiniteScroll();
    
    // Check initial load for allLoaded status
    // Wait for loadMessages to finish? setupInfiniteScroll is called there.
    setupEcho();
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
                 <button @click="$emit('back')" class="mr-4 text-gray-500 hover:text-gray-700 hover:bg-gray-100 p-2 rounded-full transition-colors md:hidden">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <button @click="$emit('back')" class="mr-4 text-gray-500 hover:text-gray-700 hover:bg-gray-100 p-2 rounded-full transition-colors hidden md:block">
                    <i class="fas fa-arrow-left"></i>
                </button>
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
            <!-- Sentry / Loading Skeletons -->
            <div v-show="!allLoaded" ref="topSentry" class="w-full transition-all duration-300">
                <div v-if="isLoadingOld" class="py-6 px-4 space-y-4">
                    <!-- Skeletons estilo WhatsApp -->
                    <div class="flex items-start space-x-3 opacity-60">
                    <div class="rounded-full bg-gray-200 h-8 w-8 animate-pulse"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-10 bg-gray-200 rounded-2xl rounded-tl-none w-1/3 animate-pulse"></div>
                    </div>
                </div>
                <div class="flex items-start space-x-3 justify-end opacity-60">
                        <div class="flex-1 flex justify-end">
                        <div class="h-10 bg-gray-200 rounded-2xl rounded-tr-none w-1/4 animate-pulse"></div>
                    </div>
                        <div class="rounded-full bg-gray-200 h-8 w-8 animate-pulse"></div>
                </div>
                </div>
                <div v-else class="h-1 w-full"></div>
            </div>
            
            <div v-if="isLoadingMessages" class="space-y-6">
                <div v-for="i in 3" :key="i" class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gray-200 animate-pulse shrink-0"></div>
                    <div class="space-y-2 flex-1">
                        <div class="flex items-center space-x-2">
                           <div class="h-3 bg-gray-200 rounded w-20 animate-pulse"></div>
                           <div class="h-3 bg-gray-200 rounded w-12 animate-pulse"></div>
                        </div>
                        <div class="h-10 bg-gray-200 rounded-2xl rounded-tl-none w-3/4 animate-pulse"></div>
                    </div>
                </div>
            </div>


            <!-- Real Messages -->
            <div v-for="msg in messages" :key="msg.id" class="mb-4 flex flex-col w-full"
                 @mouseenter="hoveredMessageId = msg.id" @mouseleave="hoveredMessageId = null">
                 
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow duration-200 max-w-[90%] relative group"
                     :class="msg.user_id === $page.props.auth.user.id ? 'ml-auto' : 'mr-auto'">
                    
                    <!-- Root Message -->
                    <div class="flex items-start gap-3">
                        <!-- Avatar -->
                        <div class="shrink-0">
                             <img v-if="msg.user.profile_photo_url" :src="msg.user.profile_photo_url" :alt="msg.user.name" class="w-8 h-8 rounded-full object-cover" />
                             <div v-else class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-700 font-bold text-xs">
                                {{ msg.user.name.substring(0, 2) }}
                             </div>
                        </div>

                        <div class="flex-1">
                            <!-- Header -->
                            <div class="flex items-baseline justify-between gap-2">
                                <span class="font-bold text-gray-900 text-sm">{{ msg.user.name }}</span>
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-gray-400">{{ new Date(msg.created_at).toLocaleDateString() }} {{ new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</span>
                                    <Check class="w-3 h-3 text-gray-400" />
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="text-gray-800 mt-1 text-sm whitespace-pre-wrap leading-relaxed">{{ msg.content }}</div>

                             <!-- Root Files -->
                            <div v-if="msg.files && msg.files.length" class="mt-2 flex flex-wrap gap-2">
                                <a v-for="file in msg.files" :key="file.id" :href="file.url" target="_blank"
                                    class="flex items-center p-2 rounded-md bg-gray-50 border border-gray-200 hover:bg-gray-100 transition-colors text-xs text-gray-700">
                                    <FileText class="w-4 h-4 mr-2 text-gray-500" />
                                    <span class="truncate max-w-[200px]">{{ file.name }}</span>
                                    <span class="ml-2 text-gray-400">{{ file.readable_size }}</span>
                                    <Download class="w-3 h-3 ml-2 text-gray-400" />
                                </a>
                            </div>

                            
                            <!-- Reactions Display -->
                            <div v-if="msg.reactions && msg.reactions.length > 0" class="flex flex-wrap gap-1 mt-2">
                                <button v-for="reaction in msg.reactions" :key="reaction.emoji" 
                                        @click="toggleReaction(msg.id, reaction.emoji)"
                                        class="flex items-center gap-1 px-1.5 py-0.5 rounded-full text-xs border transition-colors"
                                        :class="reaction.user_reacted ? 'bg-blue-50 border-blue-200 text-blue-600' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100'">
                                    <span>{{ reaction.emoji }}</span>
                                    <span class="font-medium">{{ reaction.count }}</span>
                                </button>
                            </div>



                        </div>
                    </div>

                    <!-- Replies Section -->
                    <div v-if="(msg.replies && msg.replies.length > 0) || replyingToId === msg.id" class="mt-4 pl-12 sm:pl-14">
                        
                         <!-- Existing Replies -->
                         <div v-if="msg.replies && msg.replies.length > 0" class="space-y-3 mb-3">
                             <div v-for="reply in msg.replies" :key="reply.id" class="flex gap-3 group/reply relative"
                                  @mouseenter="hoveredMessageId = reply.id" @mouseleave="hoveredMessageId = null">
                                 <!-- Reply Avatar -->
                                 <div class="shrink-0">
                                     <img v-if="reply.user.profile_photo_url" :src="reply.user.profile_photo_url" class="w-6 h-6 rounded-full object-cover">
                                     <div v-else class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[9px] font-bold text-gray-600">
                                         {{ reply.user.name.substring(0,2) }}
                                     </div>
                                 </div>
                                 
                                 <div class="flex-1">
                                      <div class="flex items-baseline gap-2">
                                          <span class="font-semibold text-xs text-gray-800">{{ reply.user.name }}</span>
                                          <div class="flex items-center gap-1">
                                              <span v-if="!reply.is_optimistic" class="text-[10px] text-gray-400">{{ new Date(reply.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</span>
                                              <span v-else class="text-[10px] text-gray-400">Enviando...</span>
                                              
                                              <Clock v-if="reply.is_optimistic" class="w-3 h-3 text-gray-400" />
                                              <Check v-else class="w-3 h-3 text-gray-400" />
                                          </div>
                                      </div>
                                      <div class="text-xs text-gray-700 mt-0.5 whitespace-pre-wrap">{{ reply.content }}</div>
                                      
                                      <!-- Reply Files -->
                                       <div v-if="reply.files && reply.files.length" class="mt-1 space-y-1">
                                            <a v-for="file in reply.files" :key="file.id" :href="file.url" target="_blank" class="flex items-center text-xs text-blue-600 hover:underline">
                                                <FileText class="w-3 h-3 mr-1" /> {{ file.name }}
                                            </a>
                                        </div>
                                        
                                        <!-- Reply Reactions -->
                                        <div v-if="reply.reactions && reply.reactions.length > 0" class="flex flex-wrap gap-1 mt-1">
                                            <button v-for="reaction in reply.reactions" :key="reaction.emoji" 
                                                    @click="toggleReaction(reply.id, reaction.emoji)"
                                                    class="flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[10px] border transition-colors"
                                                    :class="reaction.user_reacted ? 'bg-blue-50 border-blue-200 text-blue-600' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100'">
                                                <span>{{ reaction.emoji }}</span>
                                                <span class="font-medium">{{ reaction.count }}</span>
                                            </button>
                                        </div>
                                 </div>
                                 
                                <!-- Reply Reaction Trigger -->
                                <div class="opacity-0 group-hover/reply:opacity-100 transition-opacity flex items-center self-start pt-1 relative">
                                     <div v-if="showReactionPickerId === reply.id" class="absolute bottom-full mb-1 left-0 bg-white border border-gray-200 shadow-lg rounded-full p-1 flex gap-1 z-10 animate-scaleIn">
                                        <button v-for="emoji in REACTION_EMOJIS" :key="emoji" @click="toggleReaction(reply.id, emoji)"
                                            class="p-1 hover:bg-gray-100 rounded-full text-base transition-transform hover:scale-125 leading-none">
                                            {{ emoji }}
                                        </button>
                                        <div class="w-px h-5 bg-gray-200 mx-1"></div>
                                        <button @click.stop="showFullReactionPickerId = reply.id; showReactionPickerId = null"
                                            class="p-1 hover:bg-gray-100 rounded-full text-gray-500 hover:text-gray-900 transition-colors" title="Más reacciones">
                                            <Plus class="w-3 h-3" />
                                        </button>
                                    </div>
                                    
                                     <!-- Full Emoji Picker (Reply) -->
                                     <div v-if="showFullReactionPickerId === reply.id" class="absolute bottom-full left-0 mb-2 z-50 shadow-2xl rounded-lg overflow-hidden">
                                         <!-- Overlay -->
                                         <div class="fixed inset-0 z-40" @click="showFullReactionPickerId = null"></div>
                                         <div class="relative z-50">
                                             <EmojiPicker :native="true" @select="onSelectEmoji(reply.id, $event)" />
                                         </div>
                                     </div>

                                    <button @click="showReactionPickerId = (showReactionPickerId === reply.id ? null : reply.id)"
                                        class="bg-gray-50 hover:bg-white border border-transparent hover:border-gray-200 shadow-sm rounded-full p-1 text-gray-400 hover:text-yellow-500 transition-colors">
                                        <Smile class="w-3 h-3" />
                                    </button>
                                </div>
                             </div>
                         </div>
                    </div>

                    <!-- Message Actions (Reply & React) -->
                    <div class="mt-2 flex items-center gap-2">
                         <!-- Reaction Button & Picker -->
                        <div class="relative">
                            <button @click.stop="showReactionPickerId = (showReactionPickerId === msg.id ? null : msg.id)"
                                class="flex items-center text-xs text-gray-500 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded-md transition-colors w-fit group-hover:text-gray-900">
                                <Smile class="w-4 h-4 mr-1.5" /> Reaccionar
                            </button>
                            
                            <!-- Quick Picker Popover -->
                            <div v-if="showReactionPickerId === msg.id" class="absolute bottom-full left-0 mb-2 bg-white border border-gray-200 shadow-xl rounded-full p-1.5 flex items-center gap-1 z-50 animate-scaleIn origin-bottom-left">
                                <button v-for="emoji in REACTION_EMOJIS" :key="emoji" @click="toggleReaction(msg.id, emoji)"
                                    class="p-1.5 hover:bg-gray-100 rounded-full text-xl transition-transform hover:scale-125 leading-none">
                                    {{ emoji }}
                                </button>
                                <div class="w-px h-6 bg-gray-200 mx-1"></div>
                                <button @click.stop="showFullReactionPickerId = msg.id; showReactionPickerId = null"
                                    class="p-1.5 hover:bg-gray-100 rounded-full text-gray-500 hover:text-gray-900 transition-colors" title="Más reacciones">
                                    <Plus class="w-4 h-4" />
                                </button>
                            </div>

                             <!-- Full Emoji Picker -->
                             <div v-if="showFullReactionPickerId === msg.id" class="absolute bottom-full left-0 mb-2 z-50 shadow-2xl rounded-lg overflow-hidden">
                                 <!-- Overlay to close -->
                                 <div class="fixed inset-0 z-40" @click="showFullReactionPickerId = null"></div>
                                 <div class="relative z-50">
                                     <EmojiPicker :native="true" @select="onSelectEmoji(msg.id, $event)" />
                                 </div>
                             </div>
                        </div>

                        <button @click="startReply(msg.id)" 
                             class="flex items-center text-xs text-gray-500 hover:bg-gray-100 hover:text-gray-900 px-2 py-1 rounded-md transition-colors w-fit">
                             <i class="fas fa-reply mr-2"></i> Responder
                        </button>
                    </div>

                </div>
            </div>

            <!-- Optimistic Root Messages (Ghost Cards) -->
            <div v-for="msg in optimisticMessages" :key="msg.id" class="mb-4 opacity-70 flex flex-col w-full">
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm max-w-[90%] ml-auto">
                     <div class="flex items-start gap-3">
                         <div class="shrink-0">
                             <img v-if="$page.props.auth.user.profile_photo_url" :src="$page.props.auth.user.profile_photo_url" class="w-8 h-8 rounded-full object-cover" />
                             <div v-else class="w-8 h-8 rounded-full bg-yellow-500 flex items-center justify-center text-white font-bold text-xs">
                                {{ $page.props.auth.user.name.substring(0, 2) }}
                             </div>
                        </div>
                        <div class="flex-1">
                             <div class="flex items-baseline justify-between">
                                <span class="font-bold text-gray-900 text-sm">{{ msg.user.name }}</span>
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-gray-400">Enviando...</span>
                                    <Clock class="w-3 h-3 text-gray-400" />
                                </div>
                            </div>
                            <div class="text-gray-800 mt-1 text-sm">{{ msg.content }}</div>

                             <!-- Optimistic Files -->
                            <div v-if="msg.files && msg.files.length" class="mt-2 flex flex-wrap gap-2">
                                <div v-for="(file, index) in msg.files" :key="index"
                                    class="flex items-center p-2 rounded-md bg-gray-50 border border-gray-200 text-xs text-gray-700 opacity-80">
                                    <FileText class="w-4 h-4 mr-2 text-gray-500" />
                                    <span class="truncate max-w-[200px]">{{ file.name }}</span>
                                    <span class="ml-2 text-gray-400">{{ file.readable_size }}</span>
                                </div>
                            </div>
                        </div>
                     </div>
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

            <!-- Reply Context Banner -->
            <div v-if="replyingToMessage" class="flex items-center justify-between bg-yellow-50 border border-yellow-200 rounded-t-lg px-4 py-2 border-b-0 -mb-1 relative z-0 mx-1">
                <div class="flex items-center gap-2 overflow-hidden">
                    <span class="text-xs font-bold text-yellow-800">Respondiendo a {{ replyingToMessage.user.name }}:</span>
                    <span class="text-xs text-yellow-700 truncate max-w-xs">{{ replyingToMessage.content }}</span>
                </div>
                <button @click="cancelReply" class="text-yellow-600 hover:text-yellow-800">
                    <X class="w-4 h-4" />
                </button>
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
                
                <textarea v-model="tempMessage" @keydown.enter.exact="handleEnter" ref="mainInput"
                    class="flex-1 bg-transparent border-none focus:ring-0 resize-none max-h-32 py-2 min-h-[44px]" rows="1"
                    :placeholder="replyingToId ? 'Escribe una respuesta...' : 'Nueva conversación...'"></textarea>
                    
                <button type="submit" :disabled="!tempMessage.trim() && chatUploads.length === 0"
                    class="p-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm">
                    <i class="fas fa-paper-plane w-5 h-5"></i>
                </button>
            </form>
        </div>
    </div>
</template>
