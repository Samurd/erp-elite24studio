<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import Label from '@/Components/Label.vue';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import InputError from '@/Components/InputError.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout title="Registro">
        <Head title="Registro" />

        <AuthenticationCard>
            <template #logo>
                <Link href="/">
                    <img src="/img/logo.png" alt="Logo" class="w-20 h-20" />
                </Link>
            </template>

            <form @submit.prevent="submit" class="w-full">
                <div>
                    <Label for="name" value="Nombre" class="text-white" />
                    <div class="bg-gray-500/20 rounded-md mt-1">
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="block mt-1 w-full focus:ring-2 focus:ring-yellow-500 bg-transparent backdrop-blur-sm text-white placeholder-gray-400 border-none"
                            placeholder="Nombre completo"
                            required
                            autofocus
                            autocomplete="name"
                        />
                    </div>
                    <InputError class="mt-2 text-red-400" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <Label for="email" value="Correo Electrónico" class="text-white" />
                    <div class="bg-gray-500/20 rounded-md mt-1">
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="block mt-1 w-full focus:ring-2 focus:ring-yellow-500 bg-transparent backdrop-blur-sm text-white placeholder-gray-400 border-none"
                            placeholder="Correo Electrónico"
                            required
                            autocomplete="username"
                        />
                    </div>
                    <InputError class="mt-2 text-red-400" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <Label for="password" value="Contraseña" class="text-white" />
                    <div class="bg-gray-500/20 rounded-md mt-1">
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="block mt-1 w-full focus:ring-2 focus:ring-yellow-500 bg-transparent backdrop-blur-sm text-white placeholder-gray-400 border-none"
                            placeholder="Contraseña"
                            required
                            autocomplete="new-password"
                        />
                    </div>
                    <InputError class="mt-2 text-red-400" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <Label for="password_confirmation" value="Confirmar Contraseña" class="text-white" />
                    <div class="bg-gray-500/20 rounded-md mt-1">
                        <Input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="block mt-1 w-full focus:ring-2 focus:ring-yellow-500 bg-transparent backdrop-blur-sm text-white placeholder-gray-400 border-none"
                            placeholder="Confirmar Contraseña"
                            required
                            autocomplete="new-password"
                        />
                    </div>
                    <InputError class="mt-2 text-red-400" :message="form.errors.password_confirmation" />
                </div>

                <div class="flex items-center justify-center mt-4">
                    <Button
                        class="w-full flex items-center justify-center"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        Registrarse
                    </Button>
                </div>

                <div class="flex items-center justify-center mt-4">
                    <Link
                        :href="route('login')"
                        class="underline text-sm text-gray-300 hover:text-gray-200 rounded-md focus:outline-none"
                    >
                        ¿Ya tienes una cuenta?
                    </Link>
                </div>
            </form>
        </AuthenticationCard>
    </GuestLayout>
</template>
