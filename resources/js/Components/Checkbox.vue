<script setup>
defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        default: null,
    },
});

defineEmits(['update:checked']);
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        :checked="checked"
        class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500"
        @change="$emit('update:checked', $event.target.checked)"
    />
</template>
