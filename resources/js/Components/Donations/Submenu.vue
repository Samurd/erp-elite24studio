<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="w-min bg-white border-b shadow-xs h-12 flex items-center relative rounded-full m-4">
        <div id="scrollContainer"
            class="flex-1 overflow-x-auto whitespace-nowrap flex items-center scrollbar-hide scroll-smooth">
            <ul class="flex items-center space-x-6 text-sm font-medium px-4">
                <li>
                    <Link :href="route('donations.campaigns.index')"
                        :class="route().current('donations.campaigns.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600'"
                        class="hover:text-yellow-600">
                        Campañas
                    </Link>
                </li>
                <li>
                    <Link :href="route('donations.donations.index')"
                        :class="route().current('donations.donations.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600'"
                        class="hover:text-yellow-600">
                        Donaciones
                    </Link>
                </li>
                <li>
                    <a href="#" class="hover:text-yellow-600">
                        Calendario
                    </a>
                </li>
                <li>
                    <Link :href="route('donations.volunteers.index')"
                        :class="route().current('donations.volunteers.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600'"
                        class="hover:text-yellow-600">
                        Voluntariado
                    </Link>
                </li>
                <li>
                    <Link :href="route('donations.alliances.index')"
                        :class="route().current('donations.alliances.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600'"
                        class="hover:text-yellow-600">
                        Alianzas/Convenios
                    </Link>
                </li>
                <li>
                    <Link :href="route('donations.apu-campaigns.index')"
                        :class="route().current('donations.apu-campaigns.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600'"
                        class="hover:text-yellow-600">
                        Control Presupuestos APU
                    </Link>
                </li>
            </ul>
        </div>
    </div>
</template>
