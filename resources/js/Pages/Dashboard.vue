<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    CurrencyDollarIcon, 
    ShoppingCartIcon, 
    UserGroupIcon, 
    CubeIcon,
    ArrowTrendingUpIcon,
    ArchiveBoxIcon,
    ClockIcon,
    CheckCircleIcon 
} from '@heroicons/vue/24/outline/index.js';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend);

defineProps({
    stats: {
        type: Object,
        required: true,
    },
    recentOrders: {
        type: Array,
        required: true,
    },
    orderStats: {
        type: Object,
        required: true,
    },
    salesChart: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Revenue -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <CurrencyDollarIcon class="h-6 w-6 text-gray-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Total Revenue
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ stats.revenue }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <ShoppingCartIcon class="h-6 w-6 text-gray-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Total Orders
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ stats.orders }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <UserGroupIcon class="h-6 w-6 text-gray-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Total Users
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ stats.users }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <CubeIcon class="h-6 w-6 text-gray-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Total Products
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ stats.products }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Average Order Value -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <ArrowTrendingUpIcon class="h-6 w-6 text-gray-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Average Order Value
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ orderStats.averageOrderValue }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Processing Orders -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <ClockIcon class="h-6 w-6 text-gray-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Processing Orders
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ orderStats.processingOrders }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Completed Orders -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <CheckCircleIcon class="h-6 w-6 text-gray-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Completed Orders
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ orderStats.completedOrders }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Products -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <ArchiveBoxIcon class="h-6 w-6 text-gray-400" />
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        Low Stock Products
                                    </dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ orderStats.lowStockProducts }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Chart -->
            <div class="bg-white p-6 rounded-lg shadow h-[400px]">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sales Overview</h3>
                <Line 
                    :data="salesChart.data"
                    :options="salesChart.options"
                />
            </div>

            <!-- Recent Orders -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    <div v-for="order in recentOrders" :key="order.id" class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <p class="text-sm font-medium text-primary-600 truncate">
                                    Order #{{ order.id }}
                                </p>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800': order.status === 'completed',
                                            'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                            'bg-blue-100 text-blue-800': order.status === 'processing'
                                        }"
                                    >
                                        {{ order.status }}
                                    </p>
                                </div>
                            </div>
                            <div class="ml-2 flex-shrink-0 flex items-center gap-4">
                                <span class="text-sm text-gray-500">{{ order.date }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ order.total }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
