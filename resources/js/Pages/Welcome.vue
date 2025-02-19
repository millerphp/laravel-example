<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import TheHeader from '@/Components/Layout/TheHeader.vue';
import ProductCard from '@/Components/Products/ProductCard.vue';
import { useParallax } from '@/Composables/useParallax.js';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    featuredProducts: {
        type: Array,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});

const { handleMouseMove, parallaxStyle } = useParallax();

function addToCart(product) {
    
}
</script>

<template>
    <Head title="Welcome to Our Store" />

    <div 
        class="min-h-screen bg-gray-50"
        @mousemove="handleMouseMove"
    >
        <TheHeader :categories="categories" />

        <!-- Hero Section -->
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
            <!-- Static floating elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div v-for="n in 6" :key="n" 
                    class="absolute rounded-full mix-blend-multiply animate-float"
                    :class="`floating-element-${n}`"
                ></div>
            </div>

            <!-- Parallax content -->
            <div 
                class="container mx-auto px-4 text-center relative z-10"
                :style="parallaxStyle"
            >
                <h1 
                    class="text-5xl md:text-7xl font-bold text-gray-900 mb-6"
                    v-motion
                    :initial="{ opacity: 0, y: 50 }"
                    :enter="{ 
                        opacity: 1, 
                        y: 0,
                        transition: {
                            type: 'spring',
                            stiffness: 50,
                            damping: 15
                        }
                    }"
                >
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary-600 via-primary-500 to-secondary-500 animate-gradient-text">
                        Discover Amazing Products
                    </span>
                </h1>
                
                <p 
                    class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto"
                    v-motion
                    :initial="{ opacity: 0, y: 50 }"
                    :enter="{ opacity: 1, y: 0, delay: 200 }"
                >
                    Shop the latest trends with confidence
                </p>
                
                <Link 
                    :href="route('categories.show', categories[0].slug)"
                    class="inline-flex items-center px-8 py-3 text-lg font-semibold rounded-full bg-primary-600 text-white hover:bg-primary-500 transition-all duration-300 transform hover:scale-105 hover:shadow-xl"
                    v-motion
                    :initial="{ opacity: 0, y: 50 }"
                    :enter="{ opacity: 1, y: 0, delay: 400 }"
                >
                    Shop Now
                    <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </Link>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">
                    Featured Products
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <ProductCard 
                        v-for="product in featuredProducts"
                        :key="product.id"
                        :product="product"
                        @add-to-cart="addToCart"
                    />
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
.animate-gradient-shift {
    animation: gradientShift 15s ease infinite;
    background-size: 200% 200%;
}

.animate-gradient-text {
    animation: gradientText 8s ease infinite;
    background-size: 200% auto;
}

.animate-float {
    animation: float 20s infinite;
    opacity: 0.15;
}

.floating-element-1 {
    @apply bg-primary-500;
    width: 300px;
    height: 300px;
    top: 10%;
    left: 15%;
    animation-delay: -2s;
}

.floating-element-2 {
    @apply bg-secondary-500;
    width: 200px;
    height: 200px;
    top: 60%;
    right: 10%;
    animation-delay: -5s;
}

.floating-element-3 {
    @apply bg-primary-600;
    width: 150px;
    height: 150px;
    bottom: 20%;
    left: 25%;
    animation-delay: -7s;
}

.floating-element-4 {
    @apply bg-secondary-600;
    width: 100px;
    height: 100px;
    top: 30%;
    right: 25%;
    animation-delay: -11s;
}

.floating-element-5 {
    @apply bg-primary-500;
    width: 250px;
    height: 250px;
    bottom: 10%;
    right: 30%;
    animation-delay: -13s;
}

.floating-element-6 {
    @apply bg-secondary-500;
    width: 180px;
    height: 180px;
    top: 15%;
    right: 45%;
    animation-delay: -17s;
}

@keyframes gradientShift {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

@keyframes gradientText {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

@keyframes float {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(10%, 15%) rotate(5deg);
    }
    50% {
        transform: translate(-5%, -10%) rotate(-5deg);
    }
    75% {
        transform: translate(-15%, 5%) rotate(2deg);
    }
}
</style>
