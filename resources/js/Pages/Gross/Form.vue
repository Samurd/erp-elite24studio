<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MoneyInput from '@/Components/MoneyInput.vue'
import { computed } from 'vue'

const props = defineProps({
    income: Object,
    incomeTypes: Array,
    categories: Array,
    results: Array,
    users: Array,
})

const form = useForm({
    name: props.income?.name || '',
    type_id: props.income?.type_id || '',
    category_id: props.income?.category_id || '',
    description: props.income?.description || '',
    date: props.income?.date || new Date().toISOString().split('T')[0],
    amount: props.income?.amount || '',
    created_by_id: props.income?.created_by_id || '',
    result_id: props.income?.result_id || '',
})

const isEditing = computed(() => !!props.income)

const submit = () => {
    if (isEditing.value) {
        form.put(route('finances.gross.update', props.income.id))
    } else {
        form.post(route('finances.gross.store'))
    }
}
</script>

<template>
    <AppLayout :title="isEditing ? 'Actualizar Ingreso' : 'Añadir nuevo Ingreso'">
        <div class="max-w-4xl mx-auto p-4">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex border-b pb-4 mb-4">
                    <h3 class="text-xl font-bold">{{ isEditing ? 'Actualizar Ingreso' : 'Añadir nuevo Ingreso' }}</h3>
                </div>

                <form @submit.prevent="submit" class="flex flex-col gap-4">
                    <!-- Nombre -->
                    <div class="mb-3">
                        <label for="name" class="block font-semibold mb-1">Nombre</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600"
                            placeholder="Nombre"
                        />
                        <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tipo -->
                        <div class="mb-3">
                            <label for="type" class="block font-semibold mb-1">Tipo</label>
                            <select
                                id="type"
                                v-model="form.type_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600"
                            >
                                <option value="">Seleccionar</option>
                                <option v-for="type in incomeTypes" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.type_id" class="text-red-500 text-sm">{{ form.errors.type_id }}</span>
                        </div>

                        <!-- Categoria -->
                        <div class="mb-3">
                            <label for="category" class="block font-semibold mb-1">Categoria</label>
                            <select
                                id="category"
                                v-model="form.category_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600"
                            >
                                <option value="">Seleccionar</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.category_id" class="text-red-500 text-sm">{{ form.errors.category_id }}</span>
                        </div>
                    </div>

                    <!-- Monto -->
                    <MoneyInput
                        id="amount"
                        v-model="form.amount"
                        label="Monto"
                        placeholder="$0.00"
                        :error="form.errors.amount"
                    />

                    <!-- Descripción -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-500"
                            rows="3"
                            placeholder="Agregue sus observaciones"
                        ></textarea>
                        <span v-if="form.errors.description" class="text-red-500 text-sm">{{ form.errors.description }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Fecha -->
                        <div class="mb-3">
                            <label for="date" class="block font-semibold mb-1">Fecha</label>
                            <input
                                id="date"
                                v-model="form.date"
                                type="date"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600"
                            />
                            <span v-if="form.errors.date" class="text-red-500 text-sm">{{ form.errors.date }}</span>
                        </div>

                        <!-- Responsable -->
                        <div>
                            <label for="responsible" class="block font-medium mb-1">Responsable</label>
                            <select
                                id="responsible"
                                v-model="form.created_by_id"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600"
                            >
                                <option value="">Seleccionar</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.created_by_id" class="text-red-500 text-sm">{{ form.errors.created_by_id }}</span>
                        </div>
                    </div>

                    <!-- Resultado -->
                    <div>
                        <label for="result_id" class="block font-medium mb-1">Resultado</label>
                        <select
                            id="result_id"
                            v-model="form.result_id"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600"
                        >
                            <option value="">Seleccionar</option>
                            <option v-for="result in results" :key="result.id" :value="result.id">
                                {{ result.name }}
                            </option>
                        </select>
                        <span v-if="form.errors.result_id" class="text-red-500 text-sm">{{ form.errors.result_id }}</span>
                    </div>

                    <div class="flex justify-end mt-6 pt-4 border-t gap-2">
                        <Link
                            :href="route('finances.gross.index')"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-yellow-700 hover:bg-yellow-800 text-white rounded-md disabled:opacity-50"
                        >
                            {{ isEditing ? 'Actualizar' : 'Crear' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
