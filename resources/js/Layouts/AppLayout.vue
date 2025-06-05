<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import CompareBar from '@/Components/CompareBar.vue';

const drawerOpen = ref(false);
const searchQuery = ref('');

const search = () => {
    if (searchQuery.value.trim()) {
        router.get(route('stocks.index'), { search: searchQuery.value });
    }
};
</script>

<template>
    <div class="min-h-screen bg-black text-white">
        <Sidebar :isOpen="drawerOpen"  @close="drawerOpen = false"  />
        <nav class="bg-gray-900 border-b border-gray-800 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button @click="drawerOpen = !drawerOpen" class="text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <Link :href="route('dashboard')" class="text-xl font-bold text-indigo-500">
                    WebHQ FT
                </Link>
            </div>

            <!-- Right side: Search -->
            <div class="relative w-full max-w-md">
                <input type="text" v-model="searchQuery" @keyup.enter="search"
                    class="w-full rounded border border-gray-600 bg-gray-800 py-2 pl-9 pr-3 placeholder-gray-400 focus:outline-none"
                    placeholder="Search stocks or ETFs..." />
                <button @click="search"
                    class="absolute right-3 top-2 text-gray-400 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="pt-4 pl-0 mx-auto transition-all duration-300">
            <slot />
             <CompareBar />
        </main>
    </div>
</template>
