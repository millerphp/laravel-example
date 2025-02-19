<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="flex items-center justify-center space-x-1">
        <template v-for="(link, key) in links" :key="key">
            <div
                v-if="link.url === null"
                class="px-4 py-2 text-sm text-gray-500 border rounded"
                :class="{ 'opacity-50 cursor-not-allowed': link.url === null }"
                v-html="link.label"
            />
            
            <Link
                v-else
                :href="link.url"
                class="px-4 py-2 text-sm border rounded"
                :class="{
                    'bg-primary-600 text-white': link.active,
                    'text-gray-700 hover:bg-gray-50': !link.active
                }"
                v-html="link.label"
            />
        </template>
    </div>
</template> 