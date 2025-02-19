<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { TransitionRoot } from '@headlessui/vue';

const query = ref('');
const results = ref([]);
const isLoading = ref(false);
const showResults = ref(false);

const search = debounce(async () => {
    if (query.value.length < 2) {
        results.value = [];
        return;
    }

    isLoading.value = true;
    // Replace with actual API call
    await new Promise(resolve => setTimeout(resolve, 300));
    results.value = [/* sample results */];
    isLoading.value = false;
}, 300);

watch(query, () => {
    search();
});
</script>

<template>
    <div class="relative">
        <div class="relative">
            <input
                v-model="query"
                type="text"
                placeholder="Search products..."
                class="w-64 pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                @focus="showResults = true"
                @blur="setTimeout(() => showResults = false, 200)"
            />
            <svg 
                class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                xmlns="http://www.w3.org/2000/svg" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        <TransitionRoot
            :show="showResults && (results.length > 0 || isLoading)"
            enter="transition-all duration-300"
            enter-from="opacity-0 translate-y-4"
            enter-to="opacity-100 translate-y-0"
            leave="transition-all duration-200"
            leave-from="opacity-100 translate-y-0"
            leave-to="opacity-0 translate-y-4"
        >
            <div class="absolute top-full mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-200 max-h-96 overflow-auto">
                <div v-if="isLoading" class="p-4">
                    <div class="animate-pulse flex space-x-4">
                        <div class="flex-1 space-y-4 py-1">
                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-200 rounded"></div>
                            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <!-- Results will go here -->
                </div>
            </div>
        </TransitionRoot>
    </div>
</template> 