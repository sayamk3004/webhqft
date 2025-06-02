<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="flex flex-col-reverse lg:flex-row min-h-screen items-center justify-center px-6 py-12 bg-gray-50 dark:bg-gray-900">
            <!-- Left side: welcome text + image -->
            <div class="w-full lg:w-1/2 flex flex-col items-center justify-center text-center px-4 mb-10 lg:mb-0">
                <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-4">Welcome Back 👋</h1>
                <p class="text-gray-500 dark:text-gray-300">
                    Please log in to access your dashboard and insights.
                </p>
                <img
                    src="/login.webp"
                    alt="Login illustration"
                    class="w-3/4 mt-8 hidden md:block"
                />
            </div>

            <div class="w-full lg:w-1/2 bg-white dark:bg-zinc-800 rounded-lg shadow-lg p-8">
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" value="Email" class="dark:text-gray-100" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full placeholder-gray-400 dark:placeholder-gray-500"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Password" class="dark:text-gray-100" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full placeholder-gray-400 dark:placeholder-gray-500"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-300">Remember me</span>
                        </label>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200 underline"
                        >
                            Forgot password?
                        </Link>
                    </div>

                    <PrimaryButton
                        class="w-full justify-center"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Log in
                    </PrimaryButton>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>
