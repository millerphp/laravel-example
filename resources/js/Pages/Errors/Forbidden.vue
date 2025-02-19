<script setup>
import { Head } from '@inertiajs/vue3';
import TheHeader from '@/Components/Layout/TheHeader.vue';

defineProps({
    status: {
        type: Number,
        required: true,
    },
    message: {
        type: String,
        required: false,
        default: 'Sorry, you don\'t have permission to access this page.'
    },
    categories: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Unauthorized" />

    <div class="min-h-screen bg-gray-50">
        <TheHeader :categories="categories" />

        <main class="container mx-auto px-4 py-8 pt-24">
            <div class="text-center">
                <h1 class="text-6xl font-bold text-primary-600 mb-4">403</h1>
                <h2 class="text-3xl font-semibold text-gray-900 mb-4">Access Denied</h2>
                <p class="text-lg text-gray-600 mb-8">
                    {{ message }}
                </p>
                <div class="flex justify-center space-x-4">
                    <Link
                        href="/"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all"
                    >
                        <svg 
                            class="w-5 h-5 mr-2" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path 
                                stroke-linecap="round" 
                                stroke-linejoin="round" 
                                stroke-width="2" 
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" 
                            />
                        </svg>
                        Return Home
                    </Link>
                    <button
                        @click="() => window.history.back()"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all"
                    >
                        <svg 
                            class="w-5 h-5 mr-2" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path 
                                stroke-linecap="round" 
                                stroke-linejoin="round" 
                                stroke-width="2" 
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" 
                            />
                        </svg>
                        Go Back
                    </button>
                </div>
            </div>
        </main>
    </div>
</template> 