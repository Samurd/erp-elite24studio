<script setup>
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'

const props = defineProps({
    audits: Object,
    auditTypes: Array,
    filters: Object,
})

const search = ref(props.filters.search || '')
const perPage = ref(props.filters.perPage || 10)
const activeTab = ref(props.filters.activeTab || '')

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('es-CO')
}

const setActiveTab = (slug) => {
    activeTab.value = slug
    applyFilters()
}

const applyFilters = () => {
    router.get(route('finances.audits.index'), {
        search: search.value,
        perPage: perPage.value,
        activeTab: activeTab.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}

const deleteAudit = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta auditoría?')) {
        router.delete(route('finances.audits.destroy', id))
    }
}
</script>

<template>
    <AppLayout title="Auditorías">
        <div class="p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Auditorías</h1>
                        <p class="text-gray-600 mt-1">Gestión de auditorías y controles</p>
                    </div>
                    <Link
                        :href="route('finances.audits.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors"
                    >
                        <i class="fas fa-plus mr-2"></i>Nueva Auditoría
                    </Link>
                </div>
            </div>

            <!-- Tabs -->
            <div v-if="auditTypes.length > 0" class="bg-white rounded-lg shadow-sm mb-6">
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button
                            v-for="type in auditTypes"
                            :key="type.slug"
                            @click="setActiveTab(type.slug)"
                            :class="[
                                'px-6 py-3 text-sm font-medium border-b-2 transition-colors',
                                activeTab === type.slug
                                    ? 'border-yellow-600 text-yellow-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            {{ type.name }}
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Búsqueda -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Búsqueda</label>
                        <input
                            v-model="search"
                            @keyup.enter="applyFilters"
                            type="text"
                            placeholder="Buscar por objetivo o lugar..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        />
                    </div>

                    <!-- Registros por página -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Registros por página</label>
                        <select
                            v-model="perPage"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        >
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Objetivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lugar</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Auditoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="audit in audits.data" :key="audit.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">#{{ audit.id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ audit.objective }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ audit.place }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="audit.type" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ audit.type.name }}
                                    </span>
                                    <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="audit.status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ audit.status.name }}
                                    </span>
                                    <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ formatDate(audit.date_audit) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ audit.files?.length || 0 }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <Link
                                            :href="route('finances.audits.show', audit.id)"
                                            class="text-gray-600 hover:text-gray-900"
                                            title="Ver"
                                        >
                                            <i class="fa-solid fa-eye mr-1"></i> Ver
                                        </Link>
                                        <Link
                                            :href="route('finances.audits.edit', audit.id)"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Editar"
                                        >
                                            <i class="fa-solid fa-pen-to-square mr-1"></i> Editar
                                        </Link>
                                        <button
                                            @click="deleteAudit(audit.id)"
                                            class="text-red-600 hover:text-red-900"
                                            title="Eliminar"
                                        >
                                            <i class="fa-solid fa-trash mr-1"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!audits.data || audits.data.length === 0">
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    No se encontraron auditorías
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="audits.links && audits.data.length > 0" class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Mostrando
                                <span class="font-medium">{{ audits.from }}</span>
                                a
                                <span class="font-medium">{{ audits.to }}</span>
                                de
                                <span class="font-medium">{{ audits.total }}</span>
                                resultados
                            </p>
                        </div>
                        <div class="flex gap-1">
                            <template v-for="(link, index) in audits.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    :class="['px-3 py-1 border rounded', link.active ? 'bg-yellow-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100']"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    :class="['px-3 py-1 border rounded bg-gray-100 text-gray-400 cursor-not-allowed']"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
