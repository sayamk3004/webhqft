<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto mt-8">
      <div class="bg-gray-800 text-white p-6 rounded-2xl shadow-lg">
        <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
          <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" stroke-width="2"
               viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 3h18v18H3V3z" />
            <path d="M8 16h8M8 12h4M8 8h4" />
          </svg>
          My Watchlist
        </h2>

        <ul v-if="watchlist.length" class="space-y-4">
          <li
            v-for="item in watchlist"
            :key="item.id"
            class="flex justify-between items-center bg-gray-700 rounded-xl px-5 py-4 shadow-sm hover:shadow-md transition-all duration-200"
          >
            <Link
              :href="item.type === 'etf' ? route('etfs.show', item.symbol) : route('stocks.show', item.symbol)"
              class="flex flex-col text-left hover:underline"
            >
              <span class="text-lg font-semibold tracking-wide">{{ item.symbol }}</span>
              <span class="text-xs text-gray-400 uppercase">{{ item.type }}</span>
            </Link>
            

            <button
              @click="remove(item)"
              class="text-red-500 hover:text-red-400 transition-colors text-sm font-medium"
            >
              Remove
            </button>
          </li>
        </ul>

        <p v-else class="text-gray-400 text-sm mt-4">
          You haven’t added any symbols to your watchlist yet.
        </p>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { toast } from 'vue3-toastify';

const watchlist = ref([]);

const fetchWatchlist = async () => {
  const response = await axios.get('/watchlist');
  watchlist.value = response.data;
};

const remove = async (item) => {
  const confirmed = confirm(`Remove ${item.symbol.toUpperCase()} from your watchlist?`);
  if (!confirmed) return;

  try {
    await axios.delete('/watchlist', {
      data: { symbol: item.symbol, type: item.type }
    });

    watchlist.value = watchlist.value.filter(w => w.symbol !== item.symbol || w.type !== item.type);
    toast.success(`${item.symbol.toUpperCase()} removed from watchlist.`);
  } catch (err) {
    toast.error('Failed to remove. Please try again.');
  }
};

onMounted(fetchWatchlist);
</script>
