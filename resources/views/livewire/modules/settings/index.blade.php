<div class="py-8">
    <div class="max-w-3xl mx-auto bg-[#252525] shadow rounded-2xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">
            Conexiones con aplicaciones externas
        </h2>

        <p class="text-gray-500 dark:text-gray-400 mb-8">
            Conecta tu cuenta con aplicaciones externas.
        </p>

        <div class="grid sm:grid-cols-2 gap-6">
            {{-- Google --}}
            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/google/google-original.svg"
                        alt="Google" class="w-8 h-8">

                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">Google</h3>

                        @if ($googleConnected)
                            <p class="text-sm text-green-500">Conectado</p>
                            <div class="flex items-center gap-2 mt-1">
                                @if ($googleConnection->avatar)
                                    <img src="{{ $googleConnection->avatar }}" alt="Avatar"
                                        class="w-6 h-6 rounded-full border border-gray-300">
                                @endif
                                <span class="text-sm text-gray-300">
                                    {{ $googleConnection->name }} ({{ $googleConnection->email }})
                                </span>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">No conectado</p>
                        @endif
                    </div>
                </div>

                @if ($googleConnected)
                    <form method="POST" action="{{ route('oauth.disconnect', 'google') }}">
                        @csrf
                        <x-button class="bg-red-500 hover:bg-red-600">
                            Desconectar
                        </x-button>
                    </form>
                @else
                    <a href="{{ route('oauth.redirect', 'google') }}">
                        <x-button class="bg-blue-500 hover:bg-blue-600 text-white">
                            Conectar
                        </x-button>
                    </a>
                @endif
            </div>

            {{-- Placeholder para otros proveedores --}}
            <div
                class="border border-dashed border-gray-300 dark:border-gray-700 rounded-xl p-5 flex items-center justify-center text-gray-400 dark:text-gray-500">
                <span>Más conexiones próximamente…</span>
            </div>
        </div>

        <div class="mt-8 text-sm text-gray-400 dark:text-gray-500 text-center">
            Tu información está protegida y solo se usará para integrar tus datos con los servicios seleccionados.
        </div>
    </div>
</div>