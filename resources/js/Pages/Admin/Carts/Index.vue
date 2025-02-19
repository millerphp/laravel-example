<script setup>
import { ref, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    carts: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            status: '',
            date_range: '',
        }),
    },
});

const search = ref(props.filters.search);
const status = ref(props.filters.status);
const dateRange = ref(props.filters.date_range);

const statusOptions = [
    { value: '', label: 'All Statuses' },
    { value: 'active', label: 'Active' },
    { value: 'completed', label: 'Completed' },
    { value: 'abandoned', label: 'Abandoned' },
];

const updateSearch = debounce((value) => {
    router.get(route('admin.carts.index'), {
        search: value,
        status: status.value,
        date_range: dateRange.value,
    }, { preserveState: true, preserveScroll: true });
}, 300);

watch(status, (value) => {
    router.get(route('admin.carts.index'), {
        search: search.value,
        status: value,
        date_range: dateRange.value,
    }, { preserveState: true, preserveScroll: true });
});

function getStatusBadgeClass(status) {
    switch (status) {
        case 'completed':
            return 'bg-green-100 text-green-800';
        case 'abandoned':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-blue-100 text-blue-800';
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
}
</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="sm:flex sm:items-center sm:justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Carts</h1>
                <Link 
                    :href="route('admin.carts.dashboard')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
                >
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    View Dashboard
                </Link>
            </div>

            <!-- Filters -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input
                            type="text"
                            id="search"
                            v-model="search"
                            @input="updateSearch($event.target.value)"
                            placeholder="Search by customer..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select
                            id="status"
                            v-model="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                            <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="date_range" class="block text-sm font-medium text-gray-700">Date Range</label>
                        <input
                            type="date"
                            id="date_range"
                            v-model="dateRange"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                        >
                    </div>
                </div>
            </div>

            <!-- Carts Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Items
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Activity
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="cart in carts.data" :key="cart.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div v-if="cart.user" class="text-sm">
                                    <div class="font-medium text-gray-900">{{ cart.user.name }}</div>
                                    <div class="text-gray-500">{{ cart.user.email }}</div>
                                </div>
                                <div v-else class="text-sm text-gray-500 italic">Guest</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ cart.items_count }} items
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ formatCurrency(cart.total_amount) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="getStatusBadgeClass(cart.status)"
                                >
                                    {{ cart.status.charAt(0).toUpperCase() + cart.status.slice(1) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ cart.last_activity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link 
                                    :href="route('admin.carts.show', cart.id)"
                                    class="text-primary-600 hover:text-primary-900"
                                >
                                    View Details
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                <Pagination :links="carts.links" />
            </div>
        </div>
    </AdminLayout>
</template> 