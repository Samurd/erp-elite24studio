<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue'; // Or PrimaryButton if you have it
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'; // Assuming this exists or using standard button
import axios from 'axios';

const props = defineProps({
    user: Object,
    roles: Array,
    isEdit: Boolean,
});

const form = useForm({
    name: props.user ? props.user.name : '',
    email: props.user ? props.user.email : '',
    role: props.user && props.user.roles && props.user.roles.length ? props.user.roles[0].id : '',
    password: '',
});

// --- Role Management State ---
const localRoles = ref([...props.roles]); // Local copy to update after create/edit
const localPermissions = ref([]); // Now initialized empty
const arePermissionsLoaded = ref(false);

const openCreateRoleModal = ref(false);
const isEditRole = ref(false);
const roleId = ref(null);
const roleName = ref('');
const selectedPermissions = ref([]);
const newPermissionName = ref('');
const selectedArea = ref('');
const roleErrors = ref({}); // For manual error handling from axios
const permissionErrors = ref({});
const isLoadingRole = ref(false);

// --- Computed Properties for Permission Grouping ---
// Replicating: $permissions->groupBy(fn($p) => $p->area->parent?->name ?? $p->area->name)
const groupedPermissions = computed(() => {
    const groups = {};
    
    localPermissions.value.forEach(p => {
        const area = p.area;
        if (!area) return;
        
        const parentName = area.parent ? area.parent.name : area.name;
        
        if (!groups[parentName]) {
            groups[parentName] = {
                parentPermissions: [], // Permissions belonging directly to this parent area (no sub-area or is the parent)
                children: {} // Sub-areas
            };
        }

        if (!area.parent_id) {
            // Direct parent area permissions
            groups[parentName].parentPermissions.push(p);
        } else {
            // Child area permissions
            const childName = area.name;
            if (!groups[parentName].children[childName]) {
                groups[parentName].children[childName] = [];
            }
            groups[parentName].children[childName].push(p);
        }
    });
    return groups;
});

const areas = computed(() => {
    // Unique areas for the dropdown
    const uniqueAreas = new Map();
    localPermissions.value.forEach(p => {
        if(p.area) uniqueAreas.set(p.area.id, p.area);
    });
    return Array.from(uniqueAreas.values());
});


const submit = () => {
    if (props.isEdit) {
        form.put(route('users.update', props.user.id), {
            onSuccess: () => form.reset('password'),
        });
    } else {
        form.post(route('users.store'), {
            onSuccess: () => form.reset('password'),
        });
    }
};

// --- Role Logic ---

const fetchPermissions = async () => {
    if (arePermissionsLoaded.value) return;
    
    try {
        const response = await axios.get(route('users.permissions.index'));
        localPermissions.value = response.data;
        arePermissionsLoaded.value = true;
    } catch (error) {
        console.error("Error fetching permissions", error);
        alert('Error al cargar permisos.');
    }
};

const openNewRoleModal = async () => {
    isEditRole.value = false;
    roleId.value = null;
    roleName.value = '';
    selectedPermissions.value = [];
    roleErrors.value = {};
    permissionErrors.value = {};
    openCreateRoleModal.value = true;
    
    // Load in background
    isLoadingRole.value = true;
    await fetchPermissions(); 
    isLoadingRole.value = false;
};

const openEditRoleModal = async () => {
    if (!form.role) return;

    isEditRole.value = true; // Set mode immediately
    openCreateRoleModal.value = true; // Show modal immediately
    isLoadingRole.value = true; // Show spinner
    roleErrors.value = {};
    permissionErrors.value = {};

    try {
        // Parallel fetch
        const [permRes, roleRes] = await Promise.all([
             fetchPermissions(),
             axios.get(route('users.roles.get', form.role))
        ]);
        
        // fetchPermissions handles localPermissions update
        // roleRes is the specific role data
        
        const role = roleRes.data;
        
        roleId.value = role.id;
        roleName.value = role.display_name || role.name;
        selectedPermissions.value = role.permissions.map(p => p.id);
        
    } catch (error) {
        console.error("Error fetching role", error);
        alert('Error al cargar el rol.');
        openCreateRoleModal.value = false; // Close on critical error
    } finally {
        isLoadingRole.value = false;
    }
};

const saveRole = async () => {
    roleErrors.value = {};
    
    try {
        let response;
        if (isEditRole.value) {
            response = await axios.put(route('users.roles.update', roleId.value), {
                roleName: roleName.value,
                selectedPermissions: selectedPermissions.value
            });
             
             // Update local list
            const idx = localRoles.value.findIndex(r => r.id === roleId.value);
            if (idx !== -1) {
                localRoles.value[idx] = response.data.role;
            }

            alert('Rol actualizado con éxito');

        } else {
            response = await axios.post(route('users.roles.store'), {
                roleName: roleName.value,
                selectedPermissions: selectedPermissions.value
            });
            
            localRoles.value.push(response.data.role);
            form.role = response.data.role.id; // Auto select new role
            alert('Rol creado con éxito');
        }

        openCreateRoleModal.value = false;
        
    } catch (error) {
        if (error.response && error.response.status === 422) {
            roleErrors.value = error.response.data.errors;
        } else {
            console.error(error);
            alert('Ocurrió un error al guardar el rol.');
        }
    }
};

// --- Permission Logic ---
const createPermission = async () => {
    permissionErrors.value = {};
    if (!newPermissionName.value || !selectedArea.value) {
        // Simple client check, mostly reliant on server validation
    }

    try {
        const response = await axios.post(route('users.permissions.store'), {
             newPermissionName: newPermissionName.value,
             selectedArea: selectedArea.value
        });
        
        localPermissions.value = response.data.permissions; // Refresh all permissions
        // Auto select the new permission
        selectedPermissions.value.push(response.data.permission.id);
        
        newPermissionName.value = '';
        // selectedArea.value = ''; // Keep area selected for convenience?
        alert('Permiso creado.');

    } catch (error) {
        if (error.response && error.response.status === 422) {
            // Map validation errors to our separate permission error bag or general
             permissionErrors.value = error.response.data.errors;
        } else {
            console.error(error);
            alert('Error al crear permiso.');
        }
    }
};


// --- Dependency Logic (Ported from PHP) ---
const applyDependencies = (permissionId, isChecked) => {
    // Find permission object
    const permission = localPermissions.value.find(p => p.id === permissionId);
    if (!permission) return;

    if (isChecked) {
        // If checking create/update/delete -> ensure 'view' is checked
        if (['create', 'update', 'delete'].includes(permission.action)) {
            const viewPermission = localPermissions.value.find(p => 
                p.area_id === permission.area_id && p.action === 'view'
            );

            if (viewPermission && !selectedPermissions.value.includes(viewPermission.id)) {
                selectedPermissions.value.push(viewPermission.id);
            }
        }
    } else {
        // If unchecking 'view' -> uncheck create/update/delete
        if (permission.action === 'view') {
           const dependentPermissions = localPermissions.value.filter(p => 
                p.area_id === permission.area_id && 
                ['create', 'update', 'delete'].includes(p.action)
           ).map(p => p.id);
           
           // Remove all dependents from selectedPermissions
           selectedPermissions.value = selectedPermissions.value.filter(id => !dependentPermissions.includes(id));
        }
    }
};
</script>

<template>
    <AppLayout :title="isEdit ? 'Editar Usuario' : 'Crear Usuario'">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ isEdit ? 'Editar Usuario' : 'Crear Nuevo Usuario' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Flash Message (Handled by Layout usually, but adding redundant check if needed or relying on page props) -->
                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    
                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Name -->
                        <div>
                            <InputLabel for="name" value="Nombre" />
                            <TextInput id="name" type="text" v-model="form.name" class="mt-1 block w-full" required autofocus />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div>
                            <InputLabel for="email" value="Correo Electrónico" />
                            <TextInput id="email" type="email" v-model="form.email" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div>
                            <InputLabel for="role" value="Rol" />
                            <select id="role" v-model="form.role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-500 focus:ring-opacity-50" required>
                                <option value="" disabled>-- Seleccionar --</option>
                                <option v-for="role in localRoles" :key="role.id" :value="role.id">
                                    {{ role.display_name || role.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.role" class="mt-2" />
                            
                            <div class="mt-2 flex gap-2">
                                <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline" @click="openNewRoleModal">
                                    Crear Rol
                                </button>
                                <button type="button" v-if="form.role" class="text-sm text-yellow-600 hover:text-yellow-900 underline" @click="openEditRoleModal">
                                    Editar Rol
                                </button>
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <InputLabel for="password" :value="isEdit ? 'Nueva Contraseña (Opcional)' : 'Contraseña'" />
                            <TextInput id="password" type="password" v-model="form.password" class="mt-1 block w-full" :required="!isEdit" />
                            <p v-if="isEdit" class="text-xs text-gray-500 mt-1">Déjala en blanco para mantener la actual.</p>
                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>

                        <!-- Actions -->
                        <div class="col-span-1 md:col-span-2 flex justify-end items-center mt-4">
                            <Link :href="route('users.index')" class="text-gray-600 hover:text-gray-900 mr-4">
                                Cancelar
                            </Link>

                            <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" :disabled="form.processing">
                                <span v-if="form.processing">Guardando...</span>
                                <span v-else>{{ isEdit ? 'Actualizar Usuario' : 'Guardar Usuario' }}</span>
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <!-- Role Modal -->
        <DialogModal :show="openCreateRoleModal" @close="openCreateRoleModal = false">
            <template #title>
                {{ isEditRole ? 'Editar rol' : 'Crear nuevo rol' }}
            </template>

            <template #content>
                 <div v-if="isLoadingRole" class="flex justify-center items-center py-12">
                     <i class="fas fa-circle-notch fa-spin text-4xl text-yellow-500"></i>
                 </div>
                 <div v-else>
                     <!-- Role Name -->
                    <div class="mb-4">
                        <InputLabel for="roleName" value="Nombre del rol" />
                        <TextInput id="roleName" type="text" class="mt-1 block w-full" v-model="roleName" />
                        <InputError :message="roleErrors.roleName && roleErrors.roleName[0]" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <InputLabel value="Permisos por área" />
                        <div class="max-h-60 overflow-y-auto mt-1 space-y-4 pr-2 custom-scrollbar">
                            
                            <!-- Loop through grouped permissions -->
                            <div v-for="(group, parentName) in groupedPermissions" :key="parentName">
                                <h3 class="font-bold text-gray-900">{{ parentName }}</h3>

                                <!-- Parent Area Direct Permissions -->
                                <div class="grid grid-cols-2 gap-2 mt-1 ml-2" v-if="group.parentPermissions.length > 0">
                                    <label v-for="permiso in group.parentPermissions" :key="permiso.id" class="flex items-center">
                                        <input type="checkbox" :value="permiso.id" v-model="selectedPermissions" 
                                            @change="applyDependencies(permiso.id, $event.target.checked)"
                                            class="mr-2 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        {{ permiso.action }}
                                    </label>
                                </div>

                                <!-- Children Areas -->
                                <div v-for="(childrenPerms, childName) in group.children" :key="childName" class="ml-6 mt-3">
                                    <h4 class="font-semibold text-gray-700">{{ childName }}</h4>
                                    <div class="grid grid-cols-2 gap-2 mt-1">
                                        <label v-for="permiso in childrenPerms" :key="permiso.id" class="flex items-center">
                                            <input type="checkbox" :value="permiso.id" v-model="selectedPermissions" 
                                            @change="applyDependencies(permiso.id, $event.target.checked)"
                                            class="mr-2 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                            {{ permiso.action }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <InputError :message="roleErrors.selectedPermissions && roleErrors.selectedPermissions[0]" class="mt-2" />
                    </div>

                    <!-- Create New Permission (Optional) -->
                    <div class="px-4 bg-gray-100 sm:p-4 rounded-lg mt-6">
                        <InputLabel value="Crear nuevo permiso (opcional)" class="mb-3 font-semibold text-lg" />
                        
                        <div class="mb-4">
                            <InputLabel for="newPermissionName" value="Nombre del permiso" />
                            <div class="flex mt-1">
                                <TextInput id="newPermissionName" type="text" class="w-full" v-model="newPermissionName" />
                            </div>
                            <InputError :message="permissionErrors.newPermissionName && permissionErrors.newPermissionName[0]" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <InputLabel for="selectedArea" value="Área" />
                            <select id="selectedArea" v-model="selectedArea" class="w-full border border-gray-300 rounded-lg p-2 mt-1">
                                <option value="">-- Seleccionar área --</option>
                                <option v-for="area in areas" :key="area.id" :value="area.id">{{ area.name }}</option>
                            </select>
                            <InputError :message="permissionErrors.selectedArea && permissionErrors.selectedArea[0]" class="mt-2" />
                        </div>

                        <button type="button" @click="createPermission" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">
                            Crear
                        </button>
                    </div>
                 </div>
            </template>

            <template #footer>
                <SecondaryButton @click="openCreateRoleModal = false">
                    Cancelar
                </SecondaryButton>

                <button class="ml-2 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" 
                        @click="saveRole"
                        :disabled="isLoadingRole">
                    {{ isEditRole ? 'Actualizar Rol' : 'Guardar Rol' }}
                </button>
            </template>
        </DialogModal>

    </AppLayout>
</template>

