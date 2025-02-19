<script setup>
import { Link } from '@inertiajs/vue3';
import { formatCurrency } from '../../utils';
import { useCartStore } from '@/Stores/useCartStore';
import { onMounted } from 'vue';

const cart = useCartStore();

onMounted(() => {
    cart.loadCart();
});

</script>

<template>
    <div class="max-w-2xl mx-auto px-4 pt-16 pb-24 sm:px-6 lg:max-w-7xl lg:px-8">
        <!-- Debug display -->
        <pre class="text-xs">{{ JSON.stringify(cart.items, null, 2) }}</pre>

        <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Shopping Cart</h1>

        <div class="mt-12 lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start xl:gap-x-16">
            <!-- Cart Items -->
            <section class="lg:col-span-7">
                <ul role="list" class="border-t border-b border-gray-200 divide-y divide-gray-200">
                    <li v-for="item in cart.items" :key="item.product.id" class="flex py-6 sm:py-8">
                        <div class="flex-shrink-0">
                            <img :src="item.product.image" 
                                 :alt="item.product.title"
                                 class="w-24 h-24 rounded-md object-center object-cover sm:w-32 sm:h-32">
                        </div>

                        <div class="ml-4 flex-1 flex flex-col sm:ml-6">
                            <div>
                                <div class="flex justify-between">
                                    <h4 class="text-sm">
                                        <Link :href="`/products/${item.product.slug}`" 
                                              class="font-medium text-gray-700 hover:text-gray-800">
                                            {{ item.product.title }}
                                        </Link>
                                    </h4>
                                </div>

                                <!-- Original Price -->
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ formatCurrency(item.unit_price) }} each
                                </p>

                                <!-- Applied Discounts -->
                                <div v-if="item.applied_discounts?.length" class="mt-2">
                                    <div v-for="discount in item.applied_discounts" 
                                         :key="discount.description"
                                         class="text-sm text-red-600">
                                        {{ discount.description }}
                                        (-{{ discount.percentage }}%)
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 flex justify-between items-end">
                                <!-- Price Display -->
                                <div>
                                    <div v-if="item.unit_price !== item.discounted_price" 
                                         class="line-through text-sm text-gray-500">
                                        {{ formatCurrency(item.unit_price * item.quantity) }}
                                    </div>
                                    <div class="text-base font-medium text-gray-900">
                                        {{ formatCurrency(item.total) }}
                                    </div>
                                </div>

                                <!-- Quantity Controls -->
                                <div class="flex items-center">
                                    <label for="quantity" class="sr-only">Quantity</label>
                                    <select v-model="item.quantity" 
                                            class="max-w-full rounded-md border border-gray-300 py-1.5 text-base leading-5 font-medium text-gray-700 text-left shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option v-for="n in 10" :key="n" :value="n">{{ n }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </section>

            <!-- Order Summary -->
            <section class="mt-16 bg-gray-50 rounded-lg px-4 py-6 sm:p-6 lg:p-8 lg:mt-0 lg:col-span-5">
                <h2 class="text-lg font-medium text-gray-900">Order summary</h2>

                <dl class="mt-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600">Subtotal</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ formatCurrency(cart.subtotal) }}</dd>
                    </div>

                    <!-- Cart Level Discounts -->
                    <div v-if="cart.cartDiscounts?.length" 
                         class="border-t border-gray-200 pt-4 space-y-2">
                        <div v-for="discount in cart.cartDiscounts" 
                             :key="discount.name"
                             class="flex items-center justify-between text-sm">
                            <dt class="text-red-600">{{ discount.description }}</dt>
                            <dd class="text-red-600">-{{ discount.percentage }}%</dd>
                        </div>
                    </div>

                    <!-- Total Savings -->
                    <div v-if="cart.totalSavings > 0" 
                         class="flex items-center justify-between border-t border-gray-200 pt-4">
                        <dt class="text-sm font-medium text-green-600">Total Savings</dt>
                        <dd class="text-sm font-medium text-green-600">
                            {{ formatCurrency(cart.totalSavings) }}
                        </dd>
                    </div>

                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                        <dt class="text-base font-medium text-gray-900">Order total</dt>
                        <dd class="text-base font-medium text-gray-900">
                            {{ formatCurrency(cart.finalTotal) }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <Link href="/checkout"
                          class="w-full bg-indigo-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-indigo-500">
                        Checkout
                    </Link>
                </div>
            </section>
        </div>

        <div v-if="cart.isLoading">Loading...</div>
    </div>
</template> 