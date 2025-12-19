<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import Button from '@/Components/Button.vue';

defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => form.recentlySuccessful);
</script>

<template>
    <GuestLayout title="Verificar Email">
        <Head title="Verificar Email" />

        <AuthenticationCard>
            <div class="mb-4 text-sm text-gray-300">
                Antes de continuar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que acabamos de enviarte? Si no recibiste el correo, con gusto te enviaremos otro.
            </div>

            <div v-if="verificationLinkSent" class="mb-4 font-medium text-sm text-green-400">
                Se ha enviado un nuevo enlace de verificación a tu dirección de correo electrónico.
            </div>

            <form @submit.prevent="submit" class="w-full">
                <div class="mt-4 flex items-center justify-between">
                    <Button
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        Reenviar Email de Verificación
                    </Button>

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-300 hover:text-gray-200 rounded-md focus:outline-none"
                    >
                        Cerrar Sesión
                    </Link>
                </div>
            </form>
        </AuthenticationCard>
    </GuestLayout>
</template>
