<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    users: Object,
    slug: String,
    permissions: Object,
    roles: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const role = ref(props.filters?.role || '');

let timeout = null;

const page = usePage();

watch([search, role], ([newSearch, newRole]) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('users.index'), { search: newSearch, role: newRole }, {
             preserveState: true,
             replace: true,
             preserveScroll: true
        });
    }, 300);
});

const confirmDelete = (userId) => {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')) {
        router.delete(route('users.destroy', userId), {
            preserveScroll: true,
            onError: () => alert('Error al eliminar usuario.'),
        });
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });
};
</script>

<template>
    <AppLayout title="Usuarios">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Usuarios
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
                    <!-- Filters -->
                    <div class="flex flex-1 items-center gap-4 w-full md:w-auto">
                        <div class="relative w-full md:w-64">
                            <input v-model="search" type="text" placeholder="Buscar usuarios..." 
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" />
                        </div>
                        <div class="w-full md:w-48">
                            <select v-model="role" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">Todos los roles</option>
                                <option v-for="r in roles" :key="r.id" :value="r.id">
                                    {{ r.display_name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div v-if="permissions.create">
                        <!-- Standard Link to Livewire Create Page -->
                        <Link :href="route('users.create')" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm transition ease-in-out duration-150">
                            Crear nuevo usuario
                        </Link>
                    </div>
                </div>

                <div class="bg-white overflow-hidden sm:rounded-lg">
                    <div class="bg-white shadow-md rounded-lg">
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
                                <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-3 border-r">{{ user.id }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-900">{{ user.name }}</td>
                                    <td class="px-6 py-3">{{ user.email }}</td>
                                    <td class="px-6 py-3">
                                        <div v-if="user.roles && user.roles.length > 0">
                                            <span v-for="role in user.roles" :key="role.id" class="inline-block bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-full mr-1">
                                                {{ role.display_name || role.name }}
                                            </span>
                                        </div>
                                        <span v-else class="text-gray-400 italic">Sin rol</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="h-24 overflow-y-auto custom-scrollbar">
                                            <div v-if="user.roles && user.roles.length > 0">
                                                <div v-for="role in user.roles" :key="role.id + 'perms'">
                                                    <div v-if="role.permissions && role.permissions.length > 0" class="flex flex-wrap gap-1">
                                                        <span v-for="permission in role.permissions" :key="permission.id" class="inline-block bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full whitespace-nowrap">
                                                            {{ permission.name }}
                                                        </span>
                                                    </div>
                                                    <span v-else class="text-gray-400 italic">Sin permisos</span>
                                                </div>
                                            </div>
                                            <span v-else class="text-gray-400 italic">Sin rol</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">{{ formatDate(user.created_at) }}</td>
                                    <td class="px-6 py-3">{{ formatDate(user.updated_at) }}</td>
                                    <td class="px-6 py-3">
                                        <div v-if="user.id !== page.props.auth.user.id" class="flex space-x-2">
                                            <Link v-if="permissions.update" :href="route('users.edit', user.id)" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-xs px-3 py-1.5 mr-2 mx-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                                                Editar
                                            </Link>
                                            
                                            <button v-if="permissions.delete" @click="confirmDelete(user.id)" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg focus:outline-none focus:ring-4 focus:ring-red-300">
                                                Eliminar
                                            </button>
                                        </div>
                                        <span v-else class="text-gray-400 italic">Sin acciones</span>
                                    </td>
                                </tr>
                             </tbody>
                        </table>
                    </div>

                </div>
                
                <!-- Pagination -->
                <div class="mt-6">
                    <Pagination :links="users.links" />
                </div>

            </div>
        </div>
    </AppLayout>
</template>
