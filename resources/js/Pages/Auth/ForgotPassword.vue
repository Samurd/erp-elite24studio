<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import Label from '@/Components/Label.vue';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import InputError from '@/Components/InputError.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout title="Recuperar Contraseña">
        <Head title="Recuperar Contraseña" />

        <AuthenticationCard>
            <div class="mb-4 text-sm text-gray-300">
                ¿Olvidaste tu contraseña? No hay problema. Solo indícanos tu dirección de correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
            </div>

            <div v-if="status" class="mb-4 font-medium text-sm text-green-400">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="w-full">
                <div>
                    <Label for="email" value="Correo Electrónico" class="text-white" />
                    <div class="bg-gray-500/20 rounded-md mt-1">
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="block mt-1 w-full focus:ring-2 focus:ring-yellow-500 bg-transparent backdrop-blur-sm text-white placeholder-gray-400 border-none"
                            placeholder="Correo Electrónico"
                            required
                            autofocus
                        />
                    </div>
                    <InputError class="mt-2 text-red-400" :message="form.errors.email" />
                </div>

                <div class="flex items-center justify-center mt-4">
                    <Button
                        class="w-full flex items-center justify-center"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        Enviar enlace de recuperación
                    </Button>
                </div>
            </form>
        </AuthenticationCard>
    </GuestLayout>
</template>
