<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    discount: {
        type: Object,
        default: () => ({
            name: '',
            description: '',
            percentage: null,
            type: 'product',
            starts_at: null,
            ends_at: null,
            is_active: true,
            priority: 0,
            rules: {},
            stacking_rules: { allowed_types: [] },
            usage_limit: null,
            minimum_order_amount: null,
            maximum_discount_amount: null,
        }),
    },
});

const form = ref({
    name: props.discount.name,
    description: props.discount.description,
    percentage: props.discount.percentage,
    type: props.discount.type,
    starts_at: props.discount.starts_at,
    ends_at: props.discount.ends_at,
    is_active: props.discount.is_active,
    priority: props.discount.priority,
    rules: props.discount.rules,
    stacking_rules: props.discount.stacking_rules,
    usage_limit: props.discount.usage_limit,
    minimum_order_amount: props.discount.minimum_order_amount,
    maximum_discount_amount: props.discount.maximum_discount_amount,
});

const discountTypes = [
    { value: 'product', label: 'Product Discount' },
    { value: 'category', label: 'Category Discount' },
    { value: 'cart', label: 'Cart Discount' },
];

function submit() {
    if (props.discount.id) {
        router.put(route('admin.discounts.update', props.discount.id), form.value);
    } else {
        router.post(route('admin.discounts.store'), form.value);
    }
}
</script>

<template>
    <AdminLayout>
        <div class="max-w-2xl mx-auto py-6">
            <div class="px-4 sm:px-6 md:px-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ discount.id ? 'Edit Discount' : 'Create Discount' }}
                </h1>
            </div>

            <div class="mt-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white shadow rounded-lg p-6 space-y-6">
                        <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                required
                            >
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            ></textarea>
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                            <select
                                id="type"
                                v-model="form.type"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                                <option v-for="type in discountTypes" :key="type.value" :value="type.value">
                                    {{ type.label }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="percentage" class="block text-sm font-medium text-gray-700">Percentage</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input
                                    type="number"
                                    id="percentage"
                                    v-model="form.percentage"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-primary-500 focus:ring-primary-500"
                                    required
                                >
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Validity Period -->
                    <div class="bg-white shadow rounded-lg p-6 space-y-6">
                        <h2 class="text-lg font-medium text-gray-900">Validity Period</h2>
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="starts_at" class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input
                                    type="datetime-local"
                                    id="starts_at"
                                    v-model="form.starts_at"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                            </div>

                            <div>
                                <label for="ends_at" class="block text-sm font-medium text-gray-700">End Date</label>
                                <input
                                    type="datetime-local"
                                    id="ends_at"
                                    v-model="form.ends_at"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Usage Limits -->
                    <div class="bg-white shadow rounded-lg p-6 space-y-6">
                        <h2 class="text-lg font-medium text-gray-900">Usage Limits</h2>
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="usage_limit" class="block text-sm font-medium text-gray-700">Usage Limit</label>
                                <input
                                    type="number"
                                    id="usage_limit"
                                    v-model="form.usage_limit"
                                    min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                            </div>

                            <div>
                                <label for="minimum_order_amount" class="block text-sm font-medium text-gray-700">
                                    Minimum Order Amount
                                </label>
                                <input
                                    type="number"
                                    id="minimum_order_amount"
                                    v-model="form.minimum_order_amount"
                                    min="0"
                                    step="0.01"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                            </div>

                            <div>
                                <label for="maximum_discount_amount" class="block text-sm font-medium text-gray-700">
                                    Maximum Discount Amount
                                </label>
                                <input
                                    type="number"
                                    id="maximum_discount_amount"
                                    v-model="form.maximum_discount_amount"
                                    min="0"
                                    step="0.01"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                            </div>

                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                                <input
                                    type="number"
                                    id="priority"
                                    v-model="form.priority"
                                    min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <input
                                    type="checkbox"
                                    id="is_active"
                                    v-model="form.is_active"
                                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                >
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_active" class="font-medium text-gray-700">Active</label>
                                <p class="text-gray-500">Make this discount available for use</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3">
                        <a
                            :href="route('admin.discounts.index')"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
                        >
                            {{ discount.id ? 'Update Discount' : 'Create Discount' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template> 