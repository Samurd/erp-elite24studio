<script setup>
defineProps({
    title: String,
});
</script>

<template>
    <div>
        <slot name="logo" />
        
        <div class="mt-4">
            <h3 v-if="title" class="text-lg font-semibold text-center mb-4">{{ title }}</h3>
            <slot />
        </div>
    </div>
</template>
