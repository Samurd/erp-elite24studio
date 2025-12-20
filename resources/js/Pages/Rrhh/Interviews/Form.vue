<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { computed } from 'vue'

const props = defineProps({
    interview: Object, // Null if creating
    statusOptions: Array,
    interviewTypeOptions: Array,
    resultOptions: Array,
    interviewerOptions: Array,
    applicants: Array,
})

const isEditing = computed(() => !!props.interview)

const form = useForm({
    applicant_id: props.interview?.applicant_id || '',
    date: props.interview?.date ? props.interview.date.substring(0, 10) : new Date().toISOString().substring(0, 10),
    time: props.interview?.time ? props.interview.time.substring(0, 5) : '',
    interviewer_id: props.interview?.interviewer_id || '',
    interview_type_id: props.interview?.interview_type_id || '',
    status_id: props.interview?.status_id || '',
    result_id: props.interview?.result_id || '',
    platform: props.interview?.platform || '',
    platform_url: props.interview?.platform_url || '',
    expected_results: props.interview?.expected_results || '',
    interviewer_observations: props.interview?.interviewer_observations || '',
    rating: props.interview?.rating || 0,
})

const submit = () => {
    if (isEditing.value) {
        form.put(route('rrhh.interviews.update', props.interview.id))
    } else {
        form.post(route('rrhh.interviews.store'))
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Editar Entrevista' : 'Agendar Entrevista'">
        <div class="max-w-4xl mx-auto p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ isEditing ? 'Editar Entrevista' : 'Agendar Entrevista' }}
                        </h1>
                        <p class="text-gray-600 mt-1">Detalles de la sesión de entrevista</p>
                    </div>
                    <Link
                        :href="route('rrhh.interviews.index')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <form @submit.prevent="submit">
                    <!-- General Info -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Información General</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Postulante <span class="text-red-500">*</span></label>
                                <select
                                    v-model="form.applicant_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                >
                                    <option value="">Seleccionar postulante</option>
                                    <option v-for="applicant in applicants" :key="applicant.id" :value="applicant.id">
                                        {{ applicant.full_name }}
                                    </option>
                                </select>
                                <span v-if="form.errors.applicant_id" class="text-red-500 text-sm">{{ form.errors.applicant_id }}</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha <span class="text-red-500">*</span></label>
                                <input
                                    v-model="form.date"
                                    type="date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                />
                                <span v-if="form.errors.date" class="text-red-500 text-sm">{{ form.errors.date }}</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hora</label>
                                <input
                                    v-model="form.time"
                                    type="time"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                />
                                <span v-if="form.errors.time" class="text-red-500 text-sm">{{ form.errors.time }}</span>
                            </div>

                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Entrevistador</label>
                                <select
                                    v-model="form.interviewer_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                >
                                    <option value="">Seleccionar responsable</option>
                                    <option v-for="user in interviewerOptions" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                                <span v-if="form.errors.interviewer_id" class="text-red-500 text-sm">{{ form.errors.interviewer_id }}</span>
                            </div>

                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Entrevista</label>
                                <select
                                    v-model="form.interview_type_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                >
                                    <option value="">Seleccionar tipo</option>
                                    <option v-for="type in interviewTypeOptions" :key="type.id" :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </select>
                                <span v-if="form.errors.interview_type_id" class="text-red-500 text-sm">{{ form.errors.interview_type_id }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Details -->
                     <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Detalles de la Conexión</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Plataforma (ej. Google Meet)</label>
                                <input
                                    v-model="form.platform"
                                    type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                />
                                <span v-if="form.errors.platform" class="text-red-500 text-sm">{{ form.errors.platform }}</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">URL de Reunión</label>
                                <input
                                    v-model="form.platform_url"
                                    type="url"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                />
                                <span v-if="form.errors.platform_url" class="text-red-500 text-sm">{{ form.errors.platform_url }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Results -->
                     <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Resultados y Evaluación</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Resultado</label>
                                <select
                                    v-model="form.result_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                >
                                    <option value="">Seleccionar resultado</option>
                                    <option v-for="result in resultOptions" :key="result.id" :value="result.id">
                                        {{ result.name }}
                                    </option>
                                </select>
                                <span v-if="form.errors.result_id" class="text-red-500 text-sm">{{ form.errors.result_id }}</span>
                            </div>

                             <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Calificación (0-10)</label>
                                <input
                                    v-model="form.rating"
                                    type="number"
                                    min="0"
                                    max="10"
                                    step="0.1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                />
                                <span v-if="form.errors.rating" class="text-red-500 text-sm">{{ form.errors.rating }}</span>
                            </div>

                             <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                                <textarea
                                    v-model="form.interviewer_observations"
                                    rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                ></textarea>
                                <span v-if="form.errors.interviewer_observations" class="text-red-500 text-sm">{{ form.errors.interviewer_observations }}</span>
                            </div>
                        </div>
                    </div>


                    <div class="flex justify-end gap-3 mt-8 border-t pt-6">
                        <Link
                            :href="route('rrhh.interviews.index')"
                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50"
                        >
                            {{ isEditing ? 'Actualizar Entrevista' : 'Guardar Entrevista' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
