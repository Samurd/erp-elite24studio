<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: [Array, String, Number],
        default: () => [],
    },
    options: {
        type: Array,
        default: () => [],
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        default: 'Seleccionar...',
    },
    label: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const search = ref('');
const containerRef = ref(null);

const filteredOptions = computed(() => {
    if (!search.value) {
        return props.options;
    }
    const lowerSearch = search.value.toLowerCase();
    return props.options.filter(option => 
        option.name.toLowerCase().includes(lowerSearch)
    );
});

const selectedOptions = computed(() => {
    if (props.multiple) {
        return props.options.filter(option => props.modelValue.includes(option.id));
    } else {
        return props.options.find(option => option.id === props.modelValue);
    }
});

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        // Focus search input next tick could be added here
    }
};

const closeDropdown = () => {
    isOpen.value = false;
    search.value = '';
};

const selectOption = (option) => {
    if (props.multiple) {
        const newValue = [...props.modelValue];
        const index = newValue.indexOf(option.id);
        if (index === -1) {
            newValue.push(option.id);
        } else {
            newValue.splice(index, 1);
        }
        emit('update:modelValue', newValue);
    } else {
        emit('update:modelValue', option.id);
        closeDropdown();
    }
};

const removeOption = (id) => {
    if (props.multiple) {
        const newValue = props.modelValue.filter(v => v !== id);
        emit('update:modelValue', newValue);
    } else {
        emit('update:modelValue', null);
    }
};

const isSelected = (option) => {
    if (props.multiple) {
        return props.modelValue.includes(option.id);
    }
    return props.modelValue === option.id;
};

// Click outside directive logic
const handleClickOutside = (event) => {
    if (containerRef.value && !containerRef.value.contains(event.target)) {
        closeDropdown();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div ref="containerRef" class="relative">
        <label v-if="label" class="block font-medium text-sm text-gray-700 mb-1">
            {{ label }}
        </label>

        <!-- Trigger -->
        <div 
            @click="toggleDropdown"
            class="min-h-[42px] px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm cursor-pointer hover:border-gray-400 focus:outline-none focus:ring-1 focus:ring-yellow-500 focus:border-yellow-500 relative flex flex-wrap gap-2 items-center"
        >
            <span v-if="(!multiple && !selectedOptions) || (multiple && selectedOptions.length === 0)" class="text-gray-500 text-sm">
                {{ placeholder }}
            </span>

            <!-- Multiple Selection Tags -->
            <template v-if="multiple">
                <div 
                    v-for="option in selectedOptions" 
                    :key="option.id"
                    class="bg-gray-100 border border-gray-300 rounded-full pl-2 pr-1 py-0.5 flex items-center gap-2 text-sm"
                >
                    <img v-if="option.profile_photo_url" :src="option.profile_photo_url" class="w-5 h-5 rounded-full object-cover" alt="">
                    <span class="text-gray-700">{{ option.name }}</span>
                    <button @click.stop="removeOption(option.id)" class="text-gray-400 hover:text-red-500 focus:outline-none rounded-full p-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </template>
            
            <!-- Single Selection Display -->
            <template v-else-if="selectedOptions">
                 <div class="flex items-center gap-2 text-sm w-full">
                    <img v-if="selectedOptions.profile_photo_url" :src="selectedOptions.profile_photo_url" class="w-6 h-6 rounded-full object-cover" alt="">
                    <span class="text-gray-900">{{ selectedOptions.name }}</span>
                </div>
            </template>

             <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <!-- Dropdown Menu -->
        <div v-show="isOpen" class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
            <!-- Search Input -->
            <div class="sticky top-0 z-10 bg-white border-b px-2 py-2">
                <input 
                    type="text" 
                    v-model="search" 
                    placeholder="Buscar..." 
                    class="block w-full border-gray-300 rounded-md text-sm focus:border-yellow-500 focus:ring-yellow-500"
                    @click.stop
                >
            </div>

            <!-- Options List -->
            <ul v-if="filteredOptions.length > 0" class="py-1">
                <li 
                    v-for="option in filteredOptions" 
                    :key="option.id"
                    @click="selectOption(option)"
                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-yellow-50"
                    :class="{'bg-yellow-50 text-yellow-900': isSelected(option), 'text-gray-900': !isSelected(option)}"
                >
                    <div class="flex items-center">
                        <img v-if="option.profile_photo_url" :src="option.profile_photo_url" alt="" class="h-6 w-6 flex-shrink-0 rounded-full object-cover">
                         <span :class="{'font-semibold': isSelected(option), 'font-normal': !isSelected(option), 'ml-3': option.profile_photo_url}" class="block truncate">
                            {{ option.name }}
                        </span>
                    </div>

                    <span v-if="isSelected(option)" class="text-yellow-600 absolute inset-y-0 right-0 flex items-center pr-4">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </li>
            </ul>
             <div v-else class="py-2 px-3 text-sm text-gray-500 text-center">
                No se encontraron resultados
            </div>
        </div>
    </div>
</template>
