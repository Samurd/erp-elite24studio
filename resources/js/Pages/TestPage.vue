<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    message: String,
});
</script>

<template>
    <Head title="Test Page" />
    <div class="bg-white shadow-md rounded-lg p-8">
        <h1 class="text-3xl font-bold text-indigo-600 mb-4">Inertia Works!</h1>
        <p class="text-gray-700 mb-6">{{ message }}</p>
        <a href="/" class="text-indigo-500 hover:text-indigo-700 hover:underline font-medium">Back to Regular Website</a>
    </div>
</template>
