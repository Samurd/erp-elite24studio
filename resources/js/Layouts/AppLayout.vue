<script setup>
import Sidebar from '@/Components/Sidebar.vue';
import Navbar from '@/Components/Navbar.vue'; // Import Navbar
import MarketingSubmenu from '@/Components/Marketing/Submenu.vue';
import FileSelectorModal from '@/Components/Cloud/FileSelectorModal.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    title: String,
});
</script>

<template>
    <Head :title="title ? title : 'Laravel'" />

    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <Sidebar />

        <!-- Main content -->
        <div class="flex-1 flex flex-col ml-[280px]"> <!-- ml-[280px] to offset fixed sidebar width -->
            
            <!-- Navbar -->
            <Navbar />

            <!-- Marketing Submenu -->
            <div v-if="route().current('marketing.*')" class="px-4 py-2 pt-16">
                <MarketingSubmenu />
            </div>

            <main class="flex-1 p-4 overflow-auto pt-20">
                <slot />
            </main>
        </div>
        
        <!-- Global Modals -->
        <FileSelectorModal />
    </div>
</template>
