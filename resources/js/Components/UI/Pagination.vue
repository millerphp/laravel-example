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
    <div v-if="links.length > 3" class="flex justify-center mt-4">
        <div class="flex items-center gap-1">
            <template v-for="(link, key) in links" :key="key">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    v-html="link.label"
                    class="px-4 py-2 text-sm rounded-md"
                    :class="[
                        link.active 
                            ? 'bg-primary-600 text-white' 
                            : 'bg-white text-gray-700 hover:bg-gray-50'
                    ]"
                />
                <span
                    v-else
                    v-html="link.label"
                    class="px-4 py-2 text-sm text-gray-400 bg-gray-50 rounded-md"
                />
            </template>
        </div>
    </div>
</template> 