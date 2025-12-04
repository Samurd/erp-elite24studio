<div>
    <h3 class="mb-4 text-[22px] font-bold">{{ $isEdit ? 'Editar usuario' : 'Crear nuevo usuario' }}</h3>

    {{-- Mensaje flash --}}
    @if (session('success'))
        <div class="bg-green-500 text-white p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="grid grid-cols-2 gap-4">
        <!-- Nombre -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
            <x-input type="text" wire:model="name" id="name" class="mt-1 w-full" />
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Correo</label>
            <x-input type="email" wire:model="email" id="email" class="mt-1 w-full" />
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Rol -->
        <div>
            <label for="role" class="block text-sm font-medium text-gray-700">Rol</label>
            <select wire:model="role" wire:change="$set('role', $event.target.value)" id="role"
                class="mt-1 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2  focus:ring-yellow-500">
                <option value="">-- Seleccionar --</option>
                @foreach($roles as $roleItem)
                    <option value="{{ $roleItem->id }}">{{ $roleItem->display_name ?? $roleItem->name }}</option>
                @endforeach

            </select>
            @error('role') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <div>
                <!-- Botón para crear rol -->
                <x-button type="button" class="my-4" wire:click="$set('openCreateRoleModal', true)">
                    Crear Rol
                </x-button>

                @isset($role)
                    @if($role)
                        <x-button type="button" class="bg-yellow-500" wire:click="editRole">
                            Editar Rol
                        </x-button>
                    @endif
                @endisset
            </div>
        </div>

        <!-- Contraseña -->
        <div>
            <label for="password"
                class="block text-sm font-medium text-gray-700">{{ $isEdit ? 'Nueva contraseña (opcional)' : 'Contraseña' }}</label>
            <x-input type="password" wire:model="password" id="password" class="mt-1 w-full" />
            @if($isEdit)
                <p class="text-xs text-gray-500 mt-1">Déjala en blanco para mantener la contraseña actual.</p>
            @endif
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Botón -->
        <div class="col-span-2 flex justify-end">
            <x-button type="submit">
                {{ $isEdit ? 'Actualizar Usuario' : 'Guardar Usuario' }}
            </x-button>
        </div>
    </form>






    <!-- Modal Jetstream -->
    <x-dialog-modal wire:model="openCreateRoleModal">
        <x-slot name="title">
            {{ $isEditRole ? 'Editar rol' : 'Crear nuevo rol' }}
        </x-slot>

        <x-slot name="content">
            <!-- Nombre del rol -->
            <div class="mb-4">
                <x-label for="roleName" value="Nombre del rol" />
                <x-input id="roleName" type="text" class="mt-1 block w-full" wire:model.defer="roleName" />
                <x-input-error for="roleName" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-label value="Permisos por área" />
                <div class="max-h-60 overflow-y-auto mt-1 space-y-4">
                    @foreach($permissions->groupBy(fn($p) => $p->area->parent?->name ?? $p->area->name) as $parentName => $permisosParent)
                        <div>
                            <!-- Nombre del área padre -->
                            <h3 class="font-bold text-gray-900">{{ $parentName }}</h3>

                            <!-- Permisos del área padre directamente -->
                            <div class="grid grid-cols-2 gap-2 mt-1 ml-2">
                                @foreach($permisosParent->filter(fn($p) => is_null($p->area->parent_id)) as $permiso)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="selectedPermissions" value="{{ $permiso->id }}"
                                            wire:change="applyDependencies({{ $permiso->id }}, $event.target.checked)"
                                            class="mr-2">
                                        {{ $permiso->action }}
                                    </label>
                                @endforeach
                            </div>

                            <!-- Ahora agrupamos por hijos -->
                            @foreach($permisosParent->groupBy(fn($p) => $p->area->parent_id ? $p->area->name : null) as $childName => $permisosChild)
                                @if($childName)
                                    <div class="ml-6 mt-3">
                                        <h4 class="font-semibold text-gray-700">{{ $childName }}</h4>
                                        <div class="grid grid-cols-2 gap-2 mt-1">
                                            @foreach($permisosChild as $permiso)
                                                <label class="flex items-center">
                                                    <input type="checkbox" wire:model="selectedPermissions" value="{{ $permiso->id }}"
                                                        wire:change="applyDependencies({{ $permiso->id }}, $event.target.checked)"
                                                        class="mr-2">
                                                    {{ $permiso->action }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <x-input-error for="selectedPermissions" class="mt-2" />
            </div>


            -- <!-- Permisos existentes -->

            {{--
            <div class="mb-4">
                <x-label value="Permisos por área" />
                <div class="max-h-60 overflow-y-auto mt-1 space-y-4">
                    @foreach($permissions->groupBy('area.name') as $areaName => $permisosArea)
                    <div>
                        <h4 class="font-semibold text-gray-700">{{ $areaName }}</h4>
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            @foreach($permisosArea as $permiso)
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="selectedPermissions" value="{{ $permiso->id }}"
                                    wire:change="applyDependencies({{ $permiso->id }}, $event.target.checked)"
                                    class="mr-2">
                                {{ $permiso->action }}
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                <x-input-error for="selectedPermissions" class="mt-2" />
            </div> --}}




            <!-- Crear nuevo permiso -->
            <div class="px-4  bg-gray-100 sm:p-4 rounded-lg">

                <x-label value="Crear nuevo permiso (opcional)" class="mb-3 font-semibold text-lg" />

                <div class="mb-4">
                    <x-label for="newPermissionName" value="Nombre del permiso" />
                    <div class="flex mt-1">
                        <x-input id="newPermissionName" type="text" class="w-full"
                            wire:model.defer="newPermissionName" />

                    </div>
                    <x-input-error for="newPermissionName" class="mt-2" />
                </div>


                <!-- Seleccionar área para el nuevo permiso -->
                <div class="mb-4">
                    <x-label for="selectedArea" value="Área" />
                    <select id="selectedArea" wire:model.defer="selectedArea"
                        class="w-full border border-gray-300 rounded-lg p-2">
                        <option value="">-- Seleccionar área --</option>
                        @foreach(\App\Models\Area::all() as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="selectedArea" class="mt-2" />
                </div>


                <x-button class="ml-2" wire:click="createPermission">
                    Crear
                </x-button>

            </div>

        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('openCreateRoleModal', false)">
                Cancelar
            </x-secondary-button>

            @if($isEditRole)
                <x-button class="ml-2" wire:click="updateRole">
                    Actualizar Rol
                </x-button>
            @else
                <x-button class="ml-2" wire:click="createRole">
                    Guardar Rol
                </x-button>
            @endif
        </x-slot>
    </x-dialog-modal>
</div>