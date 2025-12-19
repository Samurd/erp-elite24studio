<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ApuCampaignForm from './Form.vue';

defineProps({
    apuCampaign: Object,
    campaigns: Array,
    unitOptions: Array,
});
</script>

<template>
    <AppLayout title="Editar APU">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Editar APU</h1>
                    <p class="text-gray-600 mt-1">
                        Modificar información del APU #{{ apuCampaign.id }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('donations.apu-campaigns.index')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                </div>
            </div>
        </div>

        <ApuCampaignForm 
            :apu-campaign="apuCampaign"
            :campaigns="campaigns"
            :unit-options="unitOptions"
        />
    </AppLayout>
</template>
