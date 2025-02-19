<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    product: {
        type: Object,
        default: () => ({
            title: '',
            description: '',
            price: '',
            image: '',
            is_active: true,
            category_ids: [],
            stock: [],
        }),
    },
    categories: {
        type: Array,
        required: true,
    },
    warehouses: {
        type: Array,
        required: true,
    },
});

const form = ref({
    title: props.product.title,
    description: props.product.description,
    price: props.product.price,
    image: props.product.image,
    is_active: props.product.is_active,
    category_ids: props.product.category_ids,
    stock: props.product.stock.length ? props.product.stock : [
        { warehouse_id: '', quantity: 0, reorder_point: 0 }
    ],
});

const addStockEntry = () => {
    form.value.stock.push({ warehouse_id: '', quantity: 0, reorder_point: 0 });
};

const removeStockEntry = (index) => {
    form.value.stock.splice(index, 1);
};

const submit = () => {
    if (props.product.id) {
        router.put(route('admin.products.update', props.product.id), form.value);
    } else {
        router.post(route('admin.products.store'), form.value);
    }
};
</script>

<template>
    <AdminLayout>
        <div class="max-w-4xl mx-auto">
            <form @submit.prevent="submit" class="space-y-8">
                <!-- Header -->
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-900">
                        {{ product.id ? 'Edit Product' : 'Create Product' }}
                    </h1>
                    <div class="flex items-center space-x-4">
                        <Link 
                            :href="route('admin.products.index')"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
                        >
                            {{ product.id ? 'Update Product' : 'Create Product' }}
                        </button>
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="bg-white shadow rounded-lg p-6 space-y-6">
                    <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Title
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                required
                            >
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                required
                            ></textarea>
                        </div>

                        <!-- Price -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Price
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">£</span>
                                </div>
                                <input
                                    v-model="form.price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    required
                                >
                            </div>
                        </div>

                        <!-- Image URL -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Image URL
                            </label>
                            <input
                                v-model="form.image"
                                type="url"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            >
                        </div>

                        <!-- Categories -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Categories
                            </label>
                            <select
                                v-model="form.category_ids"
                                multiple
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                required
                            >
                                <option 
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center">
                            <input
                                v-model="form.is_active"
                                type="checkbox"
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                            >
                            <label class="ml-2 block text-sm text-gray-900">
                                Product is active
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Stock Management -->
                <div class="bg-white shadow rounded-lg p-6 space-y-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Stock Management</h2>
                        <button
                            type="button"
                            @click="addStockEntry"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200"
                        >
                            Add Warehouse
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div 
                            v-for="(stock, index) in form.stock"
                            :key="index"
                            class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg"
                        >
                            <!-- Warehouse -->
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">
                                    Warehouse
                                </label>
                                <select
                                    v-model="stock.warehouse_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    required
                                >
                                    <option value="">Select Warehouse</option>
                                    <option 
                                        v-for="warehouse in warehouses"
                                        :key="warehouse.id"
                                        :value="warehouse.id"
                                    >
                                        {{ warehouse.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Quantity -->
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">
                                    Quantity
                                </label>
                                <input
                                    v-model="stock.quantity"
                                    type="number"
                                    min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    required
                                >
                            </div>

                            <!-- Reorder Point -->
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">
                                    Reorder Point
                                </label>
                                <input
                                    v-model="stock.reorder_point"
                                    type="number"
                                    min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    required
                                >
                            </div>

                            <!-- Remove Button -->
                            <button
                                v-if="form.stock.length > 1"
                                type="button"
                                @click="removeStockEntry(index)"
                                class="mt-6 p-1 text-gray-400 hover:text-red-500"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template> 