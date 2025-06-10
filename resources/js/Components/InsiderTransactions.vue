<template>
  <div class="bg-gray-900 rounded-xl shadow-md p-6 mb-8">
    <h2 class="text-xl font-semibold text-white mb-4">Recent Insider Transactions: {{ symbol }}</h2>

    <div v-if="transactions.length" class="space-y-4">
      <div
        v-for="(tx, i) in transactions"
        :key="i"
        class="flex justify-between items-center bg-gray-800 px-4 py-3 rounded-lg border border-gray-700"
      >
        <!-- Left side: name and date -->
        <div class="flex items-center space-x-3">
          <div class="bg-gray-700 text-white text-sm px-2 py-1 rounded-md font-medium">
            {{ tx.name || 'Unknown' }}
          </div>
          <div class="text-sm text-gray-400">
            {{ formatDate(tx.transactionDate) }}
          </div>
        </div>

        <!-- Right side: transaction and shares -->
        <div class="flex items-center space-x-4">
          <div :class="tx.transactionCode === 'P' || tx.change > 0 ? 'text-green-400' : 'text-red-400'" class="font-semibold">
            {{ interpretCode(tx.transactionCode) }}
          </div>
          <div class="text-sm text-gray-300">
            {{ tx.share?.toLocaleString() }} shares @ ${{ parseFloat(tx.transactionPrice).toFixed(2) }}
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-gray-400 text-sm">
      No recent insider transactions available.
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  transactions: {
    type: Array,
    default: () => [],
  },
  symbol: {
    type: String,
    required: true,
  },
});

function formatDate(dateStr) {
  const date = new Date(dateStr);
  return date.toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
}

function interpretCode(code) {
  const map = {
    'P': 'Buy',
    'S': 'Sell',
    'M': 'Award',
    'A': 'Grant',
    'F': 'Tax Withheld',
  };
  return map[code] || code;
}
</script>
