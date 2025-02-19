<script setup>
import { Head } from '@inertiajs/vue3';
import TheHeader from '@/Components/Layout/TheHeader.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    categories: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Profile" />

    <TheHeader :categories="categories" />

    <div class="pt-24 pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow-sm rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <h2 class="text-lg font-medium text-gray-900">
                            Profile Information
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Update your account's profile information and email address.
                        </p>
                    </section>

                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="mt-6"
                    />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow-sm rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <h2 class="text-lg font-medium text-gray-900">
                            Update Password
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Ensure your account is using a long, random password to stay secure.
                        </p>
                    </section>

                    <UpdatePasswordForm class="mt-6" />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow-sm rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <h2 class="text-lg font-medium text-gray-900">
                            Delete Account
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Once your account is deleted, all of its resources and data will be permanently deleted.
                        </p>
                    </section>

                    <DeleteUserForm class="mt-6" />
                </div>
            </div>
        </div>
    </div>
</template>
