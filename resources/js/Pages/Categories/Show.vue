<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import TheHeader from '@/Components/Layout/TheHeader.vue';
import ProductCard from '@/Components/Products/ProductCard.vue';
import Breadcrumbs from '@/Components/UI/Breadcrumbs.vue';
import Pagination from '@/Components/UI/Pagination.vue';

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
    products: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});

const sortOptions = [
    { value: 'newest', label: 'Newest First' },
    { value: 'oldest', label: 'Oldest First' },
    { value: 'price_asc', label: 'Price: Low to High' },
    { value: 'price_desc', label: 'Price: High to Low' },
];

const activeFilters = ref({
    sort: props.filters.sort,
    min_price: props.filters.min_price,
    max_price: props.filters.max_price,
    in_stock: props.filters.in_stock,
});

const isLoading = ref(false);

const updateFilters = debounce(() => {
    isLoading.value = true;
    router.get(route('categories.show', props.category.slug), activeFilters.value, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            isLoading.value = false;
        }
    });
}, 500);

watch(activeFilters, updateFilters, { deep: true });

const breadcrumbItems = computed(() => {
    return [
        ...props.category.ancestors.map(ancestor => ({
            label: ancestor.name,
            href: route('categories.show', ancestor.slug)
        })),
        { label: props.category.name }
    ];
});

function addToCart(product) {
    
}
</script>

<template>
    <Head :title="category.name" />

    <div class="min-h-screen bg-gray-50 relative z-0">
        <TheHeader :categories="categories" />

        <main class="container mx-auto px-4 py-8 pt-24">
            <Breadcrumbs 
                :items="breadcrumbItems"
                class="mb-8"
            />

            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                <!-- Sidebar -->
                <div class="hidden lg:block lg:col-span-3">
                    <div class="sticky top-24 space-y-6">
                        <!-- Category children if any -->
                        <div v-if="category.children.length" class="bg-white p-4 rounded-lg shadow-sm">
                            <h3 class="text-lg font-semibold mb-3">Subcategories</h3>
                            <div class="space-y-2">
                                <Link 
                                    v-for="child in category.children"
                                    :key="child.id"
                                    :href="route('categories.show', child.slug)"
                                    class="block text-gray-600 hover:text-primary-600"
                                >
                                    {{ child.name }}
                                </Link>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="bg-white p-4 rounded-lg shadow-sm space-y-4">
                            <h3 class="text-lg font-semibold">Filters</h3>

                            <!-- Price Range -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Price Range</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="sr-only">Min Price</label>
                                        <input 
                                            type="number"
                                            v-model.number="activeFilters.min_price"
                                            :min="filters.price_range.min"
                                            :max="filters.price_range.max"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                            placeholder="Min"
                                        />
                                    </div>
                                    <div>
                                        <label class="sr-only">Max Price</label>
                                        <input 
                                            type="number"
                                            v-model.number="activeFilters.max_price"
                                            :min="filters.price_range.min"
                                            :max="filters.price_range.max"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                            placeholder="Max"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Stock Filter -->
                            <div>
                                <label class="inline-flex items-center">
                                    <input 
                                        type="checkbox"
                                        v-model="activeFilters.in_stock"
                                        class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-900">In Stock Only</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-9">
                    <!-- Add loading and empty states -->
                    <div v-if="!products.data?.length" class="text-center py-12">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
                        <p class="text-gray-500">
                            Try adjusting your filters or check back later for new products.
                        </p>
                    </div>

                    <template v-else>
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">
                                    {{ category.name }}
                                </h1>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ products.total }} products found
                                </p>
                            </div>

                            <select 
                                v-model="activeFilters.sort"
                                class="rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-primary-500 focus:outline-none focus:ring-primary-500"
                            >
                                <option 
                                    v-for="option in sortOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Active Filters -->
                        <div v-if="activeFilters.in_stock || activeFilters.min_price || activeFilters.max_price" 
                            class="mb-6 flex flex-wrap gap-2"
                        >
                            <div v-if="activeFilters.in_stock" 
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-primary-100 text-primary-800"
                            >
                                In Stock Only
                                <button 
                                    @click="activeFilters.in_stock = false"
                                    class="ml-2 text-primary-600 hover:text-primary-800"
                                >
                                    ×
                                </button>
                            </div>
                            <div v-if="activeFilters.min_price || activeFilters.max_price" 
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-primary-100 text-primary-800"
                            >
                                Price: {{ activeFilters.min_price || '0' }} - {{ activeFilters.max_price || '∞' }}
                                <button 
                                    @click="() => {
                                        activeFilters.min_price = null;
                                        activeFilters.max_price = null;
                                    }"
                                    class="ml-2 text-primary-600 hover:text-primary-800"
                                >
                                    ×
                                </button>
                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div 
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                            :class="{ 'opacity-50 pointer-events-none': isLoading }"
                        >
                            <ProductCard 
                                v-for="product in products.data"
                                :key="product.id"
                                :product="product"
                                @add-to-cart="addToCart"
                            />
                        </div>

                        <!-- Pagination -->
                        <Pagination 
                            v-if="products.links?.length > 3"
                            :links="products.links"
                            class="mt-8"
                        />
                    </template>
                </div>
            </div>
        </main>
    </div>
</template> 