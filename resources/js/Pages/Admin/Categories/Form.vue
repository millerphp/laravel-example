<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    category: {
        type: Object,
        default: () => ({
            name: '',
            description: '',
            parent_id: null,
            discount_percentage: null,
        }),
    },
    parentCategories: {
        type: Array,
        required: true,
    },
});

const form = ref({
    name: props.category.name,
    description: props.category.description,
    parent_id: props.category.parent_id,
    discount_percentage: props.category.discount_percentage,
});

function submit() {
    if (props.category.id) {
        router.put(route('admin.categories.update', props.category.id), form.value);
    } else {
        router.post(route('admin.categories.store'), form.value);
    }
}
</script>

<template>
    <AdminLayout>
        <div class="max-w-2xl mx-auto py-6">
            <div class="px-4 sm:px-6 md:px-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ category.id ? 'Edit Category' : 'Create Category' }}
                </h1>
            </div>

            <div class="mt-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input
                            type="text"
                            id="name"
                            v-model="form.name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            required
                        >
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        ></textarea>
                    </div>

                    <div>
                        <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Category</label>
                        <select
                            id="parent_id"
                            v-model="form.parent_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option :value="null">None (Top Level Category)</option>
                            <option
                                v-for="parent in parentCategories"
                                :key="parent.id"
                                :value="parent.id"
                            >
                                {{ parent.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="discount_percentage" class="block text-sm font-medium text-gray-700">
                            Discount Percentage
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input
                                type="number"
                                id="discount_percentage"
                                v-model="form.discount_percentage"
                                min="0"
                                max="100"
                                class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-primary-500 focus:ring-primary-500"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a
                            :href="route('admin.categories.index')"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
                        >
                            {{ category.id ? 'Update Category' : 'Create Category' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template> 