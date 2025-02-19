<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TheHeader from '@/Components/Layout/TheHeader.vue';
import ProductCard from '@/Components/Products/ProductCard.vue';
import ImageGallery from '@/Components/Products/ImageGallery.vue';
import QuantitySelector from '@/Components/Products/QuantitySelector.vue';
import ProductTabs from '@/Components/Products/ProductTabs.vue';
import Breadcrumbs from '@/Components/UI/Breadcrumbs.vue';
import { useCartStore } from '@/Stores/useCartStore';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    relatedProducts: {
        type: Array,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});

const quantity = ref(1);
const selectedTab = ref('description');
const cart = useCartStore();

const formattedPrice = computed(() => {
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP'
    }).format(props.product.price);
});

const isInStock = computed(() => {
    return props.product.getTotalAvailableQuantity > 0;
});

const breadcrumbItems = computed(() => {
    const items = [];
    
    // Add category breadcrumbs
    if (props.product.categories && props.product.categories.length > 0) {
        const category = props.product.categories[0];
        items.push({
            label: category.name,
            href: route('categories.show', category.slug)
        });
    }
    
    // Add product
    items.push({
        label: props.product.title
    });
    
    return items;
});

const addToCart = async (product) => {
    await cart.addItem(product);
};
</script>

<template>
    <Head :title="product.title" />

    <div class="min-h-screen bg-gray-50 relative z-0">
        <TheHeader :categories="categories" />

        <main class="container mx-auto px-4 py-8 pt-24">
            <!-- Breadcrumbs -->
            <Breadcrumbs 
                :items="breadcrumbItems"
                class="mb-8"
            />

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Product Images -->
                <ImageGallery 
                    :images="product.images" 
                    :title="product.title"
                />

                <!-- Product Info -->
                <div class="space-y-6">
                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ product.title }}
                    </h1>

                    <div class="flex items-baseline">
                        <span class="text-4xl font-bold text-primary-600">
                            {{ formattedPrice }}
                        </span>
                        <span v-if="product.original_price > product.price" class="ml-4 text-lg text-gray-500 line-through">
                            {{ formatPrice(product.original_price) }}
                        </span>
                    </div>

                    <!-- Stock Status -->
                    <div>
                        <span 
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                            :class="isInStock ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                        >
                            {{ isInStock ? 'In Stock' : 'Out of Stock' }}
                        </span>
                    </div>

                    <!-- Add to Cart Section -->
                    <div class="space-y-4 pt-6 border-t">
                        <QuantitySelector 
                            v-model="quantity"
                            :max="product.getTotalAvailableQuantity"
                        />

                        <button 
                            @click="addToCart(product)" 
                            class="mt-8 flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-8 py-3 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Add to Cart
                        </button>
                    </div>

                    <!-- Product Categories -->
                    <div class="flex gap-2 pt-4">
                        <Link
                            v-for="category in product.categories"
                            :key="category.id"
                            :href="route('categories.show', category.slug)"
                            class="px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-sm hover:bg-gray-200 transition-colors"
                        >
                            {{ category.name }}
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Product Tabs -->
            <ProductTabs 
                :product="product"
                v-model="selectedTab"
                class="mt-16"
            />

            <!-- Related Products -->
            <section v-if="relatedProducts.length > 0" class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">
                    Related Products
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <ProductCard 
                        v-for="product in relatedProducts"
                        :key="product.id"
                        :product="product"
                    />
                </div>
            </section>
        </main>
    </div>
</template> 