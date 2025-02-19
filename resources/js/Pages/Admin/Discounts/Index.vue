<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    discounts: {
        type: Object,
        required: true,
    },
});

function confirmDelete(discount) {
    if (confirm(`Are you sure you want to delete "${discount.name}"?`)) {
        router.delete(route('admin.discounts.destroy', discount.id), {
            preserveScroll: true,
        });
    }
}

function toggleStatus(discount) {
    router.post(route('admin.discounts.toggle', discount.id), {}, {
        preserveScroll: true,
    });
}

function getStatusBadgeClass(discount) {
    if (!discount.is_active) return 'bg-gray-100 text-gray-800';
    if (discount.ends_at && new Date(discount.ends_at) < new Date()) return 'bg-red-100 text-red-800';
    if (discount.usage_limit && discount.usage_count >= discount.usage_limit) return 'bg-yellow-100 text-yellow-800';
    return 'bg-green-100 text-green-800';
}

function getStatusText(discount) {
    if (!discount.is_active) return 'Inactive';
    if (discount.ends_at && new Date(discount.ends_at) < new Date()) return 'Expired';
    if (discount.usage_limit && discount.usage_count >= discount.usage_limit) return 'Limit Reached';
    return 'Active';
}
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-900">Discounts</h1>
                <Link 
                    :href="route('admin.discounts.create')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
                >
                    Add Discount
                </Link>
            </div>

            <!-- Discounts Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Discount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Value
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usage
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Period
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="discount in discounts.data" :key="discount.id">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ discount.name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ discount.description }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ discount.percentage }}%
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ discount.type }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="getStatusBadgeClass(discount)"
                                >
                                    {{ getStatusText(discount) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ discount.usage_count }} used
                                </div>
                                <div v-if="discount.usage_limit" class="text-sm text-gray-500">
                                    Limit: {{ discount.usage_limit }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div v-if="discount.starts_at" class="text-sm text-gray-500">
                                    From: {{ discount.starts_at }}
                                </div>
                                <div v-if="discount.ends_at" class="text-sm text-gray-500">
                                    Until: {{ discount.ends_at }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <button
                                        @click="toggleStatus(discount)"
                                        class="text-gray-600 hover:text-gray-900"
                                    >
                                        {{ discount.is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <Link 
                                        :href="route('admin.discounts.edit', discount.id)"
                                        class="text-primary-600 hover:text-primary-900"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="confirmDelete(discount)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                <Pagination :links="discounts.links" />
            </div>
        </div>
    </AdminLayout>
</template> 