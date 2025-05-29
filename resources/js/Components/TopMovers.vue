<template>
  <section class="top-movers p-4 border border-gray-800 bg-default text-white shadow-sm rounded shadow mt-8">
    <h2 class="text-xl font-semibold mb-4">Top Movers</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Gainers -->
      <div>
        <h3 class="font-semibold mb-2 text-green-700">Top Gainers</h3>
        <ul>

          <li v-for="stock in gainers" :key="stock.symbol" class="flex justify-between py-1 border-b border-gray-200">
            <span>{{ stock.symbol }} - {{ stock.companyName }}</span>
            <span class="text-green-600 font-bold">{{ stock.changes }} ({{ stock.changesPercentage }})</span>
          </li>
        </ul>
      </div>

      <!-- Losers -->
      <div>
        <h3 class="font-semibold mb-2 text-red-700">Top Losers</h3>
        <ul>
          <li v-for="stock in losers" :key="stock.symbol" class="flex justify-between py-1 border-b border-gray-200">
            <span>{{ stock.symbol }} - {{ stock.companyName }}</span>
            <span class="text-red-600 font-bold">{{ stock.changes }} ({{ stock.changesPercentage }})</span>
          </li>
        </ul>
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  gainers: {
    type: Array,
    required: true
  },
  losers: {
    type: Array,
    required: true
  }
})

function formatChange(val) {
  if (val === null || val === undefined) return '-'
  return (val >= 0 ? '+' : '') + val?.toFixed(2)
}

function formatPercent(val) {
  if (val === null || val === undefined) return '-'
  return (val >= 0 ? '+' : '') + val?.toFixed(2) + '%'
}
</script>

<style scoped>
ul {
  list-style: none;
  padding: 0;
}
li {
  font-size: 0.9rem;
}
</style>
