<script setup>
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AppLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="border border-gray-800 bg-default text-white shadow-sm p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <!-- <div
                    class="border border-gray-800 bg-default text-white shadow-sm p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div
                    class="border border-gray-800 bg-default text-white shadow-sm p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <DeleteUserForm class="max-w-xl" />
                </div> -->
            </div>
        </div>
    </AppLayout>
</template>
