<div x-data="{ tab: 'users' }">
    <x-dialog-modal wire:model="showModal" position="center">
        <x-slot name="title">
            <div class="flex justify-between items-center border-b p-2">
                <h3 class="text-lg font-bold">
                    Compartir {{ Str::afterLast($shareableType, '\\') }}
                </h3>
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="flex flex-col gap-4">

                <!-- Tabs -->
                <div class="flex border-b mb-3">
                    <template x-for="option in [
                        { key: 'users', label: 'Usuarios' },
                        { key: 'teams', label: 'Grupos' },
                        { key: 'link', label: 'Enlace público' }
                    ]">
                        <button class="px-3 py-2 text-sm font-medium"
                            :class="tab === option.key ? 'border-b-2 border-yellow-600 text-yellow-700' : 'text-gray-500'"
                            @click="tab = option.key" x-text="option.label"></button>
                    </template>
                </div>

                <!-- Usuarios -->
                <div x-show="tab === 'users'" x-transition>
                    <label class="block font-semibold mb-1">Seleccionar usuario</label>
                    <select wire:model.live="selectedUserId"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600">
                        <option value="">Seleccionar usuario...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>

                    @error('selectedUserId')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    <div class="mt-3">
                        <label class="block font-semibold mb-1">Permiso</label>
                        <select wire:model="permission"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600">
                            <option value="view">Solo lectura</option>
                            <option value="edit">Editar</option>
                        </select>
                    </div>
                </div>

                <!-- Grupos -->
                <div x-show="tab === 'teams'" x-transition>
                    <label class="block font-semibold mb-1">Seleccionar grupo</label>
                    <select wire:model.live="selectedTeamId"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600">
                        <option value="">Seleccionar grupo...</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>


                    @error('selectedTeamId')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    <div class="mt-3">
                        <label class="block font-semibold mb-1">Permiso</label>
                        <select wire:model="permission"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600">
                            <option value="view">Solo lectura</option>
                            <option value="edit">Editar</option>
                        </select>
                    </div>
                </div>


                <!-- Enlace público -->
                <div x-show="tab === 'link'" x-transition>
                    <div class="flex flex-col gap-3">

                        @if(!$shareLink)
                            <div>
                                <label class="block font-semibold mb-1">Expiración (opcional)</label>
                                <x-input type="date" wire:model="expiresAt" class="w-full" />

                                @error('expiresAt')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <x-button wire:click="generateLink" class="bg-yellow-700 hover:bg-yellow-800 text-white w-fit">
                                Generar enlace
                            </x-button>
                        @else
                            <div class="bg-gray-100 p-2 rounded-md flex justify-between items-center">
                                <input type="text" readonly value="{{ $shareLink }}"
                                    class="bg-transparent w-full text-sm" />
                                <button x-data="{ copied: false }"
                                    @click="navigator.clipboard.writeText('{{ $shareLink }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="text-blue-600 text-sm ml-2">
                                    Copiar
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                @if($expiresAt)
                                    Expira el {{ \Carbon\Carbon::parse($expiresAt)->format('d/m/Y') }}
                                @else
                                    Sin fecha de expiración
                                @endif
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Lista de accesos existentes -->
                @if(count($existingShares) > 0)
                    <div class="border-t pt-4 mt-2">
                        <h4 class="font-bold text-sm mb-2">Personas con acceso</h4>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            @foreach($existingShares as $share)
                                <div class="flex items-center justify-between bg-gray-50 p-2 rounded text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">
                                            {{ $share->shared_with_team_id ? '👥' : '👤' }}
                                        </span>
                                        <div>
                                            <div class="font-medium">
                                                {{ $share->shared_with_team_id ? $share->sharedWithTeam->name : $share->sharedWithUser->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $share->permission === 'edit' ? 'Puede editar' : 'Solo lectura' }}
                                            </div>
                                        </div>
                                    </div>
                                    <button wire:click="removeShare({{ $share->id }})"
                                        wire:confirm="¿Estás seguro de quitar el acceso?"
                                        class="text-red-500 hover:text-red-700 p-1" title="Quitar acceso">
                                        🗑️
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-button wire:click="closeModal" class="bg-gray-600 hover:bg-gray-700 text-white">
                Cerrar
            </x-button>

            <x-button wire:click="save" class="bg-yellow-700 hover:bg-yellow-800 text-white ml-2"
                x-show="tab !== 'link'"
                x-bind:disabled="(tab === 'users' && !$wire.selectedUserId) || (tab === 'teams' && !$wire.selectedTeamId)">
                Compartir
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>