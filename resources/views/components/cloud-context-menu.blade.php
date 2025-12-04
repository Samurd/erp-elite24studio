@props(['item', 'type'])

<div x-data="{ open: false }" class="relative inline-block text-left">
    <button @click.stop="open = !open" @click.away="open = false"
        class="p-1 rounded-full hover:bg-white/10 text-gray-400 hover:text-white transition-colors">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
        </svg>
    </button>

    <div x-show="open"
        class="absolute right-0 mt-2 w-48 bg-[#252525] border border-white/10 rounded-lg shadow-xl z-50 py-1"
        x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100" style="display: none;" x-cloak>

        {{-- VERIFICACIÓN DE PERMISOS: Usamos @can en lugar de ->hasPermission --}}

        @can('update', $item)
            {{-- SHARE ACTION --}}
            {{-- Solo el dueño o admins deberían poder compartir --}}
            @if(Auth::id() === $item->user_id || Auth::user()->can('cloud.update'))
                <button wire:click="$dispatch('open-share-dialog', { type: '{{ $type }}', id: {{ $item->id }} }); open = false"
                    class="flex items-center gap-2 w-full px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z">
                        </path>
                    </svg>
                    Compartir
                </button>
            @endif

            {{-- RENAME ACTION --}}
            <button wire:click="startRename({{ $item->id }}, '{{ $type }}'); open = false"
                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white text-left">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
                Renombrar
            </button>
        @endcan

        @can('delete', $item)
            {{-- DELETE ACTION --}}
            <div class="border-t border-white/5 my-1"></div>
            <button wire:click="delete{{ ucfirst($type) }}({{ $item->id }}); open = false"
                wire:confirm="¿Estás seguro de eliminar este elemento?"
                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 text-left">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                    </path>
                </svg>
                Eliminar
            </button>
        @endcan

    </div>
</div>