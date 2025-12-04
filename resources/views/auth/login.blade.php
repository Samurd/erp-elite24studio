<x-guest-layout>


    <x-authentication-card>
        <x-slot name="title">
            <h3 class="text-white font-semibold text-2xl">Iniciar Sesión</h3>
            {{-- <x-authentication-card-logo /> --}}
        </x-slot>





        <form method="POST" action="{{ route('login') }}" class="w-full p-4">
            @csrf

            <div class="">
                <x-label class="text-white" for="email" value="{{ __('Correo Electrónico') }}" />
                <div class="bg-gray-500/20 rounded-md">
                    <x-input id="email"
                        class="block mt-1 w-full  focus:ring-2 focus:ring-yellow-500 bg-transparent  backdrop-blur-sm text-white placeholder-gray-400 border-none "
                        type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                        placeholder="Correo Electrónico" />
                </div>
            </div>

            <div class="mt-4">
                <x-label class="text-white" for="password" value="{{ __('Contraseña') }}" />
                <div class="bg-gray-500/20 rounded-md relative">
                    <x-input id="password"
                        class="block mt-1 w-full focus:ring-2 focus:ring-yellow-500 bg-transparent  backdrop-blur-sm text-white placeholder-gray-400 border-none"
                        type="password" name="password" required autocomplete="current-password"
                        placeholder="Contraseña" />

                    <button type="button"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white transition-colors"
                        onclick="togglePasswordVisibility()">
                        <svg id="password-eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                    </button>

                </div>
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-300">{{ __('Recordarme') }}</span>
                </label>
            </div>

            <x-validation-errors class="mb-4 text-center" />

            <div class="flex flex-col items-center justify-center mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-300 hover:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif

                <x-button
                    class="w-full bg-black hover:bg-gray-900 text-black font-semibold py-3 transition mt-4 flex items-center justify-center">
                    {{ __('Iniciar Sesión') }}
                </x-button>
            </div>
        </form>

        <script>
            function togglePasswordVisibility() {
                const passwordInput = document.getElementById('password');
                const eyeIcon = document.getElementById('password-eye-icon');
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Cambiar el ícono dependiendo del estado
                if (type === 'text') {
                    // Contraseña visible - mostrar ojo cerrado
                    eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>';
                } else {
                    // Contraseña oculta - mostrar ojo abierto
                    eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                }
            }
        </script>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession
    </x-authentication-card>
</x-guest-layout>