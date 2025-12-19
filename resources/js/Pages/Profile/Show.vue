<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import UpdateProfileInformationForm from '@/Components/Profile/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from '@/Components/Profile/UpdatePasswordForm.vue';
import TwoFactorAuthenticationForm from '@/Components/Profile/TwoFactorAuthenticationForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Components/Profile/LogoutOtherBrowserSessionsForm.vue';
import DeleteUserForm from '@/Components/Profile/DeleteUserForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';

defineProps({
    sessions: Array,
});
</script>

<template>
    <AppLayout title="Perfil">
        <Head title="Perfil" />

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <UpdateProfileInformationForm />

            <SectionBorder />

            <UpdatePasswordForm />

            <SectionBorder />

            <TwoFactorAuthenticationForm />

            <SectionBorder />

            <LogoutOtherBrowserSessionsForm :sessions="sessions" />

            <SectionBorder />

            <DeleteUserForm />
        </div>
    </AppLayout>
</template>
