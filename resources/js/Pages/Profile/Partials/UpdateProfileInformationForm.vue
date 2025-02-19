<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/Form/InputError.vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
                Name
            </label>
            <input
                id="name"
                type="text"
                v-model="form.name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                required
                autofocus
                autocomplete="name"
            />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                Email
            </label>
            <input
                id="email"
                type="email"
                v-model="form.email"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                required
                autocomplete="username"
            />
            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div v-if="props.mustVerifyEmail && user.email_verified_at === null">
            <p class="text-sm mt-2 text-gray-800">
                Your email address is unverified.
                <Link
                    :href="route('verification.send')"
                    method="post"
                    as="button"
                    class="text-sm text-primary-600 hover:text-primary-500 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                    Click here to re-send the verification email.
                </Link>
            </p>

            <div v-show="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                A new verification link has been sent to your email address.
            </div>
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
