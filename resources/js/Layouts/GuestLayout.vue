<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    title: String,
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-r from-black via-yellow-600 to-yellow-400 flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <Head :title="title" />
        
        <!-- <div>
            <slot name="logo">
                <a href="/">
                    <img src="/img/logo.png" alt="Logo" class="w-20 h-20" />
                </a>
            </slot>
        </div> -->

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-black/30 backdrop-blur-md shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
