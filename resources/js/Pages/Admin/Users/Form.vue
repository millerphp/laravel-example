<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    user: {
        type: Object,
        default: () => ({
            name: '',
            email: '',
            is_admin: false,
        }),
    },
});

const form = ref({
    name: props.user.name,
    email: props.user.email,
    password: '',
    is_admin: props.user.is_admin,
});

function submit() {
    if (props.user.id) {
        router.put(route('admin.users.update', props.user.id), form.value);
    } else {
        router.post(route('admin.users.store'), form.value);
    }
}
</script>

<template>
    <AdminLayout>
        <div class="max-w-2xl mx-auto py-6">
            <div class="px-4 sm:px-6 md:px-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ user.id ? 'Edit User' : 'Create User' }}
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
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            type="email"
                            id="email"
                            v-model="form.email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            required
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            {{ user.id ? 'New Password (leave blank to keep current)' : 'Password' }}
                        </label>
                        <input
                            type="password"
                            id="password"
                            v-model="form.password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            :required="!user.id"
                        >
                    </div>

                    <div class="relative flex items-start">
                        <div class="flex items-center h-5">
                            <input
                                type="checkbox"
                                id="is_admin"
                                v-model="form.is_admin"
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                            >
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_admin" class="font-medium text-gray-700">Administrator</label>
                            <p class="text-gray-500">Grant full administrative access</p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a
                            :href="route('admin.users.index')"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Cancel
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
                        >
                            {{ user.id ? 'Update User' : 'Create User' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template> 