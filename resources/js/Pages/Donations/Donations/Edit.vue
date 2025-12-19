<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DonationForm from './Form.vue';

defineProps({
    donation: Object,
    campaigns: Array,
});
</script>

<template>
    <AppLayout title="Editar Donación">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Editar Donación</h1>
                    <p class="text-gray-600 mt-1">
                        Modificar información de la donación #{{ donation.id }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('donations.donations.index')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                </div>
            </div>
        </div>

        <DonationForm 
            :donation="donation"
            :campaigns="campaigns"
        />
    </AppLayout>
</template>
