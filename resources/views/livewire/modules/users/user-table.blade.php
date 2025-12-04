<div>
   <div class="overflow-x-auto bg-white shadow-md rounded-lg">
      <table class="min-w-full table-fixed text-sm text-left text-gray-600">
         <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold">
            <tr>
               <th class="px-6 py-3 border-r">ID</th>
               <th class="px-6 py-3">Nombre</th>
               <th class="px-6 py-3">Correo</th>
               <th class="px-6 py-3">Rol</th>
               <th class="px-6 py-3">Permisos</th>
               <th class="px-6 py-3">Creado</th>
               <th class="px-6 py-3">Actualizado</th>
               <th class="px-6 py-3">Acciones</th>
            </tr>
         </thead>
         <tbody class="divide-y divide-gray-200">
            @foreach($users as $user)
               <tr class="hover:bg-gray-50">
                  <td class="px-6 py-3 border-r">{{ $user->id }}</td>
                  <td class="px-6 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                  <td class="px-6 py-3">{{ $user->email }}</td>
                  <td class="px-6 py-3">
                     @if($user->roles->count() > 0)
                        @foreach($user->roles as $role)
                           <span class="inline-block bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full">
                              {{ $role->display_name ?? $role->name }}
                           </span>
                        @endforeach
                     @else
                        <span class="text-gray-400 italic">Sin rol</span>
                     @endif
                  </td>
                  <td class="px-6 py-3">
                     @if($user->roles->count() > 0)
                        @foreach($user->roles as $role)
                           @if($role->permissions->count() > 0)
                              @foreach($role->permissions as $permission)
                                 <span class="inline-block bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">
                                    {{ $permission->name }}
                                 </span>
                              @endforeach
                           @else
                              <span class="text-gray-400 italic">Sin permisos</span>
                           @endif
                        @endforeach
                     @else
                        <span class="text-gray-400 italic">Sin rol</span>
                     @endif
                  </td>
                  <td class="px-6 py-3">{{ $user->created_at->format('d/m/Y h:i A') }}</td>
                  <td class="px-6 py-3">{{ $user->updated_at->format('d/m/Y h:i A') }}</td>
                  <td class="px-6 py-3 ">
                     @if($user->id !== auth()->id())
                        @canArea('update', $slug)
                        <x-button>
                           <a href="{{ route('users.edit', ['user' => $user->id]) }}">
                              Editar
                           </a>
                        </x-button>
                        @endCanArea
                        @canArea('delete', $slug)
                        <x-button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded"
                           wire:click="confirmUserDeletion({{ $user->id }})">
                           Eliminar
                        </x-button>
                        @endCanArea
                     @else
                        <span class="text-gray-400 italic">Sin acciones</span>
                     @endif
                  </td>
               </tr>
            @endforeach
         </tbody>
      </table>
   </div>




   <x-confirmation-modal wire:model="confirmingUserDeletion">
      <x-slot name="title">
         Eliminar Usuario
      </x-slot>

      <x-slot name="content">
         ¿Estás seguro de que deseas eliminar este usuario?
         Esta acción no se puede deshacer.
      </x-slot>

      <x-slot name="footer">
         <x-secondary-button wire:click="$set('confirmingUserDeletion', false)" wire:loading.attr="disabled">
            Cancelar
         </x-secondary-button>

         <x-danger-button class="ml-3" wire:click="deleteUser" wire:loading.attr="disabled">
            Eliminar
         </x-danger-button>
      </x-slot>
   </x-confirmation-modal>


</div>