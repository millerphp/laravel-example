<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import draggable from 'vuedraggable';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
});

console.log('Categories received:', props.categories);

const categories = ref(props.categories);

// Debug logging
console.log('Draggable component:', draggable);
console.log('Categories ref:', categories.value);

function handleReorder() {
    // Update positions based on new order
    const updatedCategories = categories.value.map((category, index) => ({
        id: category.id,
        position: index,
    }));

    router.post(route('admin.categories.reorder'), {
        categories: updatedCategories
    }, {
        preserveScroll: true,
    });
}

function confirmDelete(category) {
    if (category.children_count > 0) {
        alert('Cannot delete category with subcategories.');
        return;
    }
    
    if (category.products_count > 0) {
        alert('Cannot delete category with products.');
        return;
    }

    if (confirm(`Are you sure you want to delete "${category.name}"?`)) {
        router.delete(route('admin.categories.destroy', category.id), {
            onSuccess: () => {
                // Success notification is handled by the flash message
            },
            onError: (errors) => {
                alert(errors.error || 'An error occurred while deleting the category.');
            },
        });
    }
}
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-900">Categories</h1>
                <Link 
                    :href="route('admin.categories.create')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
                >
                    Add Category
                </Link>
            </div>

            <!-- Categories Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Products
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Discounts
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Structure
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody v-if="!draggable" class="bg-white divide-y divide-gray-200">
                        <tr v-for="category in categories" :key="category.id">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ category.name }}
                                        </div>
                                        <div v-if="category.parent" class="text-sm text-gray-500">
                                            Parent: {{ category.parent.name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ category.products_count }} products
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ category.discounts_count }} active
                                </div>
                                <div v-if="category.discount_percentage" class="text-sm text-gray-500">
                                    {{ category.discount_percentage }}% off
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div v-if="category.children_count > 0">
                                    {{ category.children_count }} subcategories
                                </div>
                                <div v-else class="text-gray-400 italic">
                                    No subcategories
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <Link 
                                        :href="route('admin.categories.edit', category.id)"
                                        class="text-primary-600 hover:text-primary-900"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="confirmDelete(category)"
                                        class="text-red-600 hover:text-red-900"
                                        :disabled="category.children_count > 0 || category.products_count > 0"
                                        :class="{ 'opacity-50 cursor-not-allowed': category.children_count > 0 || category.products_count > 0 }"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <draggable
                        v-else
                        v-model="categories"
                        tag="tbody"
                        :animation="200"
                        ghost-class="opacity-50"
                        @end="handleReorder"
                        item-key="id"
                        class="bg-white divide-y divide-gray-200"
                    >
                        <template #item="{ element: category }">
                            <tr class="hover:bg-gray-50 transition-colors cursor-move">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <!-- Drag Handle -->
                                        <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ category.name }}
                                            </div>
                                            <div v-if="category.parent" class="text-sm text-gray-500">
                                                Parent: {{ category.parent.name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ category.products_count }} products
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ category.discounts_count }} active
                                    </div>
                                    <div v-if="category.discount_percentage" class="text-sm text-gray-500">
                                        {{ category.discount_percentage }}% off
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div v-if="category.children_count > 0">
                                        {{ category.children_count }} subcategories
                                    </div>
                                    <div v-else class="text-gray-400 italic">
                                        No subcategories
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-3">
                                        <Link 
                                            :href="route('admin.categories.edit', category.id)"
                                            class="text-primary-600 hover:text-primary-900"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            @click="confirmDelete(category)"
                                            class="text-red-600 hover:text-red-900"
                                            :disabled="category.children_count > 0 || category.products_count > 0"
                                            :class="{ 'opacity-50 cursor-not-allowed': category.children_count > 0 || category.products_count > 0 }"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </draggable>
                </table>
            </div>
        </div>
    </AdminLayout>
</template> 