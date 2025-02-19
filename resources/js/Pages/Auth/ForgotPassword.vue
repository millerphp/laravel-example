<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/Form/InputError.vue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <Link href="/" class="text-4xl font-bold text-gray-900">
                    Store
                </Link>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Reset your password
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Remember your password?
                    <Link 
                        :href="route('login')" 
                        class="font-medium text-primary-600 hover:text-primary-500"
                    >
                        Sign in
                    </Link>
                </p>
            </div>

            <!-- Info Box -->
            <div class="rounded-lg bg-gray-50 p-4">
                <p class="text-sm text-gray-700">
                    Forgot your password? No problem. Just let us know your email address and we'll email you a password reset link.
                </p>
            </div>

            <!-- Status Message -->
            <div v-if="status" class="rounded-lg bg-green-50 p-4">
                <p class="text-sm font-medium text-green-800">
                    {{ status }}
                </p>
            </div>

            <form @submit.prevent="submit" class="mt-8 space-y-6">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email address
                    </label>
                    <div class="mt-1">
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                            placeholder="Enter your email address"
                        />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    >
                        <template v-if="form.processing">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending link...
                        </template>
                        <template v-else>
                            Email Password Reset Link
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
