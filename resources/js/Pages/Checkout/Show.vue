<script setup>
const props = defineProps({
    items: Array,
    subtotal: Number,
    cart_discounts: Array,
    total_savings: Number,
    final_total: Number,
});
</script>

<template>
    <div class="space-y-8">
        <!-- Items -->
        <div class="space-y-4">
            <div v-for="item in items" :key="item.product.id" class="flex justify-between">
                <div>
                    <h3>{{ item.product.title }}</h3>
                    <div class="text-sm text-gray-500">
                        Quantity: {{ item.quantity }}
                    </div>
                    <div v-if="item.applied_discounts.length" class="text-sm text-red-600">
                        <div v-for="discount in item.applied_discounts" :key="discount.description">
                            {{ discount.description }}
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">
                        {{ formatCurrency(item.unit_price) }} each
                    </div>
                    <div v-if="item.unit_price !== item.discounted_price" class="line-through text-sm text-gray-400">
                        {{ formatCurrency(item.unit_price * item.quantity) }}
                    </div>
                    <div class="font-medium">
                        {{ formatCurrency(item.total) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Totals -->
        <div class="border-t pt-4 space-y-2">
            <div class="flex justify-between text-sm text-gray-500">
                <span>Subtotal</span>
                <span>{{ formatCurrency(subtotal) }}</span>
            </div>

            <!-- Cart Discounts -->
            <div v-if="cart_discounts.length" class="space-y-1">
                <div v-for="discount in cart_discounts" 
                     :key="discount.name"
                     class="flex justify-between text-sm text-red-600">
                    <span>{{ discount.description }}</span>
                    <span>-{{ discount.percentage }}%</span>
                </div>
            </div>

            <!-- Total Savings -->
            <div v-if="total_savings > 0" class="flex justify-between text-sm font-medium text-green-600">
                <span>Total Savings</span>
                <span>{{ formatCurrency(total_savings) }}</span>
            </div>

            <!-- Final Total -->
            <div class="flex justify-between text-lg font-bold">
                <span>Total</span>
                <span>{{ formatCurrency(final_total) }}</span>
            </div>
        </div>
    </div>
</template> 