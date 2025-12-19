<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Label from '@/Components/Label.vue';
import Input from '@/Components/Input.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const loading = ref(false);

const submit = () => {
    loading.value = true;
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
            loading.value = false;
        },
    });
};

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <GuestLayout title="Iniciar Sesión">
        <Head title="Iniciar Sesión" />

        <div>
            <!-- Title outside the form content, like original -->
            <h3 class="text-white font-semibold text-2xl text-center mb-4">Iniciar Sesión</h3>

            <div v-if="status" class="mb-4 font-medium text-sm text-green-400 text-center">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="w-full p-4">
                <div>
                    <Label for="email" value="Correo Electrónico" class="text-white" />
                    <div class="bg-gray-500/20 rounded-md">
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="block mt-1 w-full focus:ring-2 focus:ring-yellow-500 bg-transparent backdrop-blur-sm text-white placeholder-gray-400 border-none"
                            placeholder="Correo Electrónico"
                            required
                            autofocus
                            autocomplete="username"
                        />
                    </div>
                    <InputError class="mt-2 text-red-400" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <Label for="password" value="Contraseña" class="text-white" />
                    <div class="bg-gray-500/20 rounded-md relative">
                        <Input
                            id="password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            class="block mt-1 w-full focus:ring-2 focus:ring-yellow-500 bg-transparent backdrop-blur-sm text-white placeholder-gray-400 border-none"
                            placeholder="Contraseña"
                            required
                            autocomplete="current-password"
                        />
                        <button
                            type="button"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white transition-colors"
                            @click="togglePasswordVisibility"
                        >
                            <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                            </svg>
                        </button>
                    </div>
                    <InputError class="mt-2 text-red-400" :message="form.errors.password" />
                </div>

                <div class="block mt-4">
                    <label for="remember_me" class="flex items-center">
                        <Checkbox id="remember_me" v-model:checked="form.remember" name="remember" />
                        <span class="ms-2 text-sm text-gray-300">Recordarme</span>
                    </label>
                </div>

                <div class="flex flex-col items-center justify-center mt-4">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="underline text-sm text-gray-300 hover:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500"
                    >
                        ¿Olvidaste tu contraseña?
                    </Link>

                    <button
                        type="submit"
                        class="w-full bg-black hover:bg-gray-900 text-white font-semibold py-3 transition mt-4 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed rounded-md"
                        :disabled="form.processing || loading"
                    >
                        <span v-if="!loading">Iniciar Sesión</span>
                        <span v-else class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                            </svg>
                            Procesando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
