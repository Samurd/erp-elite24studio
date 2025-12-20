<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'

const props = defineProps({
    kit: Object, // Null if creating
    users: Array,
    statusOptions: Array,
})

const isEditing = computed(() => !!props.kit)

const form = useForm({
    requested_by_user_id: props.kit?.requested_by_user_id || '',
    position_area: props.kit?.position_area || '',
    recipient_name: props.kit?.recipient_name || '',
    recipient_role: props.kit?.recipient_role || '',
    kit_type: props.kit?.kit_type || '',
    kit_contents: props.kit?.kit_contents || '',
    request_date: props.kit?.request_date ? props.kit.request_date.substring(0, 10) : new Date().toISOString().substring(0, 10),
    delivery_date: props.kit?.delivery_date ? props.kit.delivery_date.substring(0, 10) : '',
    status_id: props.kit?.status_id || '',
    delivery_responsible_user_id: props.kit?.delivery_responsible_user_id || '',
    observations: props.kit?.observations || '',
})

const submit = () => {
    if (isEditing.value) {
        form.put(route('rrhh.kits.update', props.kit.id))
    } else {
        form.post(route('rrhh.kits.store'))
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Kit' : 'Nuevo Kit'">
        <div class="max-w-4xl mx-auto p-6">
             <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEditing ? 'Editar Kit' : 'Nuevo Kit' }}
                        </h1>
                        <p class="text-gray-600 mt-1">Detalles de la entrega de material</p>
                    </div>
                    <Link
                        :href="route('rrhh.kits.index')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <form @submit.prevent="submit">
                    <!-- Recipient Info -->
                     <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Información del Destinatario</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Destinatario <span class="text-red-500">*</span></label>
                                <input
                                    v-model="form.recipient_name"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                />
                                <span v-if="form.errors.recipient_name" class="text-red-500 text-sm">{{ form.errors.recipient_name }}</span>
                            </div>

                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cargo del Destinatario <span class="text-red-500">*</span></label>
                                <input
                                    v-model="form.recipient_role"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                />
                                <span v-if="form.errors.recipient_role" class="text-red-500 text-sm">{{ form.errors.recipient_role }}</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Área / Posición <span class="text-red-500">*</span></label>
                                <input
                                    v-model="form.position_area"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                />
                                <span v-if="form.errors.position_area" class="text-red-500 text-sm">{{ form.errors.position_area }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Kit Details -->
                     <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Detalles del Kit</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Kit</label>
                                <input
                                    v-model="form.kit_type"
                                    type="text"
                                    placeholder="Ej. Kit de Bienvenida, EPPs"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                />
                                <span v-if="form.errors.kit_type" class="text-red-500 text-sm">{{ form.errors.kit_type }}</span>
                            </div>

                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contenido</label>
                                <textarea
                                    v-model="form.kit_contents"
                                    rows="2"
                                    placeholder="Detalle de elementos entregados..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                ></textarea>
                                <span v-if="form.errors.kit_contents" class="text-red-500 text-sm">{{ form.errors.kit_contents }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Process Info -->
                     <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Información del Proceso</h3>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Solicitado Por</label>
                                <select
                                    v-model="form.requested_by_user_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                >
                                    <option value="">Seleccionar usuario</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                                <span v-if="form.errors.requested_by_user_id" class="text-red-500 text-sm">{{ form.errors.requested_by_user_id }}</span>
                            </div>

                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Solicitud <span class="text-red-500">*</span></label>
                                <input
                                    v-model="form.request_date"
                                    type="date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                />
                                <span v-if="form.errors.request_date" class="text-red-500 text-sm">{{ form.errors.request_date }}</span>
                            </div>

                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Entrega</label>
                                <input
                                    v-model="form.delivery_date"
                                    type="date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                />
                                <span v-if="form.errors.delivery_date" class="text-red-500 text-sm">{{ form.errors.delivery_date }}</span>
                            </div>

                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Responsable de Entrega</label>
                                <select
                                    v-model="form.delivery_responsible_user_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                >
                                    <option value="">Seleccionar responsable</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                                <span v-if="form.errors.delivery_responsible_user_id" class="text-red-500 text-sm">{{ form.errors.delivery_responsible_user_id }}</span>
                            </div>

                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                <select
                                    v-model="form.status_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                >
                                    <option value="">Seleccionar estado</option>
                                    <option v-for="status in statusOptions" :key="status.id" :value="status.id">
                                        {{ status.name }}
                                    </option>
                                </select>
                                <span v-if="form.errors.status_id" class="text-red-500 text-sm">{{ form.errors.status_id }}</span>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                                <textarea
                                    v-model="form.observations"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                ></textarea>
                                <span v-if="form.errors.observations" class="text-red-500 text-sm">{{ form.errors.observations }}</span>
                            </div>
                        </div>
                     </div>


                    <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                        <Link
                            :href="route('rrhh.kits.index')"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50"
                        >
                            {{ isEditing ? 'Actualizar Kit' : 'Guardar Kit' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
