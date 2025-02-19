<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: Number,
        required: true
    },
    max: {
        type: Number,
        required: true
    },
    min: {
        type: Number,
        default: 1
    }
});

const emit = defineEmits(['update:modelValue']);

const quantity = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

function increment() {
    if (quantity.value < props.max) {
        quantity.value++;
    }
}

function decrement() {
    if (quantity.value > props.min) {
        quantity.value--;
    }
}

function updateQuantity(event) {
    const value = parseInt(event.target.value);
    if (isNaN(value)) return;
    
    quantity.value = Math.min(Math.max(value, props.min), props.max);
}
</script>

<template>
    <div class="flex items-center space-x-3">
        <button 
            class="w-10 h-10 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors"
            :disabled="quantity <= min"
            @click="decrement"
        >
            <span class="sr-only">Decrease quantity</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
            </svg>
        </button>

        <input
            type="number"
            :value="quantity"
            @input="updateQuantity"
            class="w-20 h-10 text-center border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
            :min="min"
            :max="max"
        />

        <button 
            class="w-10 h-10 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors"
            :disabled="quantity >= max"
            @click="increment"
        >
            <span class="sr-only">Increase quantity</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </button>
    </div>
</template> 