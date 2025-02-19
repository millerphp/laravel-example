<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TheHeader from '@/Components/Layout/TheHeader.vue';
import { useCartStore } from '@/Stores/useCartStore';

const cart = useCartStore();

const shippingMethods = [
    { id: 'standard', name: 'Standard Delivery', price: 3.99, time: '3-5 business days' },
    { id: 'express', name: 'Express Delivery', price: 9.99, time: '1-2 business days' },
];

const selectedShipping = ref(shippingMethods[0].id);

const total = computed(() => {
    const shippingCost = shippingMethods.find(m => m.id === selectedShipping.value).price;
    return cart.subtotal + shippingCost;
});

const formattedPrice = (price) => {
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP'
    }).format(price);
};

function handleSubmit() {

}

</script>

<template>
    <Head title="Checkout" />

    <div class="min-h-screen bg-gray-50">
        <TheHeader />

        <main class="container mx-auto px-4 py-8 pt-24">
            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
                <!-- Cart Review -->
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-6">Order Summary</h2>
                        
                        <div class="divide-y divide-gray-200">
                            <div v-for="item in cart.items" :key="item.id" class="py-6 flex">
                                <img 
                                    :src="item.product.image" 
                                    :alt="item.product.title"
                                    class="flex-none w-20 h-20 object-cover bg-gray-100 rounded-lg"
                                />
                                <div class="ml-4 flex-auto">
                                    <div class="flex justify-between">
                                        <h3 class="text-sm font-medium text-gray-900">
                                            {{ item.product.title }}
                                        </h3>
                                        <div class="ml-4 text-right">
                                            <p v-if="item.unit_price !== item.discounted_price" 
                                               class="text-sm text-gray-500 line-through">
                                                {{ formattedPrice(item.unit_price * item.quantity) }}
                                            </p>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ formattedPrice(item.discounted_price * item.quantity) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-1 flex items-end justify-between">
                                        <div class="flex items-center space-x-2">
                                            <label class="text-sm text-gray-500">Qty</label>
                                            <select 
                                                v-model="item.quantity"
                                                class="rounded-md border-gray-300 text-base focus:border-primary-500 focus:ring-primary-500"
                                                @change="cart.updateQuantity(item.id, parseInt($event.target.value))"
                                            >
                                                <option 
                                                    v-for="n in 10" 
                                                    :key="n" 
                                                    :value="n"
                                                >
                                                    {{ n }}
                                                </option>
                                            </select>
                                        </div>
                                        <button 
                                            @click="cart.removeItem(item.id)"
                                            class="text-sm font-medium text-primary-600 hover:text-primary-500"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                    <div v-if="item.applied_discounts?.length" 
                                         class="text-sm text-red-600">
                                        <div v-for="discount in item.applied_discounts" 
                                             :key="discount.description">
                                            {{ discount.description }}
                                            (-{{ discount.percentage }}%)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Method -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-6">Shipping Method</h2>
                        
                        <div class="space-y-4">
                            <div 
                                v-for="method in shippingMethods"
                                :key="method.id"
                                class="flex items-center"
                            >
                                <input
                                    type="radio"
                                    :id="method.id"
                                    :value="method.id"
                                    v-model="selectedShipping"
                                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                                />
                                <label :for="method.id" class="ml-3 flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">
                                        {{ method.name }} ({{ formattedPrice(method.price) }})
                                    </span>
                                    <span class="block text-sm text-gray-500">
                                        {{ method.time }}
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-5 mt-8 lg:mt-0">
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
                        <h2 class="text-lg font-medium text-gray-900 mb-6">Order Total</h2>
                        
                        <dl class="space-y-4">
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-600">Subtotal</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ formattedPrice(cart.subtotal) }}</dd>
                            </div>
                            
                            <div v-if="cart.cartDiscounts?.length" class="flex items-center justify-between">
                                <dt class="text-sm text-red-600">
                                    {{ cart.cartDiscounts[0].description }}
                                </dt>
                                <dd class="text-sm text-red-600">
                                    -{{ cart.cartDiscounts[0].percentage }}%
                                </dd>
                            </div>

                            <div v-if="cart.totalSavings > 0" class="flex items-center justify-between">
                                <dt class="text-sm text-green-600">Total Savings</dt>
                                <dd class="text-sm text-green-600">
                                    {{ formattedPrice(cart.totalSavings) }}
                                </dd>
                            </div>

                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-600">Shipping</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    {{ formattedPrice(shippingMethods.find(m => m.id === selectedShipping).price) }}
                                </dd>
                            </div>
                            
                            <div class="border-t border-gray-200 pt-4 flex items-center justify-between">
                                <dt class="text-base font-medium text-gray-900">Order total</dt>
                                <dd class="text-base font-medium text-gray-900">{{ formattedPrice(cart.finalTotal) }}</dd>
                            </div>
                        </dl>

                        <button
                            class="mt-6 w-full flex items-center justify-center px-6 py-4 text-lg font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-500 active:bg-primary-700 transform transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"
                            @click="handleSubmit"
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
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            Proceed to Payment
                        </button>

                        <p class="mt-4 text-center text-sm text-gray-500">
                            Secure payment powered by Stripe
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template> 