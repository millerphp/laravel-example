<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { useScrollPosition } from '@/Composables/useScrollPosition';
import { TransitionRoot } from '@headlessui/vue';
import SearchBar from '@/Components/Search/SearchBar.vue';
import CartDropdown from '@/Components/Cart/CartDropdown.vue';
import CategoryMenu from '@/Components/Categories/CategoryMenu.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useCartStore } from '@/Stores/useCartStore';
import { formatCurrency } from '@/utils';

const isScrolled = useScrollPosition();
const mobileMenuOpen = ref(false);
const user = computed(() => usePage().props.auth.user);
const cart = useCartStore();

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
});

</script>

<template>
    <header 
        class="fixed top-0 left-0 right-0 w-full z-50 transition-all duration-300"
        :class="[isScrolled ? 'bg-white shadow-lg' : 'bg-transparent']"
    >
        <div 
            class="h-1 bg-gradient-to-r from-primary-500 via-primary-400 to-secondary-500"
            :class="{ 'opacity-0': !isScrolled }"
        ></div>
        
        <nav class="container mx-auto px-4 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center space-x-8">
                <Link 
                    href="/" 
                    class="text-2xl font-bold text-gray-900 hover:text-primary-600 transition-colors"
                >
                    Store
                </Link>

                <!-- Categories Menu -->
                <CategoryMenu :categories="categories" class="hidden md:block" />
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <SearchBar />
                <div class="flex items-center">
                    <span class="mr-2 text-sm font-medium text-gray-700">
                        {{ formatCurrency(cart.finalTotal) }}
                    </span>
                    <CartDropdown />
                </div>
                
                <div v-if="user" class="relative">
                    <div class="flex items-center space-x-6">
                        <Link 
                            :href="route('profile.edit')"
                            class="text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors"
                        >
                            {{ user.name }}
                        </Link>
                        <Link 
                            :href="route('logout')" 
                            method="post" 
                            as="button"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all"
                        >
                            Logout
                        </Link>
                    </div>
                </div>
                <template v-else>
                    <div class="flex items-center space-x-4">
                        <Link 
                            :href="route('login')"
                            class="text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors"
                        >
                            Sign in
                        </Link>
                        <Link 
                            :href="route('register')"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all"
                        >
                            Create account
                        </Link>
                    </div>
                </template>
            </div>

            <!-- Mobile menu button -->
            <button 
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden"
            >
                <!-- Menu icon -->
            </button>
        </nav>

        <!-- Mobile menu -->
        <TransitionRoot 
            :show="mobileMenuOpen"
            enter="transition-transform duration-300"
            enter-from="-translate-x-full"
            enter-to="translate-x-0"
            leave="transition-transform duration-300"
            leave-from="translate-x-0"
            leave-to="-translate-x-full"
        >
            <!-- Mobile menu content -->
        </TransitionRoot>
    </header>
</template> 