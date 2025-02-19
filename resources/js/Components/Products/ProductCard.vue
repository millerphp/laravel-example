<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useIntersectionObserver } from '@/Composables/useIntersectionObserver';
import { useCartStore } from '@/Stores/useCartStore';

const props = defineProps({
    product: {
        type: Object,
        required: true
    }
});

const cart = useCartStore();

const { isIntersecting, observerRef } = useIntersectionObserver({
    threshold: 0.1,
    rootMargin: '50px'
});

const formattedFinalPrice = computed(() => {
    if (!props.product.final_price && props.product.final_price !== 0) {
        console.warn('Invalid final price for product:', props.product);
        return formattedOriginalPrice.value;
    }
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP'
    }).format(props.product.final_price);
});

const formattedOriginalPrice = computed(() => {
    if (!props.product.original_price && props.product.original_price !== 0) {
        console.warn('Invalid original price for product:', props.product);
        return '£0.00';
    }
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP'
    }).format(props.product.original_price);
});
</script>

<template>
    <div 
        ref="observerRef"
        class="group relative bg-white rounded-lg shadow-sm hover:shadow-lg transition-shadow overflow-hidden flex flex-col border border-gray-100"
        :class="{ 'animate-fade-in': isIntersecting }"
    >
        <!-- Sale badge if there's a discount -->
        <div 
            v-if="product.original_price && product.original_price > product.price"
            class="absolute top-2 left-2 bg-primary-500 text-white px-2 py-1 rounded-full text-sm font-medium z-10"
        >
            Sale
        </div>

        <Link 
            :href="route('products.show', product.slug)"
            class="aspect-square overflow-hidden bg-gray-50"
        >
            <img
                :src="product.image"
                :alt="product.title"
                class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300"
            />
        </Link>

        <div class="p-4 flex flex-col flex-grow">
            <div class="flex-grow">
                <Link 
                    :href="route('products.show', product.slug)"
                    class="text-gray-900 font-semibold hover:text-primary-600 transition-colors line-clamp-2"
                >
                    {{ product.title }}
                </Link>

                <div class="mt-2 flex items-center justify-between">
                    <div class="flex items-baseline gap-2">
                        <span class="text-lg font-bold text-primary-600">
                            {{ product.final_price ? formattedFinalPrice : formattedOriginalPrice }}
                        </span>
                        <span 
                            v-if="product.total_discount_percentage && product.total_discount_percentage > 0"
                            class="text-sm text-gray-500 line-through"
                        >
                            {{ formattedOriginalPrice }}
                        </span>
                    </div>
                    <div 
                        v-if="product.total_discount_percentage && product.total_discount_percentage > 0"
                        class="text-sm font-medium text-green-600"
                    >
                        Save {{ Math.round(product.total_discount_percentage) }}%
                    </div>
                </div>

                <!-- Show applied discounts -->
                <div 
                    v-if="product.applied_discounts?.length"
                    class="mt-2 space-y-1"
                >
                    <div 
                        v-for="discount in product.applied_discounts"
                        :key="discount.type"
                        class="text-xs text-green-600"
                    >
                        {{ discount.description }}: -{{ Math.round(discount.percentage) }}%
                    </div>
                </div>

                <div 
                    class="text-sm font-medium"
                    :class="product.stock > 0 ? 'text-secondary-600' : 'text-red-600'"
                >
                    {{ product.stock > 0 ? 'In Stock' : 'Out of Stock' }}
                </div>
            </div>

            <button 
                @click="cart.addItem(product)"
                class="mt-4 w-full relative overflow-hidden group/btn rounded-lg px-4 py-3 text-sm font-semibold"
                :class="[
                    product.stock <= 0 
                        ? 'opacity-50 cursor-not-allowed bg-gray-300 text-gray-500' 
                        : 'bg-primary-600 text-white hover:bg-primary-500 active:bg-primary-700 transform transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg'
                ]"
                :disabled="product.stock <= 0"
            >
                <span class="relative z-10 flex items-center justify-center">
                    <svg 
                        class="w-5 h-5 mr-2" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor"
                    >
                        <path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" 
                        />
                    </svg>
                    Add to Cart
                </span>
            </button>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.6s ease-out forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style> 