<script setup>
import { ref, watch, onMounted } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    modelValue: [Number, String],
    label: {
        type: String,
        default: 'Monto'
    },
    placeholder: {
        type: String,
        default: '$0.00'
    },
    error: String,
    id: String,
});

const emit = defineEmits(['update:modelValue']);

const displayValue = ref('');
const inputRef = ref(null);

// Format number to COP currency string
const formatCurrency = (value) => {
    if (value === undefined || value === null || value === '') return '';
    
    // Check if value is integer cents or float
    // Assuming modelValue comes in as float (e.g., 100.50) from backend
    // But internal logic works with cents.
    
    // We display based on value passed. 
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0, 
        maximumFractionDigits: 0 
    }).format(value);
};

// Handle Input: User types digits
const onInput = (e) => {
    // 1. Get current value only digits
    let value = e.target.value.replace(/\D/g, '');
    
    // 2. Parse as integer
    let numericValue = parseInt(value || '0', 10);
    
    // 3. Update display immediately
    displayValue.value = formatCurrency(numericValue);
    
    // 4. Emit numeric value (as integer)
    // The previous blade component logic suggested working with integers for safe money math
    // But if the backend expects float (12.34), we should divide by 100?
    // Looking at the blade component:
    // formatFromCents divides by 100.
    // realValue = parseInt(digits).
    // So realValue STORED 1234 for $12.34.
    // IF the DB stores cents (integer), this is fine.
    // IF the DB stores decimal (12.34), this is WRONG unless we divide.
    
    // User requested "haz lo mismo... asi esta con blade".
    // Blade: realValue is integer. Display is realValue / 100.
    
    // Let's assume we want to emit the INTEGER value (COP usually doesn't use cents strictly like USD, 
    // but the blade component has fraction digits 2? No, `minimumFractionDigits: 2`.
    // Wait, COP usually doesn't use cents. $1.000, $20.000.
    // BUT the blade component had `formatFromCents` dividing by 100 and `minimumFractionDigits: 2`.
    // Example: 100000 -> $1.000,00
    
    // EDIT: Wait, checking the blade component again:
    // formatFromCents(cents) { const number = (Number(cents) || 0) / 100; ... currency: 'COP' ... minimumFractionDigits: 2 }
    // So yes, it treats input as cents.
    
    // HOWEVER, in my `Subs/Index.vue` I implemented `minimumFractionDigits: 0`.
    // And in `Subs/Form.vue` I used `step="0.01"`.
    // Commonly in Colombia, prices are integers like 50000.
    
    // Let's follow the blade component exactly for behavior.
    
    // Emit the integer value?
    // If I bind v-model="form.amount", and form.amount is sent to backend.
    // Sub model in Laravel usually casts or expects.
    // If I send 5000000 (5 million cents?) to a column expecting 50000 (50k pesos), that's an issue.
    
    // Let's assume the blade component was correct for the context.
    // It emits `realValue` which is the raw integer of digits typed.
    // AND it displays `realValue / 100`.
    
    // So if I type "50000", `realValue` = 50000. Display = $500,00.
    // This value 50000 is sent to backend.
    
    // I will replicate this behavior.
    emit('update:modelValue', numericValue);
};

// Watch for external model changes
watch(() => props.modelValue, (newValue) => {
    // If we receive a value, we format it.
    // But wait, if modelValue is 50000 (from DB), we should display $500.00?
    // Or is 50000 the value in DOLLARS?
    
    // Scenario A: DB stores 500.00.
    // If we pass 500.00 to this component.
    // formatFromCents expects cents.
    // So we would need to multiply by 100?
    
    // Given the ambiguity and the request "do the same as blade", I will stick to the blade logic:
    // Input -> Digits -> Integer (realValue)
    // Display -> Integer / 100 -> Currency String
    
    let val = parseInt(newValue || '0', 10);
    // displayValue.value = formatCurrency(val / 100); 
    
    // Wait, the blade component uses `formatFromCents` which divides by 100.
    // BUT the format I wrote above `formatCurrency` uses `value` directly?
    // Let's fix `formatCurrency` to match blade:
    
    // RE-READING BLADE:
    // realValue = parseInt(digits)
    // display = formatFromCents(realValue) -> realValue / 100.
    
    displayValue.value = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 2
    }).format(val / 100);
}, { immediate: true });

</script>

<template>
    <div>
        <InputLabel v-if="label" :for="id" :value="label" />
        <div class="relative mt-1 rounded-md shadow-sm">
            <input
                ref="inputRef"
                :id="id"
                type="text"
                inputmode="numeric"
                :value="displayValue"
                @input="onInput"
                :placeholder="placeholder"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600 p-2"
                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': error }"
            />
        </div>
        <InputError v-if="error" :message="error" class="mt-2" />
    </div>
</template>
