<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    items: {
        type: Array,
        required: true,
        // Each item should have: { label: string, href?: string }
    }
});
</script>

<template>
    <nav aria-label="Breadcrumbs" class="flex">
        <ol class="flex items-center space-x-2">
            <li>
                <Link 
                    href="/"
                    class="text-gray-500 hover:text-gray-700"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    <span class="sr-only">Home</span>
                </Link>
            </li>

            <li 
                v-for="(item, index) in items" 
                :key="index"
                class="flex items-center"
            >
                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>

                <template v-if="item.href">
                    <Link 
                        :href="item.href"
                        class="ml-2 text-sm text-gray-500 hover:text-gray-700"
                    >
                        {{ item.label }}
                    </Link>
                </template>
                <template v-else>
                    <span class="ml-2 text-sm text-gray-900 font-medium">
                        {{ item.label }}
                    </span>
                </template>
            </li>
        </ol>
    </nav>
</template> 