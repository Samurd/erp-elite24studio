<header class="h-16 bg-white p-4 flex items-center justify-between w-full z-20">
    <!-- Search Bar -->
    <div class="w-2/3 relative ml-5">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
            <i class="fas fa-search text-gray-400 text-xl"></i>
        </span>
        <x-input type="text" placeholder="Buscar aquí" class="w-full" />
    </div>

    <!-- Icons and Avatar -->
    <div class="flex items-center space-x-6 relative mr-5">



        <!-- Modulo para crear notificaciones, recodatorios, etc. -->
        <a href="{{ route('notifications.index') }}">
            <div class="cursor-pointer">
                <x-gmdi-edit-notifications-r class="w-8 h-8" />
            </div>
        </a>


        <!-- Chats privados -->
        <a href="{{ route('teams.chats') }}">
            <div class="cursor-pointer">
                <x-ri-chat-private-line class="w-8 h-8" />
            </div>
        </a>


        <!-- Modulo Teams -->
        <a href="{{ route('teams.index') }}">
            <div class="cursor-pointer">
                <x-bi-microsoft-teams class="w-6 h-6" />
            </div>
        </a>

        <!-- Usuarios -->

        @canArea("view", "usuarios")
        <a href="{{ route('users.index') }}">
            <div class="w-8 h-8 flex items-center justify-center ">
                <x-clarity-users-solid />

            </div>
        </a>
        @endCanArea


        <!-- Notificaciones -->
        <livewire:components.notification-dropdown />

        <!-- User Information -->

        <div class="flex flex-col justify-center ">
            <span class="text-sm">{{ Auth::user()->name }}</span>

            <div
                class="bg-gradient-to-r from-black via-yellow-600 to-yellow-400 w-full flex items-center justify-center rounded-lg text-white text-sm font-semibold px-3">
                @if(Auth::user()->roles()->first()->display_name != null)

                    <span>{{ Auth::user()->roles()->first()->display_name }}</span>
                @else
                    <span>Usuario</span>
                @endif
            </div>
        </div>


        <!-- Perfil -->
        <div class="relative">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}"
                        class="w-10 h-10 rounded-full object-cover cursor-pointer" />
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link href="{{ route('profile.show') }}" class="flex items-center">
                        <x-fas-user-circle class="w-4 h-4 mr-3 text-gray-500" /> {{ __('Perfil') }}
                    </x-dropdown-link>



                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <x-dropdown-link href="{{ route('api-tokens.index') }}">
                            {{ __('API Tokens') }}
                        </x-dropdown-link>
                    @endif

                    <div class="border-t border-gray-200"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center">
                            <x-fas-sign-out-alt class="w-4 h-4 mr-3 text-gray-500" /> {{ __('Cerrar sesión') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>

    </div>
</header>