<template>
    <div class="fixed inset-y-0 left-0 z-40 w-64 bg-gray-900 text-white shadow-lg transform transition-transform duration-300 ease-in-out"
        :class="{ '-translate-x-full': !isOpen, 'translate-x-0': isOpen }">
        <div class="flex items-center justify-between p-4 border-b border-gray-700">
            <span class="text-xl font-bold text-indigo-500">WebHQ FT</span>
            <button @click="$emit('close')" class="text-gray-400 hover:text-white focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <nav class="p-4 space-y-3">
            <Link :href="route('dashboard')" class="block my-2 p-2 hover:text-indigo-400">Dashboard</Link>
            <Link :href="route('stocks.index')" class="block my-2 p-2 hover:text-indigo-400">Stocks</Link>
            <Link :href="route('etfs.index')" class="block my-2 p-2 hover:text-indigo-400">ETFs</Link>
            <Link :href="route('stocks.movers')" class="block my-2 p-2 hover:text-indigo-400">Top Movers</Link>
            <!-- <Link :href="route('stocks.news')" class="block my-2 p-2 hover:text-indigo-400">News</Link> -->
            <Link :href="route('watchlist.page')" class="block my-2 p-2 hover:text-indigo-400">My Watchlist</Link>
            <Link @click="goToCompare" class="block my-2 p-2 hover:text-indigo-400">My Compare List</Link>


            <hr class="border-gray-600 my-2" />
            <Link :href="route('profile.edit')" class="block my-2 p-2 hover:text-indigo-400">Profile</Link>
            <Link :href="route('logout')" method="post" as="button" class="block my-2 p-2 hover:text-red-400">Log Out</Link>
        </nav>
    </div>
</template>

<script setup>
import { Link,router } from '@inertiajs/vue3';
import { useCompareStore } from '@/Stores/compare';
defineProps(['isOpen']);
defineEmits(['close']);
const compare = useCompareStore();

const goToCompare = () => {
  const symbols = compare.symbols.join(',');
  router.get(route('compare.show', symbols));
};

</script>
