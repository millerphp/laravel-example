<script setup>
import { ref } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import { 
    HomeIcon, 
    UsersIcon, 
    ShoppingCartIcon, 
    TagIcon, 
    CubeIcon, 
    FolderIcon 
} from '@heroicons/vue/24/outline/index.js';

const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: HomeIcon },
    { name: 'Products', href: '/admin/products', icon: CubeIcon },
    { name: 'Categories', href: '/admin/categories', icon: FolderIcon },
    { name: 'Users', href: '/admin/users', icon: UsersIcon },
    { name: 'Discounts', href: '/admin/discounts', icon: TagIcon },
    { name: 'Carts', href: '/admin/carts', icon: ShoppingCartIcon },
];

const page = usePage();
const isSidebarOpen = ref(true);
</script>

<template>
    <div v-if="page.props?.auth?.user" class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <div 
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-200"
            :class="{ '-translate-x-full': !isSidebarOpen }"
        >
            <div class="flex h-16 items-center justify-between px-4 border-b">
                <Link href="/" class="text-xl font-bold text-primary-600">
                    Store Admin
                </Link>
                <button 
                    @click="isSidebarOpen = false"
                    class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500"
                >
                    <span class="sr-only">Close sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="mt-4 px-2 space-y-1">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        $page.url.startsWith(item.href)
                            ? 'bg-primary-50 text-primary-600'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                        'group flex items-center px-2 py-2 text-sm font-medium rounded-md'
                    ]"
                >
                    <component
                        :is="item.icon"
                        :class="[
                            $page.url.startsWith(item.href)
                                ? 'text-primary-600'
                                : 'text-gray-400 group-hover:text-gray-500',
                            'mr-3 h-6 w-6'
                        ]"
                    />
                    {{ item.name }}
                </Link>
            </nav>
        </div>

        <!-- Main content -->
        <div :class="{ 'lg:pl-64': isSidebarOpen }">
            <!-- Top header -->
            <div class="sticky top-0 z-40 bg-white shadow-sm">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                    <button
                        @click="isSidebarOpen = !isSidebarOpen"
                        class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    >
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-700">
                            {{ page.props.auth.user.name }}
                        </span>
                        <Link 
                            :href="route('logout')" 
                            method="post" 
                            as="button"
                            class="text-sm text-gray-700 hover:text-gray-900"
                        >
                            Logout
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="py-6 px-4 sm:px-6 lg:px-8">
                <slot />
            </main>
        </div>
    </div>
    <div v-else class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-xl font-medium text-gray-900">Please log in to continue</h1>
            <Link 
                :href="route('login')" 
                class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
            >
                Go to Login
            </Link>
        </div>
    </div>
</template> 