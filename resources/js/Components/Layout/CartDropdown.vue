<script setup>
import { Link } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/useCartStore';
import { formatCurrency } from '@/utils';
import { onMounted } from 'vue';

const cart = useCartStore();

// Load cart data when component mounts
onMounted(() => {
    cart.loadCart();
});
</script>

<template>
    <div v-if="cart.isOpen" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50">
        <div class="p-4">
            <div v-if="cart.isLoading" class="text-gray-500 text-center py-4">
                Loading...
            </div>
            <div v-else-if="cart.items.length === 0" class="text-gray-500 text-center py-4">
                Your cart is empty
            </div>
            
            <div v-else>
                <!-- Cart Items -->
                <ul class="divide-y divide-gray-100">
                    <li v-for="item in cart.items" 
                        :key="item.id" 
                        class="flex py-4"
                    >
                        <!-- Product Image -->
                        <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                            <img 
                                :src="item.product.image" 
                                class="h-full w-full object-cover object-center"
                                :alt="item.product.title"
                            >
                        </div>

                        <!-- Product Details -->
                        <div class="ml-4 flex flex-1 flex-col">
                            <div>
                                <div class="flex justify-between text-base font-medium text-gray-900">
                                    <h3>
                                        {{ item.product.title }}
                                    </h3>
                                    <div class="text-right">
                                        <p v-if="item.unit_price !== item.discounted_price" 
                                            class="text-sm text-gray-500 line-through">
                                            {{ formatCurrency(item.unit_price * item.quantity) }}
                                        </p>
                                        <p class="ml-4">
                                            {{ formatCurrency(item.discounted_price * item.quantity) }}
                                        </p>
                                    </div>
                                </div>
                                <div v-if="item.applied_discounts?.length" 
                                    class="mt-1 text-sm text-red-600">
                                    <div v-for="discount in item.applied_discounts" 
                                        :key="discount.description">
                                        {{ discount.description }}
                                        (-{{ discount.percentage }}%)
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-1 items-end justify-between text-sm">
                                <div class="flex items-center space-x-2">
                                    <label class="text-sm text-gray-500">Qty</label>
                                    <select 
                                        v-model="item.quantity"
                                        class="rounded-md border-gray-300 text-base focus:border-primary-500 focus:ring-primary-500"
                                        @change="cart.updateQuantity(item.id, parseInt($event.target.value))"
                                    >
                                        <option v-for="n in 10" :key="n" :value="n">
                                            {{ n }}
                                        </option>
                                    </select>
                                </div>
                                <button 
                                    @click="cart.removeItem(item.id)"
                                    class="font-medium text-primary-600 hover:text-primary-500"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </li>
                </ul>

                <!-- Cart Footer -->
                <div class="mt-4">
                    <div class="flex justify-between text-base font-medium text-gray-900">
                        <p>Subtotal</p>
                        <p>{{ formatCurrency(cart.subtotal) }}</p>
                    </div>
                    
                    <!-- Cart Level Discounts -->
                    <div v-if="cart.cartDiscounts?.length" class="flex items-center justify-between mt-2">
                        <p class="text-sm text-red-600">
                            {{ cart.cartDiscounts[0].description }}
                        </p>
                        <p class="text-sm text-red-600">
                            -{{ cart.cartDiscounts[0].percentage }}%
                        </p>
                    </div>

                    <!-- Total Savings -->
                    <div v-if="cart.totalSavings > 0" class="flex items-center justify-between mt-2">
                        <p class="text-sm text-green-600">Total Savings</p>
                        <p class="text-sm text-green-600">
                            {{ formatCurrency(cart.totalSavings) }}
                        </p>
                    </div>

                    <div class="mt-4">
                        <Link 
                            href="/cart"
                            class="flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-primary-700"
                        >
                            View Cart
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template> 