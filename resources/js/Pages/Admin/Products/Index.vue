<script setup>
import { ref, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { formatCurrency } from '@/utils';
import Pagination from '@/Components/UI/Pagination.vue';

const props = defineProps({
    products: Object,
    filters: Object,
    categories: Array,
});

const search = ref(props.filters.search);
const selectedCategory = ref(props.filters.category);
const stockStatus = ref(props.filters.stock_status);

const updateSearch = debounce((value) => {
    router.get(route('admin.products.index'), 
        { 
            search: value,
            category: selectedCategory.value,
            stock_status: stockStatus.value,
        }, 
        { preserveState: true }
    );
}, 300);

watch([selectedCategory, stockStatus], () => {
    router.get(route('admin.products.index'),
        {
            search: search.value,
            category: selectedCategory.value,
            stock_status: stockStatus.value,
        },
        { preserveState: true }
    );
});
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-900">Products</h1>
                <Link 
                    :href="route('admin.products.create')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
                >
                    Add Product
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-lg shadow space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Search</label>
                        <input
                            v-model="search"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Search products..."
                            @input="updateSearch($event.target.value)"
                        >
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <select
                            v-model="selectedCategory"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option value="">All Categories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Stock Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stock Status</label>
                        <select
                            v-model="stockStatus"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option value="">All Stock Levels</option>
                            <option value="low">Low Stock</option>
                            <option value="out">Out of Stock</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Product
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Categories
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stock
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sales
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template v-for="product in products.data" :key="product.id">
                            <!-- Main product row -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <img :src="product.image" class="h-10 w-10 rounded-lg object-cover">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ product.title }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Added {{ product.created_at }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        <span 
                                            v-for="category in product.categories" 
                                            :key="category.id"
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                                        >
                                            {{ category.name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="space-y-1">
                                        <div class="font-medium">
                                            {{ formatCurrency(product.final_price) }}
                                        </div>
                                        <div v-if="product.total_discount_percentage > 0" 
                                             class="text-xs text-gray-500 line-through">
                                            {{ formatCurrency(product.price) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800': product.stock > 10,
                                            'bg-yellow-100 text-yellow-800': product.stock <= 10 && product.stock > 0,
                                            'bg-red-100 text-red-800': product.stock <= 0
                                        }"
                                    >
                                        {{ product.stock }} in stock
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ product.total_sold }} units sold</div>
                                    <div>{{ product.in_cart_count }} in carts</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                    >
                                        {{ product.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-3">
                                        <Link 
                                            :href="route('admin.products.edit', product.id)"
                                            class="text-primary-600 hover:text-primary-900"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            @click="() => {
                                                if (confirm('Are you sure you want to delete this product?')) {
                                                    router.delete(route('admin.products.destroy', product.id))
                                                }
                                            }"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Discount details row -->
                            <tr class="bg-gray-50">
                                <td colspan="7" class="px-6 py-2">
                                    <div class="flex items-center space-x-4 text-sm">
                                        <span class="text-gray-500">Discounts:</span>
                                        <div v-if="product.applied_discounts?.length" class="flex flex-wrap gap-2">
                                            <span 
                                                v-for="discount in product.applied_discounts" 
                                                :key="discount.description"
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                :class="{
                                                    'bg-primary-100 text-primary-800': discount.type === 'product',
                                                    'bg-secondary-100 text-secondary-800': discount.type === 'category',
                                                    'bg-green-100 text-green-800': discount.type === 'customer'
                                                }"
                                            >
                                                {{ discount.description }}
                                                (-{{ discount.percentage }}%)
                                            </span>
                                        </div>
                                        <div v-else class="text-gray-400 italic">
                                            No active discounts
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <Pagination 
                v-if="products.links"
                :links="products.links" 
            />
        </div>
    </AdminLayout>
</template> 