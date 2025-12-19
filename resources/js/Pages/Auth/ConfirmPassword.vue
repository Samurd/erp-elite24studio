<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import Label from '@/Components/Label.vue';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import InputError from '@/Components/InputError.vue';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout title="Confirmar Contraseña">
        <Head title="Confirmar Contraseña" />

        <AuthenticationCard>
            <div class="mb-4 text-sm text-gray-300">
                Esta es un área segura de la aplicación. Por favor, confirma tu contraseña antes de continuar.
            </div>

            <form @submit.prevent="submit" class="w-full">
                <div>
                    <Label for="password" value="Contraseña" class="text-white" />
                    <div class="bg-gray-500/20 rounded-md mt-1">
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="block mt-1 w-full focus:ring-2 focus:ring-yellow-500 bg-transparent backdrop-blur-sm text-white placeholder-gray-400 border-none"
                            placeholder="Contraseña"
                            required
                            autocomplete="current-password"
                            autofocus
                        />
                    </div>
                    <InputError class="mt-2 text-red-400" :message="form.errors.password" />
                </div>

                <div class="flex justify-end mt-4">
                    <Button
                        class="ms-4"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        Confirmar
                    </Button>
                </div>
            </form>
        </AuthenticationCard>
    </GuestLayout>
</template>
