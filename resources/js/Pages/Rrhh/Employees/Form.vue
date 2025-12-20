<script setup>
import { useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue'
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue'
import { computed, ref } from 'vue'

const props = defineProps({
    employee: Object, // Null if creating
    departments: Array,
    genderOptions: Array,
    educationOptions: Array,
    maritalStatusOptions: Array,
})

const isEditing = computed(() => !!props.employee)
const activeTab = ref('work') // tabs: work, private, contact, education, files

const form = useForm({
    // Work
    full_name: props.employee?.full_name || '',
    job_title: props.employee?.job_title || '',
    work_email: props.employee?.work_email || '',
    mobile_phone: props.employee?.mobile_phone || '',
    work_address: props.employee?.work_address || '',
    work_schedule: props.employee?.work_schedule || '40 hours/week',
    department_id: props.employee?.department_id || '',

    // Private
    home_address: props.employee?.home_address || '',
    personal_email: props.employee?.personal_email || '',
    private_phone: props.employee?.private_phone || '',
    bank_account: props.employee?.bank_account || '',
    identification_number: props.employee?.identification_number || '',
    social_security_number: props.employee?.social_security_number || '',
    passport_number: props.employee?.passport_number || '',
    gender_id: props.employee?.gender_id || '',
    birth_date: props.employee?.birth_date ? props.employee.birth_date.substring(0, 10) : '',
    birth_place: props.employee?.birth_place || '',
    birth_country: props.employee?.birth_country || '',
    has_disability: props.employee?.has_disability || false,
    disability_details: props.employee?.disability_details || '',

    // Contact
    emergency_contact_name: props.employee?.emergency_contact_name || '',
    emergency_contact_phone: props.employee?.emergency_contact_phone || '',

    // Education & Family
    education_type_id: props.employee?.education_type_id || '',
    marital_status_id: props.employee?.marital_status_id || '',
    number_of_dependents: props.employee?.number_of_dependents || 0,

    // Files
    files: [],
    pending_file_ids: [],
})

const submit = () => {
    if (Object.keys(form.errors).length > 0) {
        // Simple alert or find first error to switch tab could be added here
    }

    if (isEditing.value) {
        form.put(route('rrhh.employees.update', props.employee.id))
    } else {
        form.post(route('rrhh.employees.store'), {
            forceFormData: true,
        })
    }
}

const deleteEmployee = () => {
    if (confirm('¿Estás seguro de que deseas eliminar este empleado?')) {
        router.delete(route('rrhh.employees.destroy', props.employee.id))
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Empleado' : 'Nuevo Empleado'">
        <div class="max-w-7xl mx-auto p-6">
            <!-- Header -->
             <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEditing ? 'Editar Empleado' : 'Nuevo Empleado' }}
                        </h1>
                        <p class="text-gray-600 mt-1">Diligencie la información personal y laboral</p>
                    </div>
                    <div class="flex gap-2">
                        <Link
                            :href="route('rrhh.employees.index')"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            <i class="fas fa-arrow-left mr-2"></i>Volver
                        </Link>
                         <button
                            v-if="isEditing"
                            type="button"
                            @click="deleteEmployee"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                        >
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-6 border-b border-gray-200 bg-white rounded-t-lg px-4 pt-4">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'work'" :class="[activeTab === 'work' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']">
                        Laboral
                    </button>
                    <button @click="activeTab = 'private'" :class="[activeTab === 'private' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']">
                        Privada & Documentos
                    </button>
                    <button @click="activeTab = 'contact'" :class="[activeTab === 'contact' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']">
                        Contacto Emergencia
                    </button>
                    <button @click="activeTab = 'education'" :class="[activeTab === 'education' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']">
                        Educación & Familia
                    </button>
                     <button @click="activeTab = 'files'" :class="[activeTab === 'files' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']">
                        Archivos
                    </button>
                </nav>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="bg-white rounded-b-lg rounded-tr-lg shadow-sm p-6 mb-6">
                
                <!-- Work Section -->
                <div v-show="activeTab === 'work'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 text-lg font-medium text-gray-800 border-b pb-2 mb-2">Información Laboral</div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo <span class="text-red-500">*</span></label>
                        <input v-model="form.full_name" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                        <div v-if="form.errors.full_name" class="text-red-500 text-sm mt-1">{{ form.errors.full_name }}</div>
                    </div>

                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cargo <span class="text-red-500">*</span></label>
                        <input v-model="form.job_title" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                         <div v-if="form.errors.job_title" class="text-red-500 text-sm mt-1">{{ form.errors.job_title }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Trabajo <span class="text-red-500">*</span></label>
                        <input v-model="form.work_email" type="email" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                         <div v-if="form.errors.work_email" class="text-red-500 text-sm mt-1">{{ form.errors.work_email }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Celular Móvil <span class="text-red-500">*</span></label>
                        <input v-model="form.mobile_phone" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                         <div v-if="form.errors.mobile_phone" class="text-red-500 text-sm mt-1">{{ form.errors.mobile_phone }}</div>
                    </div>

                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departamento <span class="text-red-500">*</span></label>
                        <select v-model="form.department_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                            <option value="">Seleccionar departamento</option>
                            <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                        </select>
                         <div v-if="form.errors.department_id" class="text-red-500 text-sm mt-1">{{ form.errors.department_id }}</div>
                    </div>

                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Horario <span class="text-red-500">*</span></label>
                        <input v-model="form.work_schedule" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                         <div v-if="form.errors.work_schedule" class="text-red-500 text-sm mt-1">{{ form.errors.work_schedule }}</div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección Trabajo <span class="text-red-500">*</span></label>
                        <input v-model="form.work_address" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                         <div v-if="form.errors.work_address" class="text-red-500 text-sm mt-1">{{ form.errors.work_address }}</div>
                    </div>
                </div>

                <!-- Private Section -->
                <div v-show="activeTab === 'private'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 text-lg font-medium text-gray-800 border-b pb-2 mb-2">Información Privada</div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número Identificación <span class="text-red-500">*</span></label>
                        <input v-model="form.identification_number" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                        <div v-if="form.errors.identification_number" class="text-red-500 text-sm mt-1">{{ form.errors.identification_number }}</div>
                    </div>

                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número Pasaporte</label>
                        <input v-model="form.passport_number" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Personal</label>
                        <input v-model="form.personal_email" type="email" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono Privado</label>
                        <input v-model="form.private_phone" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                    </div>

                     <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección Hogar</label>
                        <input v-model="form.home_address" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                    </div>

                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cuenta Bancaria</label>
                        <input v-model="form.bank_account" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Seguridad Social / EPS</label>
                        <input v-model="form.social_security_number" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Género</label>
                         <select v-model="form.gender_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                            <option value="">Seleccionar</option>
                            <option v-for="g in genderOptions" :key="g.id" :value="g.id">{{ g.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Nacimiento</label>
                        <input v-model="form.birth_date" type="date" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                         <div v-if="form.errors.birth_date" class="text-red-500 text-sm mt-1">{{ form.errors.birth_date }}</div>
                    </div>

                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lugar Nacimiento</label>
                        <input v-model="form.birth_place" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">País Nacimiento</label>
                        <input v-model="form.birth_country" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center space-x-2">
                            <input v-model="form.has_disability" type="checkbox" class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500">
                            <span class="text-sm font-medium text-gray-700">Tiene Discapacidad</span>
                        </label>
                    </div>

                    <div v-if="form.has_disability" class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Detalles Discapacidad</label>
                        <textarea v-model="form.disability_details" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500"></textarea>
                         <div v-if="form.errors.disability_details" class="text-red-500 text-sm mt-1">{{ form.errors.disability_details }}</div>
                    </div>
                </div>

                <!-- Contact Section -->
                 <div v-show="activeTab === 'contact'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 text-lg font-medium text-gray-800 border-b pb-2 mb-2">Contacto de Emergencia</div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Contacto <span class="text-red-500">*</span></label>
                        <input v-model="form.emergency_contact_name" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                         <div v-if="form.errors.emergency_contact_name" class="text-red-500 text-sm mt-1">{{ form.errors.emergency_contact_name }}</div>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono Contacto <span class="text-red-500">*</span></label>
                        <input v-model="form.emergency_contact_phone" type="text" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                         <div v-if="form.errors.emergency_contact_phone" class="text-red-500 text-sm mt-1">{{ form.errors.emergency_contact_phone }}</div>
                    </div>
                 </div>

                 <!-- Education & Family Section -->
                 <div v-show="activeTab === 'education'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 text-lg font-medium text-gray-800 border-b pb-2 mb-2">Educación y Familia</div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nivel Educativo</label>
                        <select v-model="form.education_type_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                             <option value="">Seleccionar</option>
                            <option v-for="e in educationOptions" :key="e.id" :value="e.id">{{ e.name }}</option>
                        </select>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado Civil</label>
                        <select v-model="form.marital_status_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500">
                             <option value="">Seleccionar</option>
                            <option v-for="m in maritalStatusOptions" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número de Dependientes</label>
                        <input v-model="form.number_of_dependents" type="number" min="0" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-yellow-500 focus:border-yellow-500" />
                    </div>
                 </div>

                 <!-- Files Section -->
                 <div v-show="activeTab === 'files'">
                    <div class="text-lg font-medium text-gray-800 border-b pb-2 mb-4">Documentos</div>
                     <ModelAttachmentsCreator 
                        v-if="!isEditing"
                        model-type="App\Models\Employee"
                        area-slug="rrhh"
                        v-model:files="form.files"
                        v-model:pendingFileIds="form.pending_file_ids"
                    />
                    <ModelAttachments 
                        v-else
                        :model-id="employee.id"
                        model-type="App\Models\Employee"
                        area-slug="rrhh"
                        :files="employee.files" 
                    />
                 </div>

                <!-- Footer / Buttons -->
                <div class="flex justify-end mt-8 pt-4 border-t">
                     <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50"
                    >
                        {{ isEditing ? 'Actualizar Información' : 'Guardar Empleado' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
