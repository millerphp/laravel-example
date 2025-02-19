<script setup>
import { computed } from 'vue';
import { TransitionRoot } from '@headlessui/vue';
import { Link } from '@inertiajs/vue3';
import { useCartDropdown } from '@/Composables/useCartDropdown';

const { cart } = useCartDropdown();

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP'
    }).format(price);
};
</script>

<template>
    <div class="relative">
        <button 
            id="cart-button"
            @click="cart.isOpen = !cart.isOpen"
            class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none"
        >
            <svg 
                class="w-6 h-6"
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span 
                v-if="cart.totalItems > 0"
                class="absolute -top-1 -right-1 bg-primary-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
            >
                {{ cart.totalItems }}
            </span>
        </button>

        <TransitionRoot
            :show="cart.isOpen"
            enter="transition-all duration-300"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="transition-all duration-200"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
        >
            <div 
                id="cart-dropdown"
                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200"
            >
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900">Shopping Cart</h3>
                    
                    <div v-if="cart.items.length === 0" class="py-8 text-center text-gray-500">
                        Your cart is empty
                    </div>
                    <div v-else>
                        <ul class="mt-4 space-y-4">
                            <li v-for="item in cart.items" :key="item.id" class="flex items-center gap-4">
                                <img :src="item.product.image" :alt="item.title" class="w-16 h-16 object-cover rounded">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">{{ item.title }}</h4>
                                    <div class="text-sm">
                                        <span v-if="item.unit_price !== item.discounted_price" 
                                              class="text-gray-500 line-through mr-2">
                                            {{ formatPrice(item.unit_price) }}
                                        </span>
                                        <span class="text-gray-900">
                                            {{ formatPrice(item.discounted_price) }}
                                        </span>
                                        <span class="text-gray-500 ml-1">
                                            × {{ item.quantity }}
                                        </span>
                                    </div>
                                    <div v-if="item.applied_discounts?.length" 
                                         class="text-xs text-red-600 mt-0.5">
                                        <div v-for="discount in item.applied_discounts" 
                                             :key="discount.description">
                                            {{ discount.description }}
                                            (-{{ discount.percentage }}%)
                                        </div>
                                    </div>
                                </div>
                                <button 
                                    @click="cart.removeItem(item.id)"
                                    class="text-gray-400 hover:text-red-500"
                                >
                                    ×
                                </button>
                            </li>
                        </ul>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex justify-between">
                                <span class="font-semibold">Subtotal:</span>
                                <span class="font-bold">{{ formatPrice(cart.subtotal) }}</span>
                            </div>
                            
                            <!-- Cart Level Discounts -->
                            <div v-if="cart.cartDiscounts?.length" 
                                 class="flex justify-between mt-2 text-sm text-red-600">
                                <span>{{ cart.cartDiscounts[0].description }}</span>
                                <span>-{{ cart.cartDiscounts[0].percentage }}%</span>
                            </div>

                            <!-- Total Savings -->
                            <div v-if="cart.totalSavings > 0" 
                                 class="flex justify-between mt-2 text-sm text-green-600">
                                <span>Total Savings:</span>
                                <span>{{ formatPrice(cart.totalSavings) }}</span>
                            </div>

                            <!-- Final Total -->
                            <div v-if="cart.totalSavings > 0" 
                                 class="flex justify-between mt-2 pt-2 border-t border-gray-200">
                                <span class="font-semibold">Final Total:</span>
                                <span class="font-bold">{{ formatPrice(cart.finalTotal) }}</span>
                            </div>
                            
                            <Link
                                href="/checkout"
                                @click="cart.closeDropdown"
                                class="mt-4 w-full flex items-center justify-center px-6 py-3 text-base font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-500 active:bg-primary-700 transform transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"
                            >
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
                                        d="M5 13l4 4L19 7"
                                    />
                                </svg>
                                Checkout
                                <span class="ml-2">→</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </TransitionRoot>
    </div>
</template> 