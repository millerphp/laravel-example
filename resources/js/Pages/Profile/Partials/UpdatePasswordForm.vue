<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/Form/InputError.vue';

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});
</script>

<template>
    <form @submit.prevent="form.put(route('password.update'))" class="mt-6 space-y-6">
        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700">
                Current Password
            </label>

            <input
                id="current_password"
                v-model="form.current_password"
                type="password"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                autocomplete="current-password"
            />

            <InputError :message="form.errors.current_password" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
                New Password
            </label>

            <input
                id="password"
                v-model="form.password"
                type="password"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                autocomplete="new-password"
            />

            <InputError :message="form.errors.password" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                Confirm Password
            </label>

            <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                autocomplete="new-password"
            />

            <InputError :message="form.errors.password_confirmation" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
            >
                <template v-if="form.processing">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                </template>
                <template v-else>
                    Save
                </template>
            </button>

            <p v-if="form.recentlySuccessful" class="text-sm text-green-600">Saved.</p>
        </div>
    </form>
</template>
