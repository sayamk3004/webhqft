<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-black">
        <!-- Navigation -->
        <nav class="border border-gray-800 bg-default text-white shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="h-16">
                    <div class="flex justify-between">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('dashboard')">
                                <span class="text-xl font-bold text-indigo-600">WebHQ FT</span>
                            </Link>
                        </div>
                    <!-- Search Bar -->
                    <div class="flex items-center ml-6 flex-1">
                        <div class="relative flex-1">
                            <input
                                type="text"
                                placeholder="Search stocks or ETFs..."
                                class="w-100 grow rounded-sm border border-gray-600 py-2 pl-9 text-[1rem] placeholder-gray-400 focus:border-default focus:shadow-lg focus:outline-none focus:ring-0 tiny:pl-8 xs:pl-10 text-white md:py-2 w-full bg-secondary focus:bg-secondar"
                                @keyup.enter="search"
                                v-model="searchQuery"
                            />
                            <button
                                @click="search"
                                class="absolute right-2 top-2 text-gray-400 hover:text-gray-600"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <Link
                                :href="route('dashboard')"
                                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out"
                                :class="{ 'border-indigo-500 text-gray-900': $page.url === '/' }"
                            >
                                Dashboard
                            </Link>
                            <Link
                                :href="route('stocks.index')"
                                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out"
                                :class="{ 'border-indigo-500 text-gray-900': $page.url.startsWith('/stocks') }"
                            >
                                Stocks
                            </Link>
                            <Link
                                :href="route('etfs.index')"
                                class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out"
                                :class="{ 'border-indigo-500 text-gray-900': $page.url.startsWith('/etfs') }"
                            >
                                ETFs
                            </Link>
                        </div>
                    </div>

                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>

<script>
export default {
    data() {
        return {
            searchQuery: '',
        };
    },
    methods: {
        search() {
            if (this.searchQuery.trim()) {
                // Try stocks first
                this.$inertia.get(route('stocks.index'), { search: this.searchQuery });
            }
        },
    },
};
</script>