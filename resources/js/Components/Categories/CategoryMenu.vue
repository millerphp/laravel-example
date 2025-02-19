<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import CategoryMenuItems from './CategoryMenuItems.vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true
    }
});

console.log('Categories in menu:', props.categories);

const isNested = computed(() => {
    return props.categories.some(cat => cat.children?.length > 0);
});
</script>

<template>
    <div class="relative">
        <Menu v-slot="{ open }">
            <MenuButton 
                class="flex items-center space-x-1 text-gray-600 hover:text-gray-900"
                :class="{ 'text-primary-600': open }"
            >
                <span>Categories</span>
                <svg 
                    class="w-5 h-5 transition-transform duration-200"
                    :class="{ 'rotate-180': open }"
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </MenuButton>

            <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
                <MenuItems 
                    class="absolute z-50 mt-2 w-56 origin-top-right bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-[60]"
                >
                    <CategoryMenuItems :categories="categories" />
                </MenuItems>
            </transition>
        </Menu>
    </div>
</template> 