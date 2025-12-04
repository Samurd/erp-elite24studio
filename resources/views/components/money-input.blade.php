@props([
    'model', // variable Livewire a enlazar (ej: form.amount)
    'label' => 'Monto',
    'placeholder' => '$0.00',
])

<div class="mb-3"
    x-data="moneyInput(@entangle($model))"
    x-init="init()"
>
    <label class="block font-semibold">{{ $label }}</label>

    <input
        type="text"
        x-model="display"
        x-on:input="onInput($event)"
        x-on:keydown="onKeyDown($event)"
        inputmode="numeric"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 focus:border-yellow-600 mb-2 p-2']) }}
    />

    @error($model)
        <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
</div>

@once
    <script>
        (function() {
            const initMoneyInput = () => {
                // Check if already registered to avoid errors
                if (Alpine.data('moneyInput')) return;

                Alpine.data('moneyInput', (entangledValue) => ({
                    display: '',
                    realValue: entangledValue,

                    formatFromCents(cents) {
                        const number = (Number(cents) || 0) / 100;
                        return number.toLocaleString('es-CO', {
                            style: 'currency',
                            currency: 'COP',
                            minimumFractionDigits: 2
                        });
                    },

                    onInput(e) {
                        const digits = e.target.value.replace(/\D/g, '');
                        this.realValue = parseInt(digits || '0', 10);
                        this.display = this.formatFromCents(this.realValue);
                    },

                    onKeyDown(e) {
                        if (e.key === 'Backspace') {
                            e.preventDefault();
                            this.realValue = Math.floor((this.realValue || 0) / 10);
                            this.display = this.formatFromCents(this.realValue);
                        }
                    },

                    init() {
                        this.display = this.formatFromCents(this.realValue ?? 0);
                        this.$watch('realValue', value => {
                            this.display = this.formatFromCents(value ?? 0);
                        });
                    }
                }));
            };

            if (typeof Alpine !== 'undefined') {
                initMoneyInput();
            } else {
                document.addEventListener('alpine:init', initMoneyInput);
            }
        })();
    </script>
@endonce
