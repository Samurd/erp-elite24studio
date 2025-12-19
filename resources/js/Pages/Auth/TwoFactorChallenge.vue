<script setup>
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import Label from '@/Components/Label.vue';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import InputError from '@/Components/InputError.vue';

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>

<template>
    <GuestLayout title="Autenticación de Dos Factores">
        <Head title="Autenticación de Dos Factores" />

        <AuthenticationCard>
            <div class="mb-4 text-sm text-gray-300">
                <template v-if="! recovery">
                    Por favor confirma el acceso a tu cuenta ingresando el código de autenticación proporcionado por tu aplicación de autenticación.
                </template>

                <template v-else>
                    Por favor confirma el acceso a tu cuenta ingresando uno de tus códigos de recuperación de emergencia.
                </template>
            </div>

            <form @submit.prevent="submit" class="w-full">
                <div v-if="! recovery">
                    <Label for="code" value="Código" class="text-white" />
                    <div class="bg-gray-500/20 rounded-md mt-1">
                        <Input
                            id="code"
                            ref="codeInput"
                            v-model="form.code"
                            type="text"
                            inputmode="numeric"
                            class="block mt-1 w-full focus:ring-2 focus:ring-yellow-500 bg-transparent backdrop-blur-sm text-white placeholder-gray-400 border-none"
                            autofocus
                            autocomplete="one-time-code"
                        />
                    </div>
                    <InputError class="mt-2 text-red-400" :message="form.errors.code" />
                </div>

                <div v-else>
                    <Label for="recovery_code" value="Código de Recuperación" class="text-white" />
                    <div class="bg-gray-500/20 rounded-md mt-1">
                        <Input
                            id="recovery_code"
                            ref="recoveryCodeInput"
                            v-model="form.recovery_code"
                            type="text"
                            class="block mt-1 w-full focus:ring-2 focus:ring-yellow-500 bg-transparent backdrop-blur-sm text-white placeholder-gray-400 border-none"
                            autocomplete="one-time-code"
                        />
                    </div>
                    <InputError class="mt-2 text-red-400" :message="form.errors.recovery_code" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button
                        type="button"
                        class="text-sm text-gray-300 hover:text-gray-200 underline cursor-pointer"
                        @click.prevent="toggleRecovery"
                    >
                        <template v-if="! recovery">
                            Usar un código de recuperación
                        </template>

                        <template v-else>
                            Usar un código de autenticación
                        </template>
                    </button>

                    <Button
                        class="ms-4"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        Iniciar Sesión
                    </Button>
                </div>
            </form>
        </AuthenticationCard>
    </GuestLayout>
</template>
