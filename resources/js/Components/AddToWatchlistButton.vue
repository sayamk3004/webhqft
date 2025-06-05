<template>
  <button
    @click="addToWatchlist"
    class="flex items-center gap-2 px-3 py-2 text-sm rounded transition-all mt-2 mb-2"
    :class="isInWatchlist
      ? 'bg-gray-500 cursor-not-allowed text-white'
      : 'bg-green-600 hover:bg-green-700 text-white'"
  >
    <span>
      {{ isInWatchlist ? 'Already in Watchlist' : 'Add to Watchlist' }}
    </span>
    <svg
      xmlns="http://www.w3.org/2000/svg"
      class="h-5 w-5"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
      :fill="isInWatchlist ? 'red' : 'none'"
      :stroke="isInWatchlist ? 'red' : 'currentColor'"
    >
      <path
            :fill="isInWatchlist ? 'red' : 'none'"
        d="M12 21C12 21 4 13.5 4 8a4 4 0 018-4 4 4 0 018 4c0 5.5-8 13-8 13z"
      />
    </svg>
  </button>
</template>


<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toast } from 'vue3-toastify';

const props = defineProps({
  symbol: String,
  type: String,
});
const page = usePage();

const watchlistSymbols = computed(() =>
  (page.props.watchlists || []).map(item => item.symbol)
);
const isInWatchlist = computed(() =>
  watchlistSymbols.value.includes(props.symbol)
);


const addToWatchlist = async () => {
  const confirmed = confirm(`Add ${props.symbol} to your watchlist?`);
  if (!confirmed) return;

  try {
    const response = await axios.post('/watchlist', {
      symbol: props.symbol,
      type: props.type,
    });

    const message = response?.data?.message;

    if (message?.toLowerCase().includes('already')) {
      toast.info(message);
    } else {
      toast.success(message);
      await router.reload({ only: ['watchlists'] });

    }
  } catch (error) {
    toast.error('Something went wrong. Please try again.');
  }
};
</script>
