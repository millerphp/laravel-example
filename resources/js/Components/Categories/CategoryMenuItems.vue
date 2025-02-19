<script setup>
import { MenuItem } from '@headlessui/vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true
    },
    level: {
        type: Number,
        default: 0
    }
});

const hoveredCategory = ref(null);
const hoverTimeout = ref(null);

function handleMouseEnter(category) {
    if (hoverTimeout.value) {
        clearTimeout(hoverTimeout.value);
    }
    hoveredCategory.value = category;
}

function handleMouseLeave(event) {
    // Check if we're moving to a submenu
    const relatedTarget = event.relatedTarget;
    if (relatedTarget && relatedTarget.closest('.category-submenu')) {
        return;
    }

    hoverTimeout.value = setTimeout(() => {
        hoveredCategory.value = null;
    }, 150);
}

const menuPosition = computed(() => {
    return 'left-full ml-2';
});

const indentClass = computed(() => {
    return props.level > 0 ? `pl-${Math.min(props.level * 2 + 4, 8)}` : 'pl-4';
});
</script>

<template>
    <div class="py-1">
        <MenuItem 
            v-for="category in categories" 
            :key="category.id" 
            v-slot="{ active }"
            as="div"
            @mouseenter="handleMouseEnter(category)"
            @mouseleave="handleMouseLeave"
            class="relative group"
        >
            <Link
                :href="route('categories.show', category.slug)"
                class="flex items-center py-2 pr-4 text-sm"
                :class="[
                    active ? 'bg-primary-500 text-white' : 'text-gray-900',
                    indentClass
                ]"
            >
                {{ category.name }}
                <svg 
                    v-if="category.children?.length"
                    class="ml-auto h-5 w-5"
                    :class="{ 'text-white': active }"
                    viewBox="0 0 20 20" 
                    fill="currentColor"
                >
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </Link>

            <!-- Nested categories -->
            <div 
                v-if="category.children?.length && hoveredCategory?.id === category.id"
                class="absolute top-0 w-56 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 -right-2 category-submenu"
                :class="menuPosition"
                @mouseenter="handleMouseEnter(category)"
                @mouseleave="handleMouseLeave"
            >
                <!-- Add an invisible bridge to prevent hover gap -->
                <div class="absolute -left-2 top-0 h-full w-2"></div>
                
                <CategoryMenuItems 
                    :categories="category.children"
                    :level="level + 1"
                />
            </div>
        </MenuItem>
    </div>
</template> 