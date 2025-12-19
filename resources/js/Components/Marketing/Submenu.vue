<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="w-min bg-white border-b shadow-xs h-12 flex items-center relative rounded-full m-4">
        <div id="scrollContainer"
            class="flex-1 overflow-x-auto whitespace-nowrap flex items-center scrollbar-hide scroll-smooth">
            <ul class="flex items-center space-x-6 text-sm font-medium px-4">
                <li>
                    <Link :href="route('marketing.strategies.index')"
                        :class="route().current('marketing.strategies.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600'"
                        class="hover:text-yellow-600">
                        Estrategias
                    </Link>
                </li>
                <li>
                    <Link :href="route('marketing.socialmedia.index')"
                        :class="route().current('marketing.socialmedia.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600'"
                        class="hover:text-yellow-600">
                        Redes Sociales & Web
                    </Link>
                </li>
                <li>
                    <Link :href="route('marketing.cases.index')"
                        :class="route().current('marketing.cases.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600'"
                        class="hover:text-yellow-600">
                        Casos
                    </Link>
                </li>
                <li>
                    <Link :href="route('marketing.events.index')"
                        :class="route().current('marketing.events.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600'"
                        class="hover:text-yellow-600">
                        Eventos + Presupuestos + APU de Pauta
                    </Link>
                </li>
                <li>
                    <Link :href="route('marketing.ad-pieces.index')"
                        :class="route().current('marketing.ad-pieces.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600'"
                        class="hover:text-yellow-600">
                        Piezas Publicitarias
                    </Link>
                </li>
            </ul>
        </div>
    </div>
</template>
